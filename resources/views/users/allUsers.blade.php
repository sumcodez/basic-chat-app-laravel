@extends('users.layouts.app')

@section('content')
<div class="main">
    <!-- Filters Section -->
    <aside class="filters">
      <h2>Search Users</h2>
            <label for="search">Search By Username</label>
            <input type="text" name="title" id="search"  placeholder="Enter Username">
            <ul id="autocomplete-results" class="autocomplete-results"></ul>
            <div id="selected-user" class="selected-user"></div>
    </aside>

    <!-- Events Section -->
    <section class="events" id="events">
      <h2>All Users</h2>
      <div class="event-grid">
        <!-- Example Event Cards -->

      @foreach($users as $user)

        @php
            $connection = \App\Models\Connections::where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)->where('receiver_id', auth()->id());
            })->first();
        @endphp

        <div class="event-card" id="connection-{{ $connection ? $connection->id : 'new' }}" data-user-id="{{ $user->id }}">
            @if($user->profile_picture)
              <img src="{{ asset($user->profile_picture) }}" alt="Current Profile Picture" class="profile-pic" id="profile-preview">
            @else
              <img src="{{ asset('default_image/defPic.jpg') }}" alt="Default Profile Picture" class="profile-pic" id="profile-preview">
            @endif
              <h3>{{ $user->first_name }}</h3>
    
            {{-- @php
                $connection = \App\Models\Connections::where(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)->where('receiver_id', auth()->id());
                })->first();
            @endphp --}}
    
            <div>
                @if (!$connection)
                    <a href="{{ route('connect.send', $user->id) }}">
                        <button class="view-details-button">Connect</button>
                    </a>
                @elseif ($connection->status === 'pending')
                    @if ($connection->sender_id === auth()->id())
                        <button class="view-details-button" disabled>Connection Sent</button>
                    @else
                      <div class="accept-decline-container">
                        <form method="POST" action="{{ route('connect.accept', $connection->id) }}">
                            @csrf
                            <button class="blue-button">Accept</button>
                        </form>
                        {{-- <form method="POST" action="{{ route('connect.decline', $connection->id) }}">
                            @csrf --}}
                        <button class="red-button" id="decline-button" data-connection-id="{{ $connection->id }}">Decline</button>
                        {{-- </form> --}}
                      </div>
                    @endif
                @elseif ($connection->status === 'accepted')
                    <div class="accept-decline-container">
                      
                      <button class="blue-button"><a href="{{ route('chat.show', $user->id )}} ">Start Chat</a></button>

                      {{-- <form method="POST" action="{{ route('connect.end', $connection->id) }}">
                        @csrf --}}
                        <button class="red-button" id="end-chat" data-user-id="{{ $user->id }}" data-connection-id="{{ $connection->id }}">End Chat</button>
                      {{-- </form> --}}
                    </div>
                @endif
            </div>
        </div>
      @endforeach

      </div>
    </section>
  </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
      const searchInput = $('#search');
      const resultsList = $('#autocomplete-results');
      const selectedUserDiv = $('#selected-user');
      const allUsersSection = $('.event-grid'); // Select the All Users section
  
      // Debounce function
      const debounce = (func, delay) => {
          let debounceTimeout;
          return (...args) => {
              clearTimeout(debounceTimeout);
              debounceTimeout = setTimeout(() => func(...args), delay);
          };
      };
  
      // Fetch users using AJAX
      const fetchUsers = (query) => {
          if (!query) {
              resultsList.empty();
              return;
          }
  
          $.ajax({
              url: "{{ route('users.search') }}",
              method: "GET",
              data: { query },
              success: function (users) {
                  resultsList.empty();
                  users.forEach(user => {
                      if (!user || !user.first_name || !user.last_name) return;
                      const li = $(`<li>${user.first_name} ${user.last_name}</li>`);
                      li.data('user', user);
                      li.on('click', function () {
                          selectUser($(this).data('user'));
                      });
                      resultsList.append(li);
                  });
              },
              error: function () {
                  console.error('Error fetching users');
              }
          });
      };
  
      // Select user and update UI
      const selectUser = (user) => {
          resultsList.empty();
          resultsList.hide();
  
          // Check if the user is already in the All Users section
          if (!$(`#user-${user.id}`).length) {
              // Create a new user card and append it to the All Users section
              const userCard = `
                  <div class="event-card" id="user-${user.id}" data-added-by-search="true">
                      <img src="${user.profile_picture || '/default_image/defPic.jpg'}" alt="Profile Picture" class="profile-pic">
                      <h3>${user.first_name} ${user.last_name}</h3>
                      <div>
                          <a href="{{ route('connect.send', '') }}/${user.id}">
                              <button class="view-details-button">Connect</button>
                          </a>
                      </div>
                  </div>
              `;
              allUsersSection.prepend(userCard);
          }
  
          // Show the selected user in the dedicated section
          selectedUserDiv.html(`
              <img src="${user.profile_picture || '/default_image/defPic.jpg'}" alt="Profile Picture" width="50">
              <p>${user.first_name} ${user.last_name}</p>
          `);
  
          selectedUserDiv.show();
      };
  
      // Clear dynamically added users
      const clearSearchResults = () => {
          allUsersSection.find('[data-added-by-search="true"]').remove();
          selectedUserDiv.hide();
      };
  
      // Attach input event listener with debounce
      searchInput.on('input', debounce(function () {
          const query = searchInput.val();
          if (query) {
              resultsList.show();
              fetchUsers(query);
              selectedUserDiv.hide();
          } else {
              resultsList.empty();
              resultsList.hide();
              clearSearchResults(); // Remove dynamically added users when input is cleared
          }
      }, 300));
  });
</script>


<script>
    $(document).ready(function() {
        // When the decline button is clicked
        $('#decline-button').on('click', function() {
            const connectionId = $(this).data('connection-id'); // Get connection ID from the data attribute
            const baseURL = "{{ url('/') }}";
            
            // Make the AJAX POST request to decline the connection
            $.ajax({
                url: `${baseURL}/connect/decline/${connectionId}`,  // The URL for the decline action
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                    connection_id: connectionId
                },
                success: function(response) {
                    // If the connection is declined successfully, update the UI
                    //alert('Connection declined successfully!');
                    console.log("Connection Id is ",connectionId);
                    // Optionally, you can remove the connection item from the DOM or update it.
                    const connectionCard = $(`#connection-${connectionId}`);
                    
                    // Remove the decline/accept buttons
                    connectionCard.find('.accept-decline-container').remove();
                    
                    // Add the "Connect" button back
                    connectionCard.find('div').prepend(`
                        <a href="/connect/send/${connectionCard.data('user-id')}">
                            <button class="view-details-button">Connect</button>
                        </a>
                    `);
                },
                error: function(xhr) {
                    // Handle any errors that may occur
                    alert('An error occurred while declining the connection.');
                }
            });
        });


        // End Chat (Delete all chat between two users)
        $('#end-chat').on('click', function() {
            const userId = $(this).data('user-id');
            const connectionId = $(this).data('connection-id');
            const baseURL = "{{ url('/') }}";

            $.ajax({
                url: `${baseURL}/deleteChat/${userId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    chatUserId: userId
                },
                success: function (response) {
                    console.log("Messages deleted successfully for user: ", userId);

                    // Optionally, you can remove the connection item from the DOM or update it.
                    const connectionCard = $(`#connection-${connectionId}`);
                    
                    // Remove the decline/accept buttons
                    connectionCard.find('.accept-decline-container').remove();
                    
                    // Add the "Connect" button back
                    connectionCard.find('div').prepend(`
                        <a href="/connect/send/${connectionCard.data('user-id')}">
                            <button class="view-details-button">Connect</button>
                        </a>
                    `);
                },
                error: function (xhr) {
                    alert("An error occurred while deleting all messages");
                }
            })
        })
    });
</script>

  
  
