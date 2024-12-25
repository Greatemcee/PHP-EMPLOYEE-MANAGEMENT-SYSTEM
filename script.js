const container = document.getElementById("container");
const registerbtn = document.getElementById("register");
const loginbtn = document.getElementById("login");
const forgotPasswordLink = document.getElementById("forgot-password-link");
const backToLoginButton = document.getElementById("back-to-login");
const showTermsLink = document.getElementById("show-terms");
const backToSignupButton = document.getElementById("back-to-signup");

// Ensure that the container stays on the signup view if there are errors or if redirected back to signup
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    // If the URL contains `signup=true`, activate the sign-up form
    if (urlParams.has('signup')) {
        container.classList.add("active"); // Keep the sign-up form active
    }
});

// When the register button is clicked
registerbtn.addEventListener("click", () => {
    container.classList.add("active");
});

// When the login button is clicked
loginbtn.addEventListener("click", () => {
    container.classList.remove("active");
});

// When the "Forgot Password" link is clicked
forgotPasswordLink.addEventListener("click", (e) => {
    e.preventDefault();  // Prevent default link behavior
    container.classList.add("forgot-active");  // Activate forgot-password view
});

// When the "Back to Login" button is clicked
backToLoginButton.addEventListener("click", () => {
    container.classList.remove("forgot-active");  // Return to login view
    container.classList.remove("terms-active");  // Ensure Terms and Conditions is also closed
});

// Show Terms and Conditions when the link is clicked
showTermsLink.addEventListener("click", (e) => {
    e.preventDefault();  // Prevent default link behavior
    container.classList.add("terms-active");  // Activate terms-and-conditions view
    container.classList.remove("forgot-active");  // Ensure forgot password view is closed
});

// Back to Signup from Terms and Conditions
backToSignupButton.addEventListener("click", (e) => {
    e.preventDefault();  // Prevent default button behavior
    container.classList.remove("terms-active");  // Return to signup view
});

// Validate password, confirm password, and terms and conditions before submission
document.getElementById("signupForm").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const errorMessage = document.getElementById("error-message");
    const termsCheckbox = document.getElementById("termsConditions");  // Get the Terms and Conditions checkbox

    // Check if the passwords match
    if (password !== confirmPassword) {
        event.preventDefault();  // Prevent form submission
        errorMessage.style.display = "block";  // Show error message
        errorMessage.textContent = "Passwords do not match!";

        // Stay in sign-up mode if there's an error
        container.classList.add("active");
    } 
    // Check if the Terms and Conditions checkbox is checked
    else if (!termsCheckbox.checked) {
        event.preventDefault();  // Prevent form submission
        errorMessage.style.display = "block";  // Show error message
        errorMessage.textContent = "You must agree to the Terms and Conditions!";
        container.classList.add("active");  // Stay in sign-up mode if there's an error
    } 
    else {
        errorMessage.style.display = "none";  // Hide error message if everything is valid
    }
});

// Toggle password visibility for both password fields in signup form
document.getElementById("show-password").addEventListener("change", function() {
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirm_password");

    if (this.checked) {
        passwordField.type = "text";
        confirmPasswordField.type = "text";
    } else {
        passwordField.type = "password";
        confirmPasswordField.type = "password";
    }
});

// Toggle password visibility for login form
document.getElementById("show-login-password").addEventListener("change", function() {
    const loginPasswordField = document.getElementById("login_password");

    if (this.checked) {
        loginPasswordField.type = "text";
    } else {
        loginPasswordField.type = "password";
    }
});