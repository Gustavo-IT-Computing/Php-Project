<?php
$currentPage = 'Login'; // declaring the current page as Login

// Database connection setup
require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once '../functionality/roles.php'; // requiring the roles to check the roles of each user when ty log in
require_once 'connection.php'; // requiring the connection script to connect to the database

$login_error = ''; // creating an empty string called login_error to store the error messages when the form is incorrect filled

// Creating an if statement to check if the form data has been submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"]; // checking if the email was posted 
    $password = $_POST["password"]; // checking if the password was posted 
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // validating the email 

    
    $sql_select = "SELECT * FROM users WHERE email = ?"; // creating the sql_select for the select query 
    $stmt_select = $db_connection->prepare($sql_select); // using the stmt_select to prepare the SQL statement that will compile the SQL query 
    $stmt_select->bind_param("s", $email); // using stmt_select to to bind the parameters to avoid SQL injection
    $stmt_select->execute(); // using stmt_select to execute the prepared statement 
    $result_select = $stmt_select->get_result(); // using result_select to select the method get_result

    if ($result_select->num_rows > 0) { // check if the user is found and verify the password 

        $user = $result_select->fetch_assoc(); // fetchinf the data from the user variable 
        if (password_verify($password, $user['password'])) { // if statement to verify the paswword 
            
            $_SESSION['loggedIn'] = true; // if the password matches, set logged in status
            $_SESSION['user_email'] = $email; 
            $_SESSION['userRole'] = $user['role']; // Set user's role in session

            // Redirect based on user role
            switch ($user['role']) {
                case 'admin':
                    header('Location: ../menus/admin_menu.php');
                    break;
                case 'tenant':
                    header('Location: ../menus/tenant_menu.php');
                    break;
                case 'landlord':
                    header('Location: ../menus/landlord_menu.php');
                    break;
                default:
                    // Default redirection for unknown roles (e.g., Public)
                    header('Location: index.php');
                    break;
            }
            exit; // Ensure no further output is sent after redirection
        } else { // otherwise 
            $login_error = "Invalid password. Please try again.";
        }
    } else { // otherwise 
        $login_error = "User not found. Please register if you don't have an account.";
    }

    $stmt_select->close(); // closing the onnection with the database 
}

$db_connection->close(); // closing the database connection 
?>

<!DOCTYPE html> 
<html>
<head> 
    <!-- adding the login as subheading -->
     <title> Log in </title> 
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

        /* Styling for input[type="email"], input[type="password"], .submit-button, select */
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
            background-color: #3498db; /* Background color for submit button */
            color: white; /* Text color for submit button */
            border: none; /* Removing border from submit button */
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

        /* Styling for form elements */
        form {
            width: 100%; /* Form takes full width of its parent */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking form elements vertically */
            align-items: center; /* Horizontally centering form elements */
            padding: 0; /* Resetting padding */
            margin: 0; /* Resetting margin */
        }

        /* Styling for anchor (link) elements */
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
        <!-- adding the login as subheading -->
        <h2>Login</h2>
        
        <?php if (!empty($login_error)) : ?>
            <p style="color: red;"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <!-- adding a form for the login page with the email, password and submit -->
        <form action="login.php" method="POST" novalidate>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="submit-button">Login</button>
        
            <!-- redirecting the user to the homepage -->
        <p>Don't have an account? <a href="register.php">Register</a></p>
        <a href="index.php" class="submit-button">Home</a>
        </form>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>