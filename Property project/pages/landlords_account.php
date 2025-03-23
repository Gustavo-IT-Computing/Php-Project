<?php 
$currentPage = 'Landlord Account';// declaring the current page as Landlord Account

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

// using sql for the SELECT statement 
$sql = "SELECT p.id AS property_id, p.title AS property_title, p.rental_price, p.location, p.bedrooms, p.tenancy_length, p.description, p.photo_path, p.eircode, IFNULL(p.inventory_details_id, 'N/A') AS inventory_details_id, IFNULL(p.landlord_id, 'N/A') AS landlord_id, l.commission_rate, l.management_fee
FROM properties p
LEFT JOIN landlord l ON p.landlord_id = l.id;";

$result = $db_connection->query($sql); // using result to execute the SELECT query

?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to the landlord account-->
    <title> Landlord Account </title> 
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

        /* Styling for elements with class "container" */
        .container {
            width: 800px; /* Adjust width as needed */
            background-color: white; /* Setting background color */
            padding: 20px; /* Adding padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering child elements */
        }

        /* Styling for table elements */
        table {
            width: 100%; /* Table takes full width */
            border-collapse: collapse; /* Collapse border spacing */
            margin-top: 20px; /* Adding margin on top */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* Border for table cells */
            padding: 10px; /* Adding padding */
            text-align: left; /* Align text to left */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* Background color for table header cells */
        }

    </style>
    
</head> 
<body>
     <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="container">
        <!-- displaying the table for the landlord account along with the renatl income details-->
    <h1>Landlords Account - Rental Income</h1>
    <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Property ID</th>";
            echo "<th>Property Title</th>";
            echo "<th>Rental Price</th>";
            echo "<th>Commission Rate (%)</th>";
            echo "<th>Management Fee (%)</th>";
            echo "<th>Commission Deduction</th>";
            echo "<th>Management Fee Deduction</th>";
            echo "<th>Net Rental Income</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) { // while loop to fetch the data from the database
                $property_id = $row['property_id']; // checking the property_id
                $property_title = $row['property_title']; // checking the property_title
                $rental_price = $row['rental_price']; // checking the rental_price
                $commission_rate = $row['commission_rate']; // checking the commission_rate
                $management_fee = $row['management_fee']; // checking the management_fee
                $commission_amount = ($commission_rate / 100) * $rental_price; // checking the commission_amount by calculating through a formula 
                $management_fee_amount = ($management_fee / 100) * $rental_price; // checking the management_fee_amount by calculating through a formula 
                $net_rental_income = $rental_price - $commission_amount - $management_fee_amount; // displaying the net_rental_income

                echo "<tr>";
                echo "<td>{$property_id}</td>";
                echo "<td>{$property_title}</td>";
                echo "<td>€" . number_format($rental_price, 2) . "</td>";
                echo "<td>{$commission_rate}%</td>";
                echo "<td>{$management_fee}%</td>";
                echo "<td>€" . number_format($commission_amount, 2) . "</td>"; // Display commission deduction
                echo "<td>€" . number_format($management_fee_amount, 2) . "</td>"; // Display management fee deduction
                echo "<td>€" . number_format($net_rental_income, 2) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No properties found.</p>";
        }
        ?>
    </div>
    <!-- including the footer -->
    <?php include 'footer.php'; ?>    
</body>
</html>

<?php
$db_connection->close(); // closing the connection
?>
