<?php 
$currentPage = 'Tenancy Account'; // declaring the current page as Tenancy Account

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errorMessage = ''; // initialzing the errorMessage variable
$successMessage = ''; // initialzing the successMessage variable

$sql = "SELECT * FROM tenancy_accounts"; // using sql for the SELECT query 
$stmt = $db_connection->prepare($sql); // using stmt to execute the query

if (!$stmt) { // if statement to check if the statement wasn't prepared successfully and printing an error if not 
    $errorMessage = "Error preparing statement: " . $db_connection->error;
} else { // otherwise
    
    $stmt->execute(); // using stmt to execute the prepared statement 
    $result = $stmt->get_result(); // using result to get the result in the function
}

$stmt->close(); // closing the query
$db_connection->close(); // closing the connection

?>

<!DOCTYPE html> 
<html>
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- giving the title to tenancy account details-->
    <title>Tenancy Account Details</title> 
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

        /* Styling for elements with class "container" */
        .container {
            min-width: 600px; /* Setting minimum width for the container */
            background-color: white; /* Background color for the container */
            padding: 20px; /* Adding padding inside the container */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the container */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for table element */
        table {
            width: 100%; /* Setting width to take full available width */
            border-collapse: collapse; /* Collapsing borders between table cells */
            margin-top: 20px; /* Adding margin space on top of the table */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* Adding border to table cells */
            padding: 8px; /* Adding padding inside table cells */
            text-align: left; /* Aligning text to the left within table cells */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* Background color for table header cells */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Setting text color for h2 elements */
            margin-bottom: 20px; /* Adding margin space below h2 elements */
        }

    </style>
</head> 
<body>
  <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="container">
        <h2>Tenancy Account Details </h2>

        <!-- displaying the error message in red-->
        <?php if (!empty($errorMessage)) : ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- displayinh the success message in red-->
        <?php if (!empty($successMessage)) : ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <!-- displaying the tenancy account detais in the table format-->
        <?php
        if ($result->num_rows > 0) { //
            echo "<table>";
            echo "<tr><th>Rental Fee</th><th>Tenancy Length (months)</th><th>Tenancy Agreement</th><th>Start Date</th><th>End Date</th><th>Amount Paid</th><th>Tenure</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['rental_fee']}</td>";
                echo "<td>{$row['tenancy_length']}</td>";
                echo "<td>{$row['tenancy_agreement']}</td>";
                echo "<td>{$row['start_date']}</td>";
                echo "<td>{$row['end_date']}</td>";
                echo "<td>{$row['amount_paid']}</td>";
                echo "<td>{$row['tenure']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No tenancy accounts found.</p>";
        }
        ?>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>   
</body>
</html>