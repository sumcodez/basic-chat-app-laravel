<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    {{-- <link rel="stylesheet" href="{{ asset('users_styles/style.css') }}"> --}}
</head>

<style>
    /* Toast message styles */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #FFB3BA; /* Pastel red for error */
        color: #333;
        padding: 15px;
        border-radius: 5px;
        font-size: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .toast.success {
        background-color: #B9FBC0; /* Pastel green for success */
    }

    .toast.show {
        opacity: 1;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #FFF8E1; /* Pastel yellow background */
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #FFFFFF;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #6C63FF; /* Pastel purple */
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #333;
    }

    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #DDD;
        border-radius: 5px;
        background-color: #F8F8FF; /* Light pastel blue */
    }

    .update-button {
        display: block;
        width: 50%;
        padding: 10px;
        margin-top: 20px;
        background-color: #A7C7E7; /* Pastel blue */
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-left: 150px;
    }

    .update-button:hover {
        background-color: #7EA8DE; /* Slightly darker pastel blue */
    }

    .cancel-container,
    .deactivate-container {
        text-align: center;
        margin-top: 20px;
    }

    .cancel-buttonn,
    .deactivate-buttonn {
        display: inline-block;
        background-color: #FFDAC1; /* Pastel peach */
        color: #333;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background-color 0.3s ease;
        width: 50%;
    }

    .cancel-buttonn:hover,
    .deactivate-buttonn:hover {
        background-color: #FFC4A3; /* Slightly darker pastel peach */
    }

    .cancel-buttonn a,
    .deactivate-buttonn a {
        color: #333;
        text-decoration: none;
    }

    .profile-picture-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .profile-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: contain;
        border: 2px solid #DDD;
    }

    .edit-button {
        position: absolute;
        margin-top: 140px;
        margin-left: 5px;
        background-color: #FFCCF9; /* Pastel pink */
        color: #333;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .edit-button:hover {
        background-color: #FFB3E4; /* Slightly darker pastel pink */
    }

    .back-to-dashboard .btn {
        display: inline-block;
        padding: 10px 15px;
        font-size: 14px;
        background-color: #D5AAFF; /* Pastel lavender */
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .back-to-dashboard .btn:hover {
        background-color: #B892FF; /* Slightly darker pastel lavender */
    }

    .back-to-dashboard {
        position: absolute;
        top: 20px;
        left: 20px;
    }
</style>


<body>
        <!-- Display success message -->
        @if(session('success'))
        <div id="success-toast" class="toast success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Display validation errors -->
        @if ($errors->any())
            <div id="error-toast" class="toast">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif


        <div class="back-to-dashboard">
            <a href="{{ route('users.all') }}" class="btn btn-primary">
                &larr; Back to Dashboard
            </a>
        </div>


    <div class="container">
        <h1>Manage Profile</h1>

        {{-- <label for="profile_picture" class="update-profile">Profile Picture:</label> --}}

        <!-- Display the current profile picture with an edit button -->
        <div class="profile-picture-container">
            @if($user->profile_picture)
                <img src="{{ asset($user->profile_picture) }}" alt="Current Profile Picture" class="profile-picture" id="profile-preview">
            @else
                <img src="{{ asset('default_image/defPic.jpg') }}" alt="Default Profile Picture" class="profile-picture" id="profile-preview">
            @endif
            <button type="button" class="edit-button" id="edit-picture-button">Edit</button>
        </div>
        
        <!-- File input for uploading a new profile picture (hidden by default) -->
        <form id="profile-pic-form" method="POST" action="{{ route('profile.update_pic') }}" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
            <button type="submit" id="save-picture-button" style="display: none;">Save</button>
        </form>

        <!-- Profile Form -->
        <form method="POST" action="{{ route('users.update_profile') }}">
            @csrf
            <label for="first_name" class="update-profile">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>

            <label for="last_name" class="update-profile">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>

            <label for="email" class="update-profile">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>

            <button type="submit" class="update-button">Update Profile</button>
            
        </form>

        <!-- Cancel Button -->
        {{-- <div class="cancel-container">
            <button class="cancel-buttonn"><a href="{{ route('events.all') }}">Back To Dashboard</a></button>
        </div> --}}

        <!-- Cancel Button -->
        <div class="deactivate-container">
            <button class="deactivate-buttonn"><a href="#">Deactivate My Account</a></button>
        </div>
    </div>



    <script>
        // Show toast message for success
        @if(session('success'))
            document.addEventListener("DOMContentLoaded", function() {
                const successToast = document.getElementById('success-toast');
                successToast.classList.add('show');

                // Hide the success toast message after 5 seconds
                setTimeout(function() {
                    successToast.classList.remove('show');
                }, 5000);
            });
        @endif

        // Show toast message for validation errors
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                const errorToast = document.getElementById('error-toast');
                errorToast.classList.add('show');

                // Hide the error toast message after 5 seconds
                setTimeout(function() {
                    errorToast.classList.remove('show');
                }, 5000);
            });
        @endif
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editButton = document.getElementById("edit-picture-button");
        const fileInput = document.getElementById("profile_picture");
        const form = document.getElementById("profile-pic-form");

        // Show file input when "Edit" button is clicked
        editButton.addEventListener("click", function () {
            fileInput.click();
        });

        // Automatically submit the form when a file is selected
        fileInput.addEventListener("change", function () {
            if (fileInput.files.length > 0) {
                form.style.display = "block"; // Show the form (optional)
                form.submit(); // Automatically submit the form
            }
        });
    });
</script>
</body>
</html>
