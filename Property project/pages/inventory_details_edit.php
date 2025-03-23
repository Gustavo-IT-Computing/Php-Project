<?php
$currentPage = 'Edit Inventory';// declaring the current page as Edit Inventory

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errorMessage = ''; // initialing the variable errorMessage
$property = null; // initialing the variable property as null
$inventoryDetails = null; // initialing the variable inventoryDetails as null
$successMessage = ''; // initialing the variable successMessage

// Creating an if statement to check if the form data has been submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if statment for when the button submit_property_id is clicked 
    if (isset($_POST['submit_property_id'])) {
        $propertyId = $_POST['property_id']; // defining propertyId variable when it is posted
        $sqlProperty = "SELECT * FROM properties WHERE id = ?"; // sqlProperty to the SELECT query
        $stmtProperty = $db_connection->prepare($sqlProperty);  // using the stmtProperty to prepare the SQL statement that will compile the SQL query 
        $stmtProperty->bind_param("i", $propertyId); // using stmtProperty to to bind the parameters to avoid SQL injection
        $stmtProperty->execute(); // using stmtProperty to execute the prepared statement 
        $resultProperty = $stmtProperty->get_result(); // using resultProperty to store the result in the function

        if ($resultProperty && $resultProperty->num_rows > 0) { // if statement for when there is data on the properties datatbase
            $property = $resultProperty->fetch_assoc(); // using property to fetch all the data

            $sqlInventory = "SELECT * FROM inventory_details WHERE property_id = ?"; // sqlInventory for the SELECT query
            $stmtInventory = $db_connection->prepare($sqlInventory); // using the stmtInventory to prepare the SQL statement that will compile the SQL query 
            $stmtInventory->bind_param("i", $propertyId); // using stmtInventory to to bind the parameters to avoid SQL injection
            $stmtInventory->execute(); // using stmtInventory to execute the prepared statement 
            $resultInventory = $stmtInventory->get_result();  // using resultInventory to store the result in the function

            if ($resultInventory && $resultInventory->num_rows > 0) { // if there  is data in the inventory_details database, procced with fetching the data 
                $inventoryDetails = $resultInventory->fetch_assoc(); // using inventoryDetails to fetch all the data
            } else { // otherwise, if there's no details, display the message 
                $errorMessage = 'No inventory details found for this property. You can add new inventory above.';
            }
        } else { // otherwise dipskay the error message 
            $errorMessage = 'Property not found. Please enter a valid Property ID.';
        }
    }

    // Creating an if statement to check if the submit_inventory has been submitted using the POST method
    if (isset($_POST['submit_inventory'])) {
        $item_name = $_POST['item_name']; // checking if the item_name was posted 
        $quantity = $_POST['quantity']; // checking if the quantity was posted 
        $condition = $_POST['condition']; // checking if the condition was posted 
        $propertyId = $_POST['property_id']; // checking if the propertyId was posted 

        $insertSql = "INSERT INTO inventory_details (property_id, item_name, quantity, `condition`)
                      VALUES (?, ?, ?, ?)"; // using insertSql for the INSERT query 
        $stmtInsert = $db_connection->prepare($insertSql); // using the stmtInsert to prepare the SQL statement that will compile the SQL query 
        $stmtInsert->bind_param("isds", $propertyId, $item_name, $quantity, $condition);

        if ($stmtInsert->execute()) {
            $successMessage = "Inventory details added successfully.";
            // Fetch newly added inventory details to display
            $sqlNewInventory = "SELECT * FROM inventory_details WHERE property_id = ?";
            $stmtNewInventory = $db_connection->prepare($sqlNewInventory); // using the stmtNewInventory to prepare the SQL statement that will compile the SQL query 
            $stmtNewInventory->bind_param("i", $propertyId); // using stmtNewInventory to to bind the parameters to avoid SQL injection
            $stmtNewInventory->execute(); // using stmtNewInventory to execute the prepared statement 
            $resultNewInventory = $stmtNewInventory->get_result();  // using resultNewInventory to store the result in the function

            if ($resultNewInventory && $resultNewInventory->num_rows > 0) { // if statement for when there is data on the incentory_details
                $inventoryDetails = $resultNewInventory->fetch_assoc(); // inventoryDetails to fetch all the data 
            }
        } else { // otherwise display the error message
            $errorMessage = "Error adding inventory details: " . $stmtInsert->error;
        }
    }
}

$db_connection->close(); // closing the connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- giving the title to edit the inventory-->
    <title>Inventory Details Editor</title>
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

        /* Styling for elements with class "inventory-form" */
        .inventory-form {
            width: 600px; /* Setting width */
            background-color: white; /* Setting background color */
            padding: 20px; /* Adding padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering child elements */
        }

        /* Styling for input, number, and select elements */
        input[type="text"],
        input[type="number"],
        select {
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

        /* Styling for elements with class "submit-button" on hover */
        .submit-button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 20px; /* Adding margin on bottom */
        }

    </style>
</head>
<body>
     <!-- includingg the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="inventory-form">
        <!-- displaying the form when the property is empty-->
    <?php if (empty($property)) : ?>
        <h2>Enter Property ID to Edit Inventory</h2>
        <!-- displaying the message to the user input the property id -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
        <label for="property_id">Enter Property ID:</label>
        <input type="text" id="property_id" name="property_id" required><br><br>
        <input type="submit" class="submit-button" name="submit_property_id" value="Fetch Inventory Details">
    </form>
    <?php endif; ?>

    <!-- if statement when the property is not empty-->
    <?php if (!empty($property)) : ?>
        <!-- and the inventoryDetails is also not empty-->
        <?php if (!empty($inventoryDetails)) : ?>
            <h2>Inventory Details for Property ID: <?php echo $property['id']; ?></h2>
            <!-- displaying the data from the inventory database-->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                <label for="item_name">Item Name:</label>
                <input type="text" id="item_name" name="item_name" value="<?php echo $inventoryDetails['item_name']; ?>" required><br><br>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo $inventoryDetails['quantity']; ?>" required><br><br>
                <label for="condition">Condition:</label>
                <input type="text" id="condition" name="condition" value="<?php echo $inventoryDetails['condition']; ?>" required><br><br>
                <input type="submit" class="submit-button" name="submit_inventory" value="Update Inventory">
            </form>
        
            <!-- else statement when it doesn't find the property ID-->
            <?php else : ?>
            <h2>No inventory details found for Property ID: <?php echo $property['id']; ?></h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                <label for="item_name">Item Name:</label>
                <input type="text" id="item_name" name="item_name" required><br><br>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required><br><br>
                <label for="condition">Condition:</label>
                <input type="text" id="condition" name="condition" required><br><br>
                <input type="submit" class="submit-button" name="submit_inventory" value="Add Inventory">
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <!-- displaying the error message in red-->
    <?php if (!empty($errorMessage)) : ?>
        <p style="color: red;" class="error-message"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <!-- displaying the success message in green-->
    <?php if (!empty($successMessage)) : ?>
        <p style="color: green;" class="success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>
</div>
<!-- including the footer-->
<?php include 'footer.php'; ?>
</body>
</html>