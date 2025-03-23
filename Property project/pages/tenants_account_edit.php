<?php
$currentPage = 'Tenants Account Edit'; // declaring the current page as Tenants Account Edit

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errorMessage = ''; // initializing the errorMessage
$successMessage = ''; // initializing the successMessage

// Creating an if statement to check if the form data has been submitted using the POST method and checking for the submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $id = $_POST['id']; // checking if the id was posted 
    $rental_fee = $_POST['rental_fee']; // checking if the rental_fee was posted 
    $tenancy_length = $_POST['tenancy_length']; // checking if the tenancy_length was posted 
    $tenancy_agreement = $_POST['tenancy_agreement']; // checking if the tenancy_agreement was posted 
    $start_date = $_POST['start_date']; // checking if the start_date was posted 
    $end_date = $_POST['end_date']; // checking if the end_date was posted 
    $amount_paid = $_POST['amount_paid']; // checking if the amount_paid was posted 
    $tenure = $_POST['tenure']; // checking if the tenure was posted 
 
    // using sql_update for the UPDATE query
    $sql_update = "UPDATE tenancy_accounts SET rental_fee = ?, tenancy_length = ?, tenancy_agreement = ?, start_date = ?, end_date = ?, amount_paid = ?, tenure = ? WHERE id = ?";
    $stmt = $db_connection->prepare($sql_update);  // using the stmt to prepare the SQL statement that will compile the SQL query 

    // using stmt to to bind the parameters to avoid SQL injection
    $stmt->bind_param("dssssdsi", $rental_fee, $tenancy_length, $tenancy_agreement, $start_date, $end_date, $amount_paid, $tenure, $id);

    if ($stmt->execute()) { // if statement for when the stmt is executed, to dispaly a successfull message 
        $successMessage = 'Tenancy account updated successfully.';

    } else { // otherwise display the error message 
        $errorMessage = 'Error updating tenancy account: ' . $stmt->error;
    }
    $stmt->close(); // closing the query
}

$sql = "SELECT * FROM tenancy_accounts"; // using sql for the SELECT query
$result = $db_connection->query($sql); // using result to execute the statement 

$db_connection->close(); // closing the connection

?>

<!DOCTYPE html> 
<html>
<head> 
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- giving the title of Edit Tenancy Account-->
    <title>Edit Tenancy Account</title> 
    <style>
        /* Styling for the body element */
        body {
            font-family: Arial, sans-serif; /* Setting the font family for the body text */
            background-color: #f5f5f5; /* Setting the background color for the entire page */
            padding: 20px; /* Adding padding around the content of the body */
        }

        /* Styling for elements with class "container" */
        .container {
            margin-top: 100px; /* Adding top margin to the container */
            min-width: 600px; /* Setting minimum width for the container */
            background-color: white; /* Background color for the container */
            padding: 20px; /* Adding padding inside the container */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the container */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 20px; /* Adding margin space below h2 elements */
        }

        /* Styling for table element */
        table {
            width: 100%; /* Setting width to take full available width */
            border-collapse: collapse; /* Collapsing borders between table cells */
            margin-top: 20px; /* Adding margin space on top of the table */
            background-color: #fff; /* Background color for the table */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* Adding border to table cells */
            padding: 12px; /* Adding padding inside table cells */
            text-align: left; /* Aligning text to the left within table cells */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* Background color for table header cells */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Setting width to take full available width */
            padding: 10px; /* Adding padding inside the button */
            margin-top: 20px; /* Adding top margin */
            background-color: #3498db; /* Background color for the button */
            color: white; /* Text color for the button */
            border: none; /* Removing border from the button */
            border-radius: 5px; /* Adding rounded corners to the button */
            cursor: pointer; /* Changing cursor to pointer */
        }

</style>

</head> 
<body>
      <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="container">
    <h2>Edit Tenants Account</h2>

        <!-- displaying the error message in red-->
        <?php if (!empty($errorMessage)) : ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- displaying the success message in green-->
        <?php if (!empty($successMessage)) : ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <!-- displaying the table for the tenants account-->
        <table>
            <thead>
                <tr>
                    <th>Rental Fee</th>
                    <th>Tenancy Length (months)</th>
                    <th>Tenancy Agreement</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount Paid</th>
                    <th>Tenure</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <!-- displaying the info into the atble based on the data fetched from the database-->
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['rental_fee']}</td>";
                        echo "<td>{$row['tenancy_length']}</td>";
                        echo "<td>{$row['tenancy_agreement']}</td>";
                        echo "<td>{$row['start_date']}</td>";
                        echo "<td>{$row['end_date']}</td>";
                        echo "<td>{$row['amount_paid']}</td>";
                        echo "<td>{$row['tenure']}</td>";
                        echo "<td>";
                        echo "<form method='post' class='edit-form'>";
                        echo "<input type='hidden' name='id' value='{$row['id']}'>";
                        echo "<input type='number' name='rental_fee' value='{$row['rental_fee']}' required><br>";
                        echo "<input type='number' name='tenancy_length' value='{$row['tenancy_length']}' required><br>";
                        echo "<input type='text' name='tenancy_agreement' value='{$row['tenancy_agreement']}' required><br>";
                        echo "<input type='date' name='start_date' value='{$row['start_date']}' required><br>";
                        echo "<input type='date' name='end_date' value='{$row['end_date']}' required><br>";
                        echo "<input type='number' name='amount_paid' value='{$row['amount_paid']}' required><br>";
                        echo "<input type='text' name='tenure' value='{$row['tenure']}' required><br>";
                        echo "<input type='submit' class='submit-button' name='submit' value='Update'> <br></br>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else { // otherwise display the error message as span
                    echo "<tr><td colspan='8'>No tenancy accounts found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>