<?php 
$currentPage = 'Register'; // declaring the current page as Property Listing

// Database connection setup
require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once '../functionality/roles.php';  // requiring the roles to check the roles of each user when ty log in
require_once 'connection.php'; // requiring the connection script to connect to the database


// Creating an if statement to check if the form data has been submitted using the POST method; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountType = $_POST["accountType"]; // checking if the accountType was posted 
    $email = $_POST["email"]; // checking if the email was posted 
    $password = $_POST["password"]; // checking if the password was posted 
    $confirmPassword = $_POST["confirm_password"]; // checking if the confirm_password was posted 

    $accountType = trim($accountType); // sanitazing the accountType variable 
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // sanitazing the email variable 

    $sql_check = "SELECT COUNT(*) AS count FROM users WHERE email = ?"; // sql_check for the SELECT statement to check if the email already exists 
    $stmt_check = $db_connection->prepare($sql_check); // using the stmt_check to prepare the SQL statement that will compile the SQL query 
    $stmt_check->bind_param("s", $email); // using stmt_check to to bind the parameters to avoid SQL injection
    $stmt_check->execute(); // using stmt_check to execute the prepared statement 
    $result_check = $stmt_check->get_result(); // using result_check to store the result in the function get_result()
    $row = $result_check->fetch_assoc(); // using row to fetch the result from the resultSelect
    $stmt_check->close(); // using to close the query SELECT

    if ($row['count'] > 0) { // if statement for when the email already exists 
        $registration_error = "Email address already registered. Please use a different email."; // display the error 
    } else { // otherwise provide one password 
        if (empty($password)) { // if its empty, provide one 
            $registration_error = "Please provide a password."; // display the error 
        } elseif (strlen($password) < 8) { // if is less tahn 8 digits, fix it 
            $registration_error = "Password must be at least 8 characters long.";// display the error 
        } elseif ($password !== $confirmPassword) { // if it is different than the confirm one, edit it 
            $registration_error = "Passwords do not match.";// display the error 
        } else { // otherwise hash the password  
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);//hashedPassword to hash the password for security purposes  

            $plainPassword = $password; // storing the plain password  

            $sql_insert = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)"; // sql_insert for the INSERT query 
            $stmt_insert = $db_connection->prepare($sql_insert); // using the stmtSelect to prepare the SQL statement that will compile the SQL query 

            if ($stmt_insert) { // if statement for stmt_insert

                $stmt_insert->bind_param("sss", $email, $hashedPassword, $accountType); // using stmt_insert to to bind the parameters to avoid SQL injection

                if ($stmt_insert->execute()) { // using stmt_insert to execute the prepared statement 
                    
                    $registration_success = "User registered successfully! An email will be sent to $email with further instructions."; // display the successfull message and the email 
                } else { // otherwise 
                    $registration_error = "Failed to register user. Please try again."; // display the error message 
                }
                $stmt_insert->close(); // close the INSERT query 
            } else { // otherwise it's a database error 
                $registration_error = "Database error. Please try again."; // display the error 
            }
        }
    }

    $db_connection->close(); // closing the database 
}
?>

<!DOCTYPE html> 
<html>
<head> 
    <!--  adding the title to register -->
    <title>Register</title> 
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

        /* Styling for elements with class "login-box" */
        .login-box {
            width: 400px; /* Setting the width of the login box */
            height: 400px; /* Setting the height of the login box */
            background-color: white; /* Background color for the login box */
            padding: 20px; /* Adding padding inside the login box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the login box */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
            justify-content: center; /* Vertically centering flex items */
        }

        /* Styling for input[type="email"], input[type="password"], .submit-button, select elements */
        input[type="email"],
        input[type="password"],
        .submit-button,
        select {
            width: 100%; /* Taking full width of the parent minus padding */
            padding: 10px; /* Adding padding inside input fields and buttons */
            margin-top: 20px; /* Adding margin space on top */
            border-radius: 5px; /* Adding rounded corners to input fields and buttons */
            border: 1px solid #ccc; /* Adding border to input fields and buttons */
            box-sizing: border-box; /* Including padding and border in the width calculation */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            background-color: #3498db; /* Background color for the submit button */
            color: white; /* Text color for the submit button */
            border: none; /* Removing border from the submit button */
            cursor: pointer; /* Changing cursor to pointer on hover */
        }

        /* Styling for button elements on hover */
        button:hover {
            background-color: #2980b9; /* Background color change on hover for buttons */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 1em; /* Adding spacing after the header */
        }

        /* Resetting margin and padding for the form to ensure alignment */
        form {
            width: 100%; /* Form takes full width of its parent */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking form elements vertically */
            align-items: center; /* Horizontally centering form elements */
            padding: 0; /* Resetting padding */
            margin: 0; /* Resetting margin */
        }

        /* Styling for anchor elements */
        a {
            text-decoration: none; /* Removing underline from links */
            color: #3498db; /* Setting link color */
        }

        /* Styling for p elements */
        p {
            margin-top: 20px; /* Adding margin space on top of paragraphs */
        }

    </style>
</head> 
<body>
<div class="login-box">
    <h2>Register</h2>
    <?php
    // displaying the success message in green along with the login page 
    if (isset($registration_success)) {
        echo '<p style="color: green;">' . $registration_success . '</p>';
        echo '<p >Login <a href="login.php">here</a></p>';
    }

    // displaying the error message in red 
    if (isset($registration_error)) {
        echo '<p style="color: red;">' . $registration_error . '</p>';
    }

    ?>

    <form id="registerForm" novalidate>
         <!-- displaying the type of accounts to register  -->
        <select name="accountType" required>
            <option value="">Select Account Type</option>
            <option value="Tenant">Tenant</option>
            <option value="Landlord">Landlord</option>
            <option value="Admin">Admin</option>
        </select>
         <!--  displaying the fielsd in the form to the user insert the data-->
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (Password must be at least 8 characters)" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="button" class="submit-button" onclick="submitForm()">Create Account</button>
    </form>
</div>

<!-- We decided to use Javascript for register.php page to handle the response from the server after sending the form data and updates the UI accordingly-->
<script>
    function submitForm() {
    // Get the form element with the id "registerForm"
    var form = document.getElementById("registerForm");
    
    // Create a new FormData object from the form data
    var formData = new FormData(form);
    
    // Create a new XMLHttpRequest object to send the form data asynchronously
    var xhr = new XMLHttpRequest();
    
    // Define a function to handle changes in the XMLHttpRequest ready state
    xhr.onreadystatechange = function () {
        // Check if the request is completed
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // Check if the request was successful (status code 200)
            if (xhr.status === 200) {
                // Successful response, update the UI with the response text
                document.querySelector(".login-box").innerHTML = xhr.responseText;
            } else {
                // An error occurred, log the status code to the console
                console.error("Error occurred:", xhr.status);
            }
        }
    };
    
    // Open a new POST request to the "register.php" endpoint asynchronously
    xhr.open("POST", "register.php", true);
    
    // Send the form data with the XMLHttpRequest
    xhr.send(formData);
}

</script>
 <!--  including the foother.php file -->
<?php include 'footer.php'; ?>
</body>
</html>

