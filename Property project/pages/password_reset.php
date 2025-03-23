<?php 
ob_start(); // Start output buffering
// using the ob_start utput buffering to capture any output and prevent it from being sent to the browser

$currentPage = 'Password Reset'; // declaring the current page as Password Reset

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$email = ''; // initializing the email variable 
$oldPassword = ''; // initializing the oldPassword variable 
$newPassword = ''; // initializing the newPassword variable 
$confirmPassword = ''; // initializing the confirmPassword variable 
$errorMessage = ''; // initializing the errorMessage variable 
$successMessage = ''; // initializing the successMessage variable 

// Creating an if statement to check if the form data has been submitted using the POST method; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // using the mysqli_real_escape_string() function to to escape special characters in a String for use in a SQL statement.
    $email = mysqli_real_escape_string($db_connection, $_POST['email']);
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) { // if statement for when the newPassword is not equal to confirmPassword
        $errorMessage = 'New passwords do not match.'; // display the message and store in the variable 
    } elseif (strlen($newPassword) < 8) { // else if statament for when the password is less than 8 characters 
        $errorMessage = 'New password must be at least 8 characters long.'; // display the message and store in the variable 
    } else { // otherwise 

        $selectQuery = "SELECT * FROM users WHERE email = ?"; // selectQuery to check if the email exists in the database
        $stmtSelect = $db_connection->prepare($selectQuery); // using the stmtSelect to prepare the SQL statement that will compile the SQL query 
        $stmtSelect->bind_param('s', $email); // using stmtSelect to to bind the parameters to avoid SQL injection
        $stmtSelect->execute(); // using stmtSelect to execute the prepared statement 
        $resultSelect = $stmtSelect->get_result(); // using resultSelect to store the result in the function get_result()

        if ($resultSelect->num_rows > 0) { // if statement for verify the old password 
            $user = $resultSelect->fetch_assoc(); // using user to fetch the result from the resultSelect
            $storedPassword = $user['password']; // storing the password as an index of the user

            if (password_verify($oldPassword, $storedPassword)) { // if statement when the old password matches 
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); //hashedPassword to hash the password for security purposes  
                $updateQuery = "UPDATE users SET password = ? WHERE email = ?"; // update the password in the database 
                $stmtUpdate = $db_connection->prepare($updateQuery); // using the stmtUpdate to prepare the SQL statement that will compile the SQL query 
                $stmtUpdate->bind_param('ss', $hashedPassword, $email); // using stmtUpdate to to bind the parameters to avoid SQL injection

                if ($stmtUpdate->execute()) { // if statement to execute the query UPDATE 
                    $successMessage = 'Password reset successfully!'; // displaying the succesfull message 
                    $email = ''; // clear the input of email 
                    $oldPassword = ''; // clear the input of oldPassword 
                    $newPassword = ''; // clear the input of newPassword 
                    $confirmPassword = ''; // clear the input of confirmPassword 
                } else { // othewise display the errors 
                    $errorMessage = 'Failed to update password.';
                }
                $stmtUpdate->close(); // close the execution of the update query 
            } else { // otherwise the old password don't match 
                $errorMessage = 'Old password does not match.';
            }
        } else { // otherwise the email wasn't found 
            $errorMessage = 'Email not found. Please enter a valid email.';
        }

        $stmtSelect->close(); // closing the execution of the select query 
    }
}

$db_connection->close(); // closing the connection 

?>

<!DOCTYPE html> 
<html>
<head> 
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!--  adding the title to reset the password -->
    <title> Password Reset </title> 
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

        /* Styling for elements with class "reset-box" */
        .reset-box {
            width: 400px; /* Setting the width of the reset box */
            background-color: white; /* Background color for the reset box */
            padding: 20px; /* Adding padding inside the reset box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the reset box */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for input[type="text"] and input[type="password"] elements */
        input[type="text"],
        input[type="password"] {
            width: 100%; /* Taking full width of the parent minus padding */
            padding: 10px; /* Adding padding inside input fields */
            margin-top: 10px; /* Adding margin space on top */
            border-radius: 5px; /* Adding rounded corners to input fields */
            border: 1px solid #ccc; /* Adding border to input fields */
            box-sizing: border-box; /* Including padding and border in the width calculation */
            font-family: "Roboto", sans-serif; /* Ensuring consistent font */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Taking full width of the parent */
            padding: 10px; /* Adding padding inside the button */
            margin-top: 20px; /* Adding margin space on top */
            background-color: #3498db; /* Background color for the submit button */
            color: white; /* Text color for the submit button */
            border: none; /* Removing border from the submit button */
            border-radius: 5px; /* Adding rounded corners to the submit button */
            cursor: pointer; /* Changing cursor to pointer on hover */
        }

        /* Styling for submit button on hover */
        .submit-button:hover {
            background-color: #2980b9; /* Background color change on hover for the submit button */
        }

        /* Styling for elements with class "button" */
        .button {
            display: inline-block; /* Ensures the buttons behave like block elements with padding */
            padding: 10px 20px; /* Adding padding inside the button */
            background-color: #3498db; /* Blue background for buttons */
            color: white; /* White text color for buttons */
            text-decoration: none; /* Remove underline from links */
            border: none; /* Removing border from buttons */
            border-radius: 5px; /* Adding rounded corners to buttons */
            cursor: pointer; /* Changing cursor to pointer on hover */
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition for hover effects */
            text-align: center; /* Centering the text inside buttons */
        }

        /* Styling for button elements on hover */
        .button:hover {
            background-color: #2980b9; /* Darker blue background color on hover */
            transform: scale(1.05); /* Slightly larger size on hover */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Text color for h2 elements */
            margin-bottom: 20px; /* Adding margin space below h2 elements */
        }

    </style>
</head> 
<body>
      <!-- include the header in the page -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="reset-box">
    <h2>Reset Password</h2>
     <!-- displaying the error message in red -->
    <?php if ($errorMessage) : ?>
        <p style="color: red;"><?php echo htmlentities($errorMessage); ?></p>
    <?php endif; ?>

     <!--  displaying the success message in green-->
    <?php if ($successMessage) : ?>
        <p style="color: green;"><?php echo htmlentities($successMessage); ?></p>
        <a href="index.php" class="button">Back to Home</a></button>
    <!--  displaying the else statement -->
    <?php else : ?>
         <!--  creating the form for the reset of the password and implmenting sanitanization on the attributes -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="text" name="email" placeholder="Your Email" value="<?= htmlentities($email) ?>" required>
            <input type="password" name="oldPassword" placeholder="Old Password" required>
            <input type="password" name="newPassword" placeholder="New Password (Password must be at least 8 characters)" required>
            <input type="password" name="confirmPassword" placeholder="Confirm New Password" required>
            <button type="submit" class="submit-button" >Reset Password</button>
        </form>
    <?php endif; ?>
</div>
 <!--  including the footer.php file -->
<?php include 'footer.php'; ?>
</body>
</html>
<?php
ob_end_flush(); // Flush output buffer and send content to the browser
?>
