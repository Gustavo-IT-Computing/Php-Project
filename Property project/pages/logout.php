<?php

require_once 'cookies.php'; // including the cookies.php file

clearAllCookies(); // Calling the function to clear all cookies 

$_SESSION = array(); // unseting all session variables

session_destroy(); // destroying the session 
header("Location: login.php"); // redirect to login.php page 
exit; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to Logout-->
    <title>Logout</title>
    <style>
    /* Styling for the body element */
    body {
        background-color: rgb(244, 244, 244); /* Setting the background color for the entire page */
        font-family: "Roboto", sans-serif; /* Choosing the font family for text */
        margin: 0; /* Resetting margin to ensure no default spacing */
        padding: 0; /* Resetting padding to ensure no default spacing */
        display: flex; /* Using flexbox for layout */
        justify-content: center; /* Horizontally centering content */
        align-items: center; /* Vertically centering content */
        flex-direction: column; /* Stacking flex items vertically */
        height: 100vh; /* Full viewport height */
    }

    /* Styling for elements with class "message" */
    .message {
        width: 400px; /* Setting the width of the message box */
        background-color: white; /* Background color for the message box */
        padding: 20px; /* Adding padding inside the message box */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
        border-radius: 8px; /* Adding rounded corners to the message box */
        display: flex; /* Using flexbox for layout */
        flex-direction: column; /* Stacking flex items vertically */
        align-items: center; /* Horizontally centering flex items */
        justify-content: space-around; /* Adjusted for better spacing */
    }

    /* Styling for elements with class "button" */
    .button {
        display: inline-block; /* Ensures the buttons behave like block elements with padding */
        background-color: #3498db; /* Background color for buttons */
        color: white; /* Text color for buttons */
        padding: 10px 20px; /* Uniform padding for better button size */
        font-size: 16px; /* Font size for buttons */
        border: none; /* Removing border from buttons */
        cursor: pointer; /* Changing cursor to pointer on hover */
        border-radius: 4px; /* Adding rounded corners to buttons */
        text-decoration: none; /* Remove underline from links */
        margin: 10px; /* Spacing between buttons */
        text-align: center; /* Center the text inside buttons */
    }

    /* Styling for button elements on hover */
    .button:hover {
        background-color: #2980b9; /* Subtle hover effect */
    }
    </style>
</head>
<body>
    <div class="message">
         <!-- displaying the successfull message of the logout with a link to the login and homepage -->
        <p>You have been successfully logged out of your account.</p>
        <a href="login.php" class="button">Login</a>
        <a href="index.php" class="button">Home</a>
    </div>
     <!-- including the footer -->
    <?php include 'footer.php'; ?>
</body>
</html>