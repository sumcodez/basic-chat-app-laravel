<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up Page</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #fcefee, #e8f8f5);
            color: #444;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .signup-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .signup-container h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #6c63ff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #888;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            background: #fdfdfd;
            color: #333;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border: 2px solid #6c63ff;
        }

        .signup-btn {
            width: 100%;
            padding: 0.8rem;
            background: #6c63ff;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .signup-btn:hover {
            background: #5a54e8;
        }

        .footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .footer a {
            color: #6c63ff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .valid-message {
            color: #4caf50;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .input-error {
            border-color: #ff6b6b;
        }

        /* Toast message styles */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff6b6b; /* Red for error */
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: 9999;
        }

        .toast.success {
            background-color: #4caf50; /* Green for success */
        }

        .toast.show {
            opacity: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');


            emailInput.addEventListener('input', () => {
                const email = emailInput.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(email)) {
                    emailError.textContent = 'Please enter a valid email address.';
                    emailError.classList.remove('valid-message');
                    emailError.classList.add('error-message');
                } else {
                    emailError.textContent = 'Email looks good!';
                    emailError.classList.remove('error-message');
                    emailError.classList.add('valid-message');
                }
            });

            passwordInput.addEventListener('input', () => {
                const password = passwordInput.value;
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

                if (!passwordRegex.test(password)) {
                    passwordError.textContent = 'Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, and a number.';
                    passwordError.classList.remove('valid-message');
                    passwordError.classList.add('error-message');
                } else {
                    passwordError.textContent = 'Password looks good!';
                    passwordError.classList.remove('error-message');
                    passwordError.classList.add('valid-message');
                }
            });

            confirmPasswordInput.addEventListener('input', () => {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword !== password) {
                    confirmPasswordError.textContent = "Passwords didn't match.";
                    confirmPasswordError.classList.remove('valid-message');
                    confirmPasswordError.classList.add('error-message');
                } else {
                    confirmPasswordError.textContent = "Passwords match.";
                    confirmPasswordError.classList.remove('error-message');
                    confirmPasswordError.classList.add('valid-message');
                }
            });
        });
    </script>
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

            

    <div class="signup-container">
        <h1>Register Now</h1>
        <form method="POST" action="{{ route('users.sign-up') }}" id="frm">
            @csrf
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <div id="emailError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div id="passwordError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="password_confirmation" placeholder="Confirm your password" required>
                <div id="confirmPasswordError" class="error-message"></div>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        
        <div class="footer">
            <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        $(document).ready(function () {
            // Initialize form validation
            $("#frm").validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    last_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirmPassword: {
                        required: true,
                        minlength: 8
                    }

                },
                messages: {
                    first_name: {
                        required: "Please enter your first name.",
                    },
                    last_name: {
                        required: "Please enter your last name.",
                    },
                    email: {
                        required: "Please enter your email",
                    },
                    password: {
                        required: "Please enter your password",
                    },
                    confirmPassword: {
                        required: "Please enter confirm password",
                    }
                },
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass("error-message");
                    error.insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("input-error");
                },
                unhighlight: function (element) {
                    $(element).removeClass("input-error");
                }
            });
        });
    </script>


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
</body>
</html>
