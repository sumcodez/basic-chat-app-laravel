<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Many Chat</title>
  {{-- <link rel="stylesheet" href="{{ asset('users_styles/style.css') }}"> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  background-color: #f7f6f2; /* Light pastel background */
  color: #4a4a4a; /* Soft dark text */
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

h1, h2, h3 {
  color: #4a4a4a;
}

/* Main Layout */
.main {
  display: flex;
  flex-direction: row;
  gap: 20px;
  padding: 20px;
}

/* Filters Section */
.filters {
  width: 20%;
  background-color: #fef6e4; /* Light pastel yellow */
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow */
  height: 85vh;
  position: sticky;
  top: 20px;
}

.filters h2 {
  margin-bottom: 15px;
  font-size: 1.5rem;
  color: #4a4a4a; /* Soft pastel pink */
}

.filters label {
  display: block;
  margin: 10px 0 5px;
  font-weight: bold;
}

.filters input,
.filters select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: none;
  border-radius: 5px;
  background-color: #e8f1f2; /* Light pastel blue */
  color: #4a4a4a;
}

.filters input[type="range"] {
  width: 100%;
  height: 5px;
  background: #ffb3c1; /* Pastel pink slider */
  border-radius: 5px;
  outline: none;
  margin-bottom: 15px;
}

.apply-filter-button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: none;
  border-radius: 5px;
  background-color: #ffb3c1; /* Soft pastel pink */
  color: #4a4a4a;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.apply-filter-button:hover {
  background-color: #ff9aa2; /* Slightly darker pink */
}

.clear-filter-button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: none;
  border-radius: 5px;
  background-color: #b2f2bb; /* Pastel green */
  color: #4a4a4a;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.clear-filter-button:hover {
  background-color: #a2d2a2; /* Slightly darker green */
}

/* Events Section */
.events {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.events h2 {
  margin-bottom: 20px;
  font-size: 1.8rem;
  color: #4a4a4a; /* Pastel pink */
}

/* Event Grid */
.event-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

/* Event Card */
.event-card {
  background-color: #e8f1f2; /* Light pastel blue */
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow */
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.event-card h3 {
  font-size: 1.2rem;
  margin-bottom: 10px;
}

.event-card p {
  margin-bottom: 10px;
}

.view-details-button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: #ffb3c1; /* Pastel pink */
  color: #4a4a4a;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.view-details-button:hover {
  background-color: #ff9aa2; /* Slightly darker pink */
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle depth */
}

.allMessages-button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: #ffb3c1; /* Pastel pink */
  color: #4a4a4a;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 124px;
  margin-left: 50px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .main {
    flex-direction: column;
  }

  .filters {
    width: 100%;
    margin-bottom: 20px;
  }

  .events {
    width: 100%;
  }
}




    /* Toast message styles */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #F44336; /* Red for error */
        color: white;
        padding: 15px;
        border-radius: 5px;
        font-size: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        z-index: 9999;
    }

    .toast.success {
        background-color: #4CAF50; /* Green for success */
    }

    .toast.show {
        opacity: 1;
    }


  /* Styling for the labels (e.g., Venue, Location, Date) */
  .event-label {
      font-weight: bold;
      font-size: 1.1rem; /* Slightly larger font size */
      color: #3498db; /* Highlighted color for labels */
  }

  /* Styling for the values (e.g., venue name, location, date) */
  .event-value {
      font-weight: normal;
      font-size: 1.1rem; /* Match label font size */
      color: #ffffff; /* White text for values */
  }
  /* Optional: Add spacing for better readability */
  .event-card p {
      margin: 8px 0; /* Adds space between each line */
  }




  .profile-pic {
        width: 40px; /* Set the desired size */
        height: 40px; /* Match the width for a perfect circle */
        border-radius: 50%; /* Makes the image circular */
        object-fit: contain; /* Ensures the image scales properly */
        border: 2px solid #fff; /* Optional: Add a border for better appearance */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Optional: Add a subtle shadow */
        vertical-align: middle; /* Aligns the image with the text */
        margin-right: 8px; /* Adds spacing between the image and text */
    }

    .dropdown {
        display: flex;
        align-items: center; /* Aligns the image and text vertically */
    }

    .dropbtn {
        margin-left: 4px; /* Adjust spacing between image and dropdown button */
    }

  /* Prevent dropdown from showing on hover */
.dropdown:hover .dropdown-content {
    display: none; /* Prevent showing on hover */
}

/* Dropdown button styles */
.dropdown .dropbtn {
    cursor: pointer; /* Indicate the button is clickable */
}

/* Dropdown content styles */
.dropdown-content {
    display: none; /* Initially hidden */
    position: absolute;
    background-color: #black;
    min-width: 160px;
    z-index: 1;
    border: 1px solid white;
    border-radius: 4px;
}

/* Show dropdown content when clicked */
.dropdown.show .dropdown-content {
    display: block;
}
  


/* Mobile Styles */
@media (max-width: 768px) {
    .navbar .nav-links {
        display: none; /* Hide navigation links by default */
        flex-direction: column; /* Stack the links vertically */
        position: absolute;
        top: 60px;
        right: 0;
        background-color: #333;
        width: 100%;
        padding: 10px;
    }

    .navbar .nav-links.show {
        display: flex; /* Show the links when the 'show' class is added */
    }

    .navbar .nav-links li {
        margin-right: 0;
        margin-bottom: 10px;
    }

    .navbar .menu-toggle {
        display: block; /* Show the hamburger icon on mobile */
        cursor: pointer;
    }

    .navbar .menu-toggle i {
        font-size: 2rem;
        color: white;
    }
  }

  .event-card .profile-pic-show{
    width: 70px; /* Set the desired size */
    height: 70px; /* Match the width for a perfect circle */
    border-radius: 50%; /* Makes the image circular */
    object-fit: cover; 
  }

  



  /* Navigation Bar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #FFEBE0;
  padding: 10px 20px;
  position: sticky;
  top: 0;
  z-index: 1;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* .navbar .logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #FF5722;
} */

.navbar .logo a {
  text-decoration: none;
  color: #4a4a4a;
  font-size: large;
  font-weight: bold;
}

.navbar .nav-links {
  display: flex;
  gap: 20px;
  list-style: none;
}

.navbar .nav-links li a {
  text-decoration: none;
  color: #4a4a4a;
  font-weight: bold;
  transition: color 0.3s;
  margin-right: 50px;
}

/* .navbar .nav-links li a:hover {
  color: #FF5722;
} */

.menu-toggle {
  display: none;
  font-size: 1.8rem;
  color: #FFFFFF;
  cursor: pointer;
}

@media (max-width: 768px) {
  .navbar.responsive .nav-links {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .navbar.responsive .nav-links li {
    margin: 10px 0;
  }

  .menu-toggle {
    display: block;
  }
}


/* Profile Dropdown Styling */
.dropdown {
  position: relative;
}

.dropdown .dropbtn {
  background-color: transparent;
  border: 2px solid black; /* Border added to the Profile button */
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 5px;
}

.dropdown .dropbtn:hover {
  background-color: #575757;
  color: whitesmoke;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  min-width: 145px; /* Default width */
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
  left: 1;
  top: 109%;
  width: 40%; /* Set dropdown width to be the same as the button */
  border: 1px solid white;
  border-radius: 4px;
  color: black;
  font-size: 11px;
}

/* .dropdown:hover .dropdown-content {
  display: block;
} */

.dropdown-content a {
  color: white;
  padding: 12px 16px;
  text-decoration: none;
}

.dropdown-content .logout-button{
  color: whitesmoke;
  text-decoration: none;
}

/* .dropdown-content a:hover {
  background-color: #575757;
} */

.accept-decline-container{
  display: flex;
  gap: 1rem;
  align-items: center;
  justify-content: center;
}


.blue-button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: blueviolet; /* Pastel pink */
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.red-button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: red; /* Pastel pink */
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}





.autocomplete-results {
    list-style: none;
    padding: 0;
    margin: 0;
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    background: white;
    border-radius: 5px;
    display: none;
}

.autocomplete-results li {
    padding: 10px;
    cursor: pointer;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.autocomplete-results li:hover {
    background: #f0f0f0;
}

.selected-user {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background: #f9f9f9;
    display: none;
}

  
  

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            display: flex;
            height: 90vh;
        }

        .sidebar {
            width: 30%;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .search-bar {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .search-bar input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }

        .chats {
            flex-grow: 1;
            overflow-y: auto;
        }

        .chat {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .chat:hover {
            background-color: #f9f9f9;
        }

        .chat img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .chat-info {
            flex-grow: 1;
        }

        .chat-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .chat-info p {
            font-size: 14px;
            color: #888;
        }

        .chat-time {
            font-size: 12px;
            color: #aaa;
        }

        .unread-count {
          font-size: 10px;
          color: white;
          border: 1px #11eb11;
          border-radius: 50%;
          background-color: #11eb11;
          width: 16px;
          text-align: center;
        }

        .chat-area {
            flex-grow: 1;
            width: 70%;
            background-color: #e5ddd5;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .chat-area-header {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #f0f2f5;
            border-bottom: 1px solid #ddd;
        }

        .chat-area-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .chat-area-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* .message {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            height: 60px;
            position: relative;
        } */

        /* .message.sent {
            align-self: flex-end;
            background-color: #dcf8c6;
        }

        .message.received {
          align-self: flex-start;
          background-color: #fff;
        } */

        .chat-input {
            padding: 10px;
            background-color: #f0f2f5;
            display: flex;
            gap: 10px;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }

        .chat-input button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background-color: #25d366;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .chat-input button:hover {
            background-color: #1db954;
        }

        .chat-placeholder {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          height: 100%;
          text-align: center;
          background-color: #f9f9f9;
          padding: 20px;
        }

        .start-chat-with-user {
          font-size: 18px;
          color: #555;
          font-weight: 500;
        }

        /* .message-time {
            font-size: 12px;
            color: gray;
            margin-right: 18px;
        } */

        .refresh-button {
            background-color: transparent; /* Remove background color */
            border: 1px solid black;
            border-radius: 5px;
            cursor: pointer; /* Add pointer cursor for better UX */
            padding: 0; /* Remove extra padding */
            display: flex;
            align-items: center;
            justify-content: center;
        }







        .message-container {
            display: flex;
            flex-direction: column;
            max-width: 80%;
            position: relative;
        }

        .message-time {
            font-size: 12px;
            color: gray;
            margin-bottom: 2px;
        }

        .sent {
            align-self: flex-end;
            /* background-color: #dcf8c6; */
            padding: 8px 12px;
            border-radius: 10px;
        }

        .received #msg-received {
            align-self: flex-start;
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
        }

        .sent-time {
            align-self: flex-end;
            margin-right: 10px;
        }

        .received-time {
            align-self: flex-start;
            margin-left: 10px;
        }

        #msg-sent {
          align-self: flex-end;
            background-color: #dcf8c6;
            padding: 8px 12px;
            border-radius: 10px;
        }

         /* edit delete */
         .message .fa-ellipsis-vertical {
            /* cursor: pointer;
            margin-left: 10px; */
            position: absolute;
            right: -25px;  /* Moves icon to the left outside the message */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: gray;
        }


        .message {
            position: relative; /* Ensures relative positioning for absolute child elements */
            padding: 8px 12px;
            border-radius: 10px;
            word-wrap: break-word;
            max-width: 100%;
        }


        .popup-menu {
            position: absolute;
            bottom: 43%; /* Position above the icon */
            right: -17px;
            /* background-color: #fff; */
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            /* flex-direction: column;
            width: 120px; */
            z-index: 1000;

            /* Hide by default */
            display: none;
            color: red;
        }

        .popup-menu.active {
            display: flex;
            gap: 10px;
        }



        #media-preview {
            max-width: 100%;
            max-height: 300px;
            margin: 10px 0;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
            /* border: ; */
            align-content: center;
            align-items: center;
            position: relative; /* Important for positioning the close icon */
            overflow: hidden;
        }

        .media-preview img,
        .media-preview video {
            max-width: 100%;
            max-height: 300px;
            display: block;
            margin: auto;
            border-radius: 5px;
        }


    </style>

</head>
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