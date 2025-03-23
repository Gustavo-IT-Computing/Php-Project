<?php
$currentPage = 'Landlord Settings';// declaring the current page as Landlord Settings

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

// if statement to check if the user is alreadu=y logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php"); // if it is, head him to the index.php page
    exit;
}

// initializing the email c=variable using the isset function 
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$username = ''; // initializing username
$commissionRate = ''; // initializing commissionRate
$managementFee = ''; // initializing managementFee
$password = ''; // initializing password
$errorMessage = ''; // initializing errorMessage
$successMessage = ''; // initializing successMessage

if ($email) { // if statement for the email
    $selectQuery = "SELECT * FROM landlord WHERE email = ?"; // using selectQuery for the SELECT query
    $stmtSelect = $db_connection->prepare($selectQuery); // using the selectQuery to prepare the SQL statement that will compile the SQL query 
    $stmtSelect->bind_param('s', $email); // using checkExistingPropertyStmt to to bind the parameters to avoid SQL injection
    $stmtSelect->execute(); // using checkExistingProperselectQuerytyStmt to execute the prepared statement 
    $resultSelect = $stmtSelect->get_result(); // using resultSelect to store the result in the function

    if ($resultSelect->num_rows > 0) {  // if statement for when there is data in the landlord database
        $userDetails = $resultSelect->fetch_assoc(); // using userDetails to fetch all the data
        $username = $userDetails['username']; // passing username as userDetails based on the username value 
        $commissionRate = $userDetails['commission_rate']; // passing commissionRate as userDetails based on the commission_rate 
        $managementFee = $userDetails['management_fee']; // passing managementFee as userDetails based on the management_fee
        $password = $userDetails['password']; // passing password as userDetails based on the password
    }

    $stmtSelect->close(); // closing the select statment 
}

// Creating an if statement to check if the form data has been submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // retrieving the username
    $commissionRate = $_POST['commission_rate']; // retrieving the commissionRate
    $managementFee = $_POST['management_fee']; // retrieving the managementFee

    // Check if username, commissionRate, and managementFee are not empty
if (!empty($username) && !empty($commissionRate) && !empty($managementFee)) {
    // Check if email is not empty
    if ($email) {
        // SQL query to update user details
        $updateQuery = "UPDATE landlord SET username = ?, commission_rate = ?, management_fee = ? WHERE email = ?";
        // Prepare the update statement
        $stmtUpdate = $db_connection->prepare($updateQuery);
        // Bind parameters to the update statement
        $stmtUpdate->bind_param('ssss', $username, $commissionRate, $managementFee, $email);

        // Execute the update statement
        if ($stmtUpdate->execute()) {
            // If update is successful, set success message
            $successMessage = 'User details updated successfully!';
        } else {
            // If update fails, set error message
            $errorMessage = 'Failed to update user details.';
        }

        // Close the update statement
        $stmtUpdate->close();
    } else {
        // SQL query to insert new user details
        $insertQuery = "INSERT INTO landlord (email, username, commission_rate, management_fee, password) VALUES (?, ?, ?, ?, ?)";
        // Prepare the insert statement
        $stmtInsert = $db_connection->prepare($insertQuery);
        // Bind parameters to the insert statement
        $stmtInsert->bind_param('sssss', $email, $username, $commissionRate, $managementFee, $password);

        // Execute the insert statement
        if ($stmtInsert->execute()) {
            // If insertion is successful, set success message
            $successMessage = 'User details added successfully!';
        } else {
            // If insertion fails, set error message
            $errorMessage = 'Failed to add user details.';
        }

        // Close the insert statement
        $stmtInsert->close();
    }
} else {
    // If required fields are not filled, set error message
    $errorMessage = 'Please fill in all the fields.';
}

// Close the database connection
$db_connection->close();
?>


<!DOCTYPE html> 
<html>
<head> 
    <!-- giving the title to Lanlord Settings-->
     <title> Landlord Settings</title> 
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

        /* Styling for elements with class "login-box" */
        .login-box {
            width: 400px; /* Setting width */
            background-color: white; /* Setting background color */
            padding: 20px; /* Adding padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering child elements */
        }

        /* Styling for input and select elements */
        input, select {
            width: 100%; /* Taking full width */
            padding: 10px; /* Adding padding */
            margin-top: 10px; /* Adding margin on top */
            border-radius: 5px; /* Adding rounded corners */
            border: 1px solid #ccc; /* Adding border */
            box-sizing: border-box; /* Ensuring padding and border are included in the element's total width and height */
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

        /* Styling for button elements on hover */
        button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Setting text color */
            margin-bottom: 20px; /* Adding margin on bottom */
        }

    </style>
</head> 
<!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <!-- displaying the suscces messsage in green -->
    <?php if ($successMessage) : ?>
        <p style="color: green;"><?php echo htmlentities($successMessage); ?></p>

        <!-- displaying the error message in red-->
    <?php elseif ($errorMessage) : ?>
        <p style="color: red;"><?php echo htmlentities($errorMessage); ?></p>
    <?php endif; ?>

    <div class="login-box">
    <!-- Display form to update user details -->
    <h2>Landlord Settings</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
    
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlentities($email) ?>" readonly><br><br> <!-- Readonly field for email -->

        <label for="password">Password:</label>
        <input type="text" id="password" name="password" value="<?= htmlentities($password) ?>" readonly><br><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlentities($username) ?>" required><br><br>

        <label for="commission_rate">Commission Rate:</label>
        <input type="text" id="commission_rate" name="commission_rate" value="<?= htmlentities($commissionRate) ?>" required><br><br>

        <label for="management_fee">Management Fee:</label>
        <input type="text" id="management_fee" name="management_fee" value="<?= htmlentities($managementFee) ?>" required><br><br>

        <button type="submit" class="submit-button">Update</button>
    </form>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>
