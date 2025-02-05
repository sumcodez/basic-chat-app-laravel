<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #FDF6E3; /* Soft cream background */
            color: #333333; /* Dark gray text for contrast */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #FFEBE0; /* Soft peach */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar .app-name {
            font-size: 18px;
            font-weight: bold;
            color: #5C5C5C; /* Medium gray */
            margin-left: 40px;
        }

        .navbar .datetime {
            margin-right: 40px;
            font-size: 16px;
            color: #7D7D7D; /* Light gray */
        }

        .content {
            text-align: center;
        }

        .content h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #5A5A5A; /* Medium gray */
        }

        .content .buttons {
            margin-top: 20px;
        }

        .content .buttons button {
            background-color: #BDE0FE; /* Soft blue */
            color: #333333; /* Dark gray text */
            border: 2px solid #5A5A5A; /* Medium gray border */
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s;
        }

        .content .buttons button a {
            text-decoration: none;
            color: inherit;
        }

        .content .buttons button:hover {
            background-color: #FFD6E0; /* Soft pink on hover */
            color: #333333; /* Dark gray text */
        }

        .footer {
            position: absolute;
            bottom: 10px;
            right: 20px;
            font-size: 20px;
            color: #7D7D7D; /* Light gray */
        }

        /* Toast message */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #F9C6C9; /* Soft red for error */
            color: #333333; /* Dark gray text */
            padding: 15px;
            border-radius: 5px;
            font-size: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .toast.success {
            background-color: #CDEAC0; /* Soft green for success */
        }

        .toast.show {
            opacity: 1;
        }

        .subheading {
            font-size: 1.2rem;
            color: #7D7D7D; /* Light gray for subtle contrast */
            margin-top: -10px;
            margin-bottom: 20px;
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

    <div class="navbar">
        <div class="app-name">Many Chat</div>
        <div class="datetime" id="datetime"></div>
    </div>

    <div class="content">
        <h1>Connect. Share. Chat—Your World, Your Way!</h1>
        <p class="subheading">Stay connected with friends, family, and colleagues in one seamless platform.</p>
        <div class="buttons">
            <a href="{{ route('login') }}"><button>Sign In</button></a>
            <a href="{{ route('register') }}"><button>Get Started</button></a>
        </div>
    </div>

    <div class="footer">Developed by @Bitpastel</div>

    <script>
        function updateDateTime() {
            const datetimeElement = document.getElementById('datetime');
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            };
            datetimeElement.textContent = now.toLocaleDateString('en-US', options);
        }

        // Update the date and time every second
        setInterval(updateDateTime, 1000);

        // Initialize the date and time
        updateDateTime();
    </script>
</body>
</html>
