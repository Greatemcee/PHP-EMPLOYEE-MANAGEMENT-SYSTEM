<?php
session_start(); // Start the session at the very top, to capture any session data such as error messages
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet" />
    <title>Login Page</title>
</head>

<body>
    <div class="container" id="container">
    
        <!-- Sign Up Form -->
        <div class="sign-up">
            <form id="signupForm" action="signup.php" method="POST">
                <h1>Create Account</h1>
                <input type="text" name="last_name" placeholder="Last Name" required />
                <input type="text" name="first_name" placeholder="First Name" required />
                <input type="text" name="username" placeholder="Username" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="text" name="address" placeholder="Address" required />
                <input type="date" name="dob" placeholder="Date of Birth" required />

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="nonbinary">Non-binary</option>
                    <option value="prefer_not_to_say">Prefer not to say</option>
                    <option value="other">Other</option>
                </select>

                <input type="password" name="password" id="password" placeholder="Create Password" required />
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />

                <div class="show-password-container">
                    <input type="checkbox" id="show-password" />
                    <label for="show-password">Show Password</label>
                </div>
                <div class="show-password-container">
                    <input type="checkbox" id="termsConditions" />
                    <label for="termsConditions">
                        I agree to the <a href="#" id="show-terms" style= "color: blue; text-decoration: underline;">Terms and Conditions</a>
                    </label>
                </div>
            
                <button type="submit">Sign Up</button>
                <!-- Error message for email/username or password mismatch -->
                <p id="error-message" style="color: red;">
                    <?php
                    if (isset($_SESSION['signup_error_message'])) {
                        echo $_SESSION['signup_error_message'];
                        unset($_SESSION['signup_error_message']); // Clear the message after displaying it
                    }
                    ?>
                </p>


                <?php if (isset($_SESSION['success_message'])): ?>
                    <p style="color: green;"><?php echo $_SESSION['success_message']; ?></p>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
            </form>
        </div>

        <!-- Sign In Form with Error Message Display -->
        <div class="sign-in">
            <form action="login.php" method="POST">
        
                <h1>Sign In</h1>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" id="login_password" placeholder="Password" required />

                <!-- Show Password Checkbox -->
                <div class="show-password-container">
                    <input type="checkbox" id="show-login-password" />
                    <label for="show-login-password">Show Password</label>
                </div>

                <a href="#" id="forgot-password-link">Forgot password?</a>
                <button type="submit">Sign In</button>
                <p id="error-message" style="color: red;">
                    <?php
                    if (isset($_SESSION['login_error_message'])) {
                        echo $_SESSION['login_error_message'];
                        unset($_SESSION['login_error_message']); // Clear the message after displaying it
                    }
                    ?>
                </p>

            </form>
        </div>

        <!-- Forgot Password Section -->
        <div class="forgot-password">
            <form action="password_reset_request.php" method="POST">
                <h2>Forgot Password</h2>
                <input type="email" name="email" placeholder="Enter your email" required />
                <button type="submit">Send Reset Link</button>
                <button id="back-to-login">Back to Login</button>
            </form>
        </div>

        <!-- Terms and Conditions Section -->
        <<div class="terms-and-conditions">
            <h2>Terms and Conditions</h2>
            <p>1. User Access Eligibility: Only authorized employees with valid login credentials may use the EMS.
                Account Responsibility: Users are responsible for the confidentiality of their login credentials and all activities under their account.
                Access Restrictions: Use the EMS only for job-related tasks. Unauthorized access or misuse will result in disciplinary action.</p>
            <p>2. System Usage Permitted Use: Use the EMS only for work-related purposes in line with company policies.
                 Prohibited Use: Do not engage in illegal activities, share harmful content, or attempt unauthorized access.</p>
            <p>3. Data Privacy & Security Data Use: The EMS collects and processes employee data for work-related purposes. By using the EMS, you consent to this data collection.
                Confidentiality: Maintain the confidentiality of all data accessed through the EMS.</p>
            <p>4. Intellectual Property All content in the EMS is owned by OKX lang ako and is protected by copyright. Users may only access the system for authorized purposes.
            <p>5. Monitoring The company may monitor EMS activity to ensure compliance with these terms and to protect the system..</p>
            <button id="back-to-signup">Back to Signup</button>
        </div>
                

        <!-- Toggle between sign-up and sign-in -->
        <div class="toogle-container">
            <div class="toogle">
                <div class="toogle-panel toogle-left">
                    <h1>Welcome User!</h1>
                    <p>If you already have an account</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toogle-panel toogle-right">
                    <h1>Hello, User!</h1>
                    <p>If you don't have an account</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>