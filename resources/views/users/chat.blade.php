@extends('users.layouts.app')

@section('content')
<div class="container">
    <div class="sidebar">
        <div class="sidebar-header">
            @if($current_user->profile_picture)
            <img src="{{ asset($current_user->profile_picture) }}" alt="Current Profile Picture" class="profile-pic" id="profile-preview">
            @else
                <img src="{{ asset('default_image/defPic.jpg') }}" alt="Default Profile Picture" class="profile-pic" id="profile-preview">
            @endif
            <h2>Chats</h2>
        </div>                                                                                                                                                                                                                                                                                  
        <div class="search-bar">
            {{-- <input type="text" placeholder="Search or start new chat"> --}}
            <p style="text-align: center;">You are now connected with {{ $chat_user->first_name }} {{ $chat_user->last_name }}</p>
        </div>
        <div class="chats">

            @foreach ($connected_users as $chatUser)

                    <div class="chat" data-user-id="{{ $chatUser->id }}">
                        <img src="{{ asset($chatUser->profile_picture ?? 'default_image/defPic.jpg') }}" alt="Profile" class="chat-avatar">
                        <div class="chat-info">
                            <h4>{{ $chatUser->first_name }} {{ $chatUser->last_name }}</h4>
                            <p class="last-chat">{{ $chatUser->last_message }}</p>
                        </div>
                        <div>
                            <div class="chat-time">{{ $chatUser->last_message_time }}</div>
                            @if ($chatUser->unreadCount)
                                <div class="chat-time">Undead SMS {{ $chatUser->unreadCount }}</div>
                            @endif
                        </div>
                    </div>
                
            @endforeach

            {{-- <div class="chat">
                <img src="{{ asset($chat_user->profile_picture ?? 'default_image/defPic.jpg') }}" alt="Profile" class="chat-avatar">
                <div class="chat-info">
                    <h4>{{ $chat_user->first_name }} {{ $chat_user->last_name }}</h4>
                    <p class="last-chat"></p>
                </div>
                <div class="chat-time"></div>
            </div> --}}
            <!-- Add more chats as needed -->
        </div>
    </div>

    <div class="chat-area">
        <div class="chat-area-header">
            <img src="{{ asset($chat_user->profile_picture ?? 'default_image/defPic.jpg') }}" alt="Profile" class="chat-avatar">
            <h3>{{ $chat_user->first_name }} {{ $chat_user->last_name }}</h3>
            <button id="refresh-chat" class="refresh-button">
                <lord-icon
                    src="https://cdn.lordicon.com/mfblariy.json"
                    trigger="click"
                    style="width:35px;height:35px;">
                </lord-icon>
            </button>
        </div>
        <div class="chat-area-messages" id="chat-messages">
            
            <!-- Received Message -->
            {{-- <div class="message-container received">
                <div class="received-time"><span class="message-time">12:20</span></div>
                <div class="message received" id="msg-received">Hello!hhhdvghdcdsvchsgdv</div>
            </div> --}}

            <!-- Sent Message -->
            {{-- <div class="message-container sent">
                <div class="sent-time"><span class="message-time">12:24</span></div>
                <div class="message sent" id="msg-sent">
                    <span class="message-text">hello</span>
                    <i class="fa-solid fa-ellipsis-vertical" onclick="togglePopup(event)"></i>

                    <div class="popup-menu">
                        <div>Reply</div>
                        <div>Forward</div>
                        <div>Delete</div>
                    </div>
                </div>
            </div> --}}
            <!-- Add more messages as needed -->
        </div>

        <!-- Media Preview Container -->
        <div id="media-preview-container" style="position: relative; display: inline-block; margin-top: 10px;">
            <div id="media-preview" style="position: relative; display: inline-block;">
            </div>
            <!-- Close Icon -->
            <i id="clear-media" class="fa-solid fa-xmark" style="
                position: absolute;
                top: -4px;
                right: 515px;
                background: red;
                color: white;
                border-radius: 30%;
                padding: 5px;
                font-size: 14px;
                cursor: pointer;
                display: none;
                z-index:9999;
            "></i>
        </div>


        <div class="chat-input">
            {{-- <input type="text" placeholder="Type a message" id="message">
            <label for="attachment" class="attachment-btn" style="margin-top: 5px;"><i class="fa-solid fa-paperclip"></i></label>
            <input type="file" id="attachment" accept="image/*,video/*,.pdf" style="display: none;">
            <button id="send-message">Send</button> --}}

            <input type="text" placeholder="Type a message" id="message">
            <input type="file" id="media" accept="image/*,video/*,.pdf" style="display: none;">
            <button id="attach-file" style="background: none; color:black;"><i class="fa-solid fa-paperclip"></i></button>
            {{-- <div id="media-preview" style="margin-top: 10px;"></div> --}}
            <button id="send-message">Send</button>
        </div>
    </div>
</div>
<input type="hidden" id="current-user-id" value="{{ auth()->id() }}">
<input type="hidden" id="chat-user-id" value="{{ $chat_user->id }}">






<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        function fetchMessages() {
            const senderId = $('#current-user-id').val();
            const receiverId = $('#chat-user-id').val();
            const message = $('#message').val();

            const baseURL = "{{ url('/') }}";

            // Get latest 10 conversations
            $.ajax({
                url: `${baseURL}/api/messages_latest/${senderId}/${receiverId}`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("Response received:", response);

                    $('#chat-messages').empty();
                    
                    if (Array.isArray(response)) {
                        response.forEach(message => {

                            const messageClass = message.sender_id == senderId ? 'sent' : 'received';
                            const messageTime = new Date(message.created_at);
                            const formattedTime = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            

                            // Function to get the ordinal suffix for a given day
                            function getOrdinalSuffix(day) {
                                if (day > 3 && day < 21) return day + 'th'; // Special case for 11th to 13th
                                switch (day % 10) {
                                    case 1: return day + 'st';
                                    case 2: return day + 'nd';
                                    case 3: return day + 'rd';
                                    default: return day + 'th';
                                }
                            }

                            // Format the date as "11th Jun 2025"
                            const day = messageTime.getDate();
                            const month = messageTime.toLocaleString('default', { month: 'short' }); // Gets the abbreviated month name (e.g., "Jun")
                            const year = messageTime.getFullYear();
                            const formattedDate = `${getOrdinalSuffix(day)} ${month} ${year}`;



                            // Function to get the media HTML
                            function getMediaHTML(mediaUrl) {
                                const extension = mediaUrl.split('.').pop().toLowerCase();
                                
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                                    return `
                                        <div class="media-image-container" style="max-width: 200px; border-radius: 5px;">
                                            <img src="${mediaUrl}" alt="Uploaded Image" class="message-image" style="width: 100%; border-radius: 5px;">
                                        </div>
                                    `;
                                } else if (['mp4', 'mov', 'avi'].includes(extension)) {
                                    return `
                                        <div class="media-container" style="max-width: 200px; border-radius: 5px;">
                                            <video controls class="message-video" style="width: 100%; border-radius: 5px;">
                                                <source src="${mediaUrl}" type="video/${extension}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    `;
                                } else if (extension === 'pdf') {
                                    return `
                                        <div class="media-container">
                                            <a href="${mediaUrl}" target="_blank" class="message-pdf" style="color: blue; text-decoration: underline;">View PDF</a>
                                        </div>
                                    `;
                                }
                                return '';
                            }

                            // Add message buttons for the sender's own messages
                            let messageButtons = '';
                            if (message.sender_id == senderId) {
                                messageButtons = `
                                    <i class="fa-solid fa-ellipsis-vertical" onclick="togglePopup(event)"></i> <!-- 3-dot icon -->
                                    <div class="popup-menu">
                                        <div class="delete-message" data-message-id="${message.id}"><i class="fa-solid fa-trash"></i></div>
                                    </div>
                                `;
                            }

                            // Create the message HTML dynamically
                            const messageHTML = `
                                <div class="message-container ${messageClass}">
                                    <div class="${messageClass}-time">
                                        <span class="message-time">${formattedDate} ${formattedTime}</span>
                                    </div>
                                    <div class="message ${messageClass}" id="msg-${messageClass}">
                                        ${message.media_url ? getMediaHTML(message.media_url) : ''}
                                        <span class="message-text">${message.message}</span>
                                        ${messageButtons} <!-- Only show buttons for sender's own messages -->
                                    </div>
                                </div>
                            `;

                            // Append the message to the chat area
                            $('#chat-messages').append(messageHTML);

                        });

                        const lastResponse = response[response.length - 1];
                        console.log('Lastmessage', lastResponse);
                        const lastmessage = lastResponse.message;
                        const lastMessageTime = new Date(lastResponse.created_at);
                        const formattedTimeLstMag = lastMessageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        
                        //$('.last-chat').empty();
                        //$('.chat-time').empty();
                        
                        //$('.last-chat').text(lastmessage);
                        //$('.chat-time').text(formattedTimeLstMag);

                    } else {
                        console.error("Unexpected response format:", response);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseJSON);
                    //alert('Failed to fetch messages. Please try again.');
                }
            });
        }

        // Call the function (fetch last 10 chat) when page is refreshed 
        fetchMessages();

        // Refresh chat messages on button click (call the function for fetch last 10 chat)
        $('#refresh-chat').on('click', fetchMessages);
        
        // Handle Delete Button Click
        $(document).on('click', '.delete-message', function () {
            const messageDiv = $(this).closest('.message-container'); // Get the full message container
            const msg_id = $(this).data('message-id'); // message ID

            const baseURL = "{{ url('/') }}";

            if (!msg_id) {
                alert("Message ID not found. Cannot delete.");
                return;
            }

            // Confirm deletion
            if (confirm('Are you sure you want to delete this message?')) {
                // Add a loading state
                $(this).text('Deleting...').css('opacity', '0.6');

                // Send an AJAX request to delete the message from the backend
                $.ajax({
                    url: `${baseURL}/api/messages/${msg_id}`, // Correct URL
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Message deleted:', response);
                        
                        // Smoothly remove the message from the UI
                        messageDiv.fadeOut(300, function () {
                            $(this).remove();
                        });

                        fetchMessages();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseJSON);
                        alert(`Failed to delete message. Please try again. (Error: ${xhr.status})`);
                        
                        // Restore button text in case of failure
                        $('.delete-message').text('Delete').css('opacity', '1');
                    }
                });
            }
        });



        // Upload image, video, pdf
        // Hide send button initially
        $('#send-message').hide();
        $('#media-preview').hide();
        $('#media-preview-container').hide();

        // Show send button when user types a message
        $('#message').on('input', function() {
            toggleSendButton();
        });

        // Handle attach file button click
        $('#attach-file').on('click', function() {
            $('#media').click();
            console.log("Media input icon is clicked");
        });

        // Show send button when a file is selected
        $('#media').on('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                console.log("Got the file", file.type);

                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewHTML = '';

                    // Get file extension
                    const extension = file.name.split('.').pop().toLowerCase();

                    // Generate preview
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        previewHTML = `<img src="${e.target.result}" alt="Preview" class="media-preview" style="max-width: 200px; border-radius: 5px;">`;
                    } else if (['mp4', 'mov', 'avi'].includes(extension)) {
                        previewHTML = `<video controls class="media-preview" style="max-width: 200px; border-radius: 5px;">
                                            <source src="${e.target.result}" type="video/${extension}">
                                            Your browser does not support the video tag.
                                        </video>`;
                    } else if (extension === 'pdf') {
                        previewHTML = `<a href="${e.target.result}" target="_blank" class="media-preview" style="color: blue; text-decoration: underline;">View PDF</a>`;
                    }

                    // Show the preview
                    $('#media-preview').html(previewHTML);

                    $('#clear-media').show();
                };

                reader.readAsDataURL(file);  // Read file as Data URL for preview

                toggleSendButton();
            }
        });


        // Clear media input when close button is clicked
        $('#clear-media').on('click', function() {
            $('#send-message').hide();
            $('#media').val('');
            $('#media-preview').empty();
            $(this).hide(); // Hide close button
            $('#media-preview-container').hide();
        });

        // Send message and/or media when the send button is clicked
        $('#send-message').on('click', function() {
            $('#media-preview-container').hide();
            const message = $('#message').val();
            const file = $('#media')[0].files[0];

            const baseURL = "{{ url('/') }}";

            const senderId = $('#current-user-id').val();
            const receiverId = $('#chat-user-id').val();

            const formData = new FormData();
            
            if (file) {
                formData.append('media', file);
            }

            formData.append('message', message);
            formData.append('sender_id', senderId);
            formData.append('receiver_id', receiverId);

            $.ajax({
                url: `${baseURL}/upload_media`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log("Message and/or media sent successfully:", response);
                    
                    $('#media-preview').empty();
                    $('#media').val('');
                    $('#message').val('');
                    
                    $('#send-message').hide(); // Hide send button after sending
                    $('#clear-media').hide();
                    // $('#media-preview-container').hide();

                    fetchMessages(); // Fetch last 10 conversation
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                }
            });
        });

        // Function to toggle send button visibility
        function toggleSendButton() {
            const message = $('#message').val().trim();
            const fileSelected = $('#media')[0].files.length > 0;

            if (message || fileSelected) {
                if (fileSelected) {
                    $('#media-preview').show();
                    $('#media-preview-container').show();
                }
                $('#send-message').show();
            } else {
                $('#send-message').hide();
                $('#media-preview').hide();
                $('#media-preview-container').hide();
            }
        }
    });
</script>

<script>
    let offset = 0;
    const limit = 10;
    let isLoading = false;
    const chatContainer = document.getElementById("chat-messages");
    const senderId = document.getElementById('current-user-id').value;  // Logged-in user
    const receiverId = document.getElementById('chat-user-id').value;   // Chat partner

    const baseURL = "{{ url('/') }}";


    async function loadMessages() {
        if (isLoading) return;
        isLoading = true;

        try {
            const response = await fetch(`${baseURL}/fetch-messages?contact_id=${receiverId}&offset=${offset}`);
            const messages = await response.json();
            console.log("Messages for infinite scrolling: ", messages);
            
            if (messages.length > 0) {
                offset += limit; // Increase offset for next scroll
                prependMessages(messages);
            }
        } catch (error) {
            console.error("Error loading messages:", error);
        }

        isLoading = false;
    }
    

    // Function to prepend messages
    function prependMessages(messages) {
        const firstMessage = chatContainer.firstElementChild;

        messages.forEach(message => {
            const messageClass = message.sender_id == senderId ? 'sent' : 'received';
            const messageTime = new Date(message.created_at);
            const formattedTime = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Function to get the ordinal suffix for a given day
            function getOrdinalSuffix(day) {
                if (day > 3 && day < 21) return day + 'th'; // Special case for 11th to 13th
                switch (day % 10) {
                    case 1: return day + 'st';
                    case 2: return day + 'nd';
                    case 3: return day + 'rd';
                    default: return day + 'th';
                }
            }

            // Format the date as "11th Jun 2025"
            const day = messageTime.getDate();
            const month = messageTime.toLocaleString('default', { month: 'short' }); // Gets the abbreviated month name (e.g., "Jun")
            const year = messageTime.getFullYear();
            const formattedDate = `${getOrdinalSuffix(day)} ${month} ${year}`;

            // Function to get the media HTML
            function getMediaHTML(mediaUrl) {
                const extension = mediaUrl.split('.').pop().toLowerCase();
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    return `<img src="${mediaUrl}" alt="Uploaded Image" class="message-image" style="max-width: 200px; border-radius: 5px;">`;
                } else if (['mp4', 'mov', 'avi'].includes(extension)) {
                    return `<video controls class="message-video" style="max-width: 200px; border-radius: 5px;">
                                <source src="${mediaUrl}" type="video/${extension}">
                                Your browser does not support the video tag.
                            </video>`;
                } else if (extension === 'pdf') {
                    return `<a href="${mediaUrl}" target="_blank" class="message-pdf" style="color: blue; text-decoration: underline;">View PDF</a>`;
                }
                return '';
            }

            // Add message buttons for the sender's own messages
            let messageButtons = '';
            if (message.sender_id == senderId) {
                messageButtons = `
                    <i class="fa-solid fa-ellipsis-vertical" onclick="togglePopup(event)"></i> <!-- 3-dot icon -->
                    <div class="popup-menu">
                        <div class="delete-message" data-message-id="${message.id}"><i class="fa-solid fa-trash"></i></div>
                    </div>
                `;
            }

            // Create the message HTML dynamically
            const messageHTML = `
                <div class="message-container ${messageClass}">
                    <div class="${messageClass}-time">
                        <span class="message-time">${formattedDate} ${formattedTime}</span>
                    </div>
                    <div class="message ${messageClass}" id="msg-${messageClass}">
                        <span class="message-text">${message.message}</span>
                        ${message.media_url ? getMediaHTML(message.media_url) : ''}
                        ${messageButtons} <!-- Only show buttons for sender's own messages -->
                    </div>
                </div>
            `;

            // Prepend the message to the chat area
            chatContainer.insertAdjacentHTML('afterbegin', messageHTML);
        });

        // Maintain scroll position
        if (firstMessage) {
            firstMessage.scrollIntoView();
        }
    }

    // Detect scrolling to the top
    chatContainer.addEventListener("scroll", function () {
        if (chatContainer.scrollTop === 0) {
            loadMessages();
        }
    });


    //Load initial messages when the page loads
    document.addEventListener("DOMContentLoaded", () => {
        loadMessages();
    });


    // Refresh button
    //document.getElementById('refresh-chat').addEventListener('click', loadMessages);

</script>


<script>
    function togglePopup(event) {
        event.stopPropagation(); // Prevent immediate closing due to document click listener
        console.log("Toggle clicked");

        // Find the closest message container
        const messageElement = event.target.closest('.message');
        if (!messageElement) return;

        // Get the popup menu inside the message container
        const popup = messageElement.querySelector('.popup-menu');

        if (popup) {
            // Close all other popups before opening the clicked one
            document.querySelectorAll('.popup-menu').forEach(menu => {
                if (menu !== popup) {
                    menu.classList.remove('active');
                }
            });

            // Toggle the active class to show/hide the popup
            popup.classList.toggle('active');

            // Close popup when clicking outside
            document.addEventListener('click', function closePopup(e) {
                if (!popup.contains(e.target) && e.target !== event.target) {
                    popup.classList.remove('active');
                    document.removeEventListener('click', closePopup);
                }
            });
        }
    }
</script>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".chat").forEach(chat => {
            chat.addEventListener("click", function () {
                let userId = this.getAttribute("data-user-id");
                if (userId) {
                    window.location.href = `/chat/${userId}`;
                }
            });
        });
    });
</script>

@endsection


