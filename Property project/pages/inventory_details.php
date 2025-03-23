   <?php
   $currentPage = 'Inventory Details';// declaring the current page as Inventory Details

   require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
   require_once 'connection.php'; // requiring the connection script to connect to the database

   $userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
   $permissions = getUserRolePermissions($userType);

$sql = "SELECT property_id, item_name, quantity, `condition` FROM inventory_details"; // using sql for the SELECT query
$stmt = $db_connection->prepare($sql); // using the stmt to prepare the SQL statement that will compile the SQL query 
$stmt->execute(); // using stmt to execute the prepared statement  
$result = $stmt->get_result(); // using result to store the result in the function

$db_connection->close(); // clsoing the connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Details</title>
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
            margin-top: 100px; /* Adding margin on top */
            min-width: 600px; /* Setting minimum width */
            background-color: white; /* Setting background color */
            padding: 20px; /* Adding padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering child elements */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 20px; /* Adding margin on bottom */
        }

        /* Styling for table elements */
        table {
            width: 100%; /* Table takes full width */
            border-collapse: collapse; /* Collapse border spacing */
            margin-top: 20px; /* Adding margin on top */
            background-color: #fff; /* Setting background color */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Adding box shadow */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* Border for table cells */
            padding: 12px; /* Adding padding */
            text-align: left; /* Align text to left */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* Background color for table header cells */
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

    </style>
</head>
<body>
    <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>
        <div class="container">
            <!-- displaying the inventory details and its table -->
        <h2>Inventory Details</h2>

        <table>
            <thead>
                <tr>
                    <th>Property ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>

                <?php
                // if statement for when it fetches the data
                if ($result->num_rows > 0) { // while loop to fectch the result variable and display the table values
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["property_id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["item_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["condition"]) . "</td>";
                        echo "</tr>";
                    }
                } else { // otherwise display the error message
                    echo "<tr><td>No inventory details found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- include the footer-->
    <?php include 'footer.php'; ?>   
</body>
</html>