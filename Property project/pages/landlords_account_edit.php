<?php 
$currentPage = 'Edit Landlord'; // declaring the current page as Edit Landlord

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errorMessage = ''; // initializng the errorMessage variable 
$successMessage = ''; // initializng the successMessage variable 

// Creating an if statement to check if the form data has been submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted to update landlord details
    if (isset($_POST['landlord_id'])) {
        $username = $_POST['username']; // checking for the username 
        $email = $_POST['email']; // checking for the email 
        $password = $_POST['password']; // checking for the password 
        $commissionRate = $_POST['commission_rate']; // checking for the commissionRate 
        $managementFee = $_POST['management_fee']; // checking for the managementFee 
        $landlordId = $_POST['landlord_id']; // checking for the landlordId 

        $updateSql = "UPDATE landlord
                      SET username = ?, email = ?, password = ?, commission_rate = ?, management_fee = ? 
                      WHERE id = ?"; // using updateSql for the UPDATE query
        $stmt = $db_connection->prepare($updateSql); // using the stmt to prepare the SQL statement that will compile the SQL query 
        $stmt->bind_param('ssssdi', $username, $email, $password, $commissionRate, $managementFee, $landlordId); // using stmt to to bind the parameters to avoid SQL injection
        $stmt->execute(); // using stmt to execute the prepared statement 

        if ($stmt->affected_rows > 0) { // if statement to check if the update was succesfull and displaying the success message 
            $successMessage = 'Landlord details updated successfully!';
        } else { // otherwise display the error messaeg
            $errorMessage = 'Failed to update landlord details. Please try again.';
        }
        $stmt->close(); // closing the statement 
        $db_connection->close(); // closing the connection
    } else {
        // Form submitted to retrieve landlord details
        $email = trim($_POST['email']); // Retrieve and sanitize entered email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); 

        $sql = "SELECT * FROM landlord WHERE email = ?"; // using sql for the SELECT query
        $stmt = $db_connection->prepare($sql);  // using the stmt to prepare the SQL statement that will compile the SQL query 
        $stmt->bind_param('s', $email); // using stmt to to bind the parameters to avoid SQL injection
        $stmt->execute(); // using stmt to execute the prepared statement 
        $result = $stmt->get_result(); // using result to store the result in the function

        if ($result->num_rows > 0) { // if statement for when there is data in landlord table
            $landlord = $result->fetch_assoc(); // using landlord to fetch all the data 
            $stmt->close(); // closing the statement 
            $db_connection->close(); // closing the connection
        } else { // otherwise display the error message 
            $errorMessage = 'Landlord not found. Please try again or contact support.';
        }
    }
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- gicing the title of Edit landlord-->
    <title>Edit Landlord</title> 
    <style>
        /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting background color */
            font-family: "Roboto", sans-serif; /* Setting font family */
            margin: 0; /* Resetting margin */
            padding: 0; /* Resetting padding */
            display: flex; /* Using flexbox for layout */
            justify-content: center; /* Horizontally centering content */
            align-items: center; /* Vertically centering content */
            flex-direction: column; /* Stacking flex items vertically */
            height: 100vh; /* Full viewport height */
        }

        /* Styling for elements with class "register-box" */
        .register-box {
            width: 600px; /* Setting width */
            background-color: white; /* Setting background color */
            padding: 20px; /* Adding padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering child elements */
        }

        /* Styling for input, email, password, select, and textarea elements */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%; /* Taking full width */
            padding: 10px; /* Adding padding */
            margin-top: 10px; /* Adding margin on top */
            border-radius: 5px; /* Adding rounded corners */
            border: 1px solid #ccc; /* Adding border */
            box-sizing: border-box; /* Ensuring padding and border are included in the element's total width and height */
            font-family: "Roboto", sans-serif; /* Ensures consistent font */
            font-size: 16px; /* Optional: Ensures consistent font size */
            color: grey; /* Setting text color */
        }

        /* Styling for textarea elements */
        textarea {
            height: 100px; /* Setting height */
        }

        /* Styling for file input elements */
        input[type="file"] {
            border: none; /* Removing default border styling for file input */
            margin-top: 10px; /* Adding margin on top */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Taking full width */
            padding: 10px; /* Adding padding */
            margin-top: 20px; /* Adding margin on top */
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            border: none; /* Removing border */
            border-radius: 5px; /* Adding rounded corners */
            cursor: pointer; /* Changing cursor to pointer on hover */
        }

        /* Styling for elements with class "submit" on hover */
        .submit.button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Setting text color */
            margin-bottom: 20px; /* Adding margin on bottom */
        }

    </style>
</head> 
<body>
     <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="register-box">
        <!--if statament to display the error message in red -->
        <?php if (!empty($errorMessage)) : ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- if statement to display the success message in green-->
        <?php if (!empty($successMessage)) : ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        
        <!-- if statement for the lanlord cariable, to display the form-->
        <?php if (isset($landlord)) : ?>
            <h2>Edit Landlord Details</h2>
            <form method="POST" action="" novalidate >
                <input type="hidden" name="landlord_id" value="<?php echo $landlord['id']; ?>">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo $landlord['username']; ?>" required>
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $landlord['email']; ?>" required>
                <label>Password:</label>
                <input type="password" name="password" value="<?php echo $landlord['password']; ?>" required>
                <label>Commission Rate:</label>
                <input type="text" name="commission_rate" value="<?php echo $landlord['commission_rate']; ?>" required>
                <label>Management Fee:</label>
                <input type="text" name="management_fee" value="<?php echo $landlord['management_fee']; ?>" required>
                <button type="submit" class="submit-button">Update Landlord</button>
            </form>
        <?php else : ?>

            <!-- creating the form to edit the landlord details-->
            <h2>Find Landlord for Editing</h2>
            <form method="POST" action="" novalidate >
                <input type="email" name="email" placeholder="Enter Landlord Email" required>
                <button type="submit" class="submit-button">Search</button>
            </form>
            <!-- if he doesn't have an account, directinh him to the appropriate page-->
            <p>Don't have the email? <a href="landlords_account.php">Back to Landlords Account</a></p>
        <?php endif; ?>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>
