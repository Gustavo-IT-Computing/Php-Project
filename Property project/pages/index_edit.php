<?php
$currentPage = 'Edit Home'; // declaring the current page as Edit Home

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

// Check user permissions
$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

        function getFeaturedProperties($db_connection) // creating a function called getFeaturedProperties to fetch the featured properties from the database 
        {
            $sql = "SELECT * FROM featured_properties ORDER BY id"; // sql for the SELECT query
            $result = $db_connection->query($sql); // result to execute the query 

            if ($result->num_rows > 0) { // if statement for when there is data in the properties table
                return $result->fetch_all(MYSQLI_ASSOC); // returns the result fetched
            } else { // otherwise 
                return array(); // return an empty array 
            }
        }

        // creating a function called updateFeaturedProperty that will update the featured data 
        function updateFeaturedProperty($db_connection, $property_id, $property_name, $description, $price)
        {
            $sql = "UPDATE featured_properties 
                    SET property_name = '$property_name', description = '$description', price = '$price' 
                    WHERE id = $property_id";
                    // sql to UPDATE query
            return $db_connection->query($sql); // retutning the execution of the query
        }

// Creating an if statement to check if the form data has been submitted using the POST method and checking for the submision of the property_id
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['property_id'])) {
    $property_ids = $_POST['property_id']; // checking if the property_id was posted 
    $property_names = $_POST['property_name'];  // checking if the property_name was posted 
    $descriptions = $_POST['description'];  // checking if the description was posted 
    $prices = $_POST['price'];  // checking if the price was posted 

    foreach ($property_ids as $index => $property_id) { // foreach loop to update each property 
        $property_name = $property_names[$index]; // assigning property_name as property_name on the index 
        $description = $descriptions[$index]; // assigning description as description on the index 
        $price = $prices[$index]; // assigning price as price on the index 

        // calling the updateFeaturedProperty method and passing the new values to be updated in the database
        updateFeaturedProperty($db_connection, $property_id, $property_name, $description, $price);
    }

    $successMessage = "Featured information updated successfully!"; // displaying the succes message 
}

$featuredProperties = getFeaturedProperties($db_connection); // featuredProperties to fetch the fetaured properties of the database 

$db_connection->close(); // closing the connection

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- giving the title to edit the home page -->
    <title>Edit Home Page Features - Hynesdrade Dublin Property</title>
    <style>
        /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting the background color of the body */
            font-family: "Roboto", sans-serif; /* Setting the font-family for the body text */
            padding: 0; /* Resetting padding to ensure no default spacing */
            display: flex; /* Using flexbox for layout */
            justify-content: center; /* Horizontally centering content */
            align-items: center; /* Vertically centering content */
            flex-direction: column; /* Stacking flex items vertically */
            height: 100vh; /* Full viewport height */
        }

        /* Styling for elements with class "container" */
        .container {
            margin-top: 300px; /* Applying margin on top to create space */
            background-color: white; /* Setting background color */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for table elements */
        table {
            width: 100%; /* Table takes full width */
            border-collapse: collapse; /* Collapse border spacing */
            margin-top: 20px; /* Applying margin on top */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* Border for table cells */
            padding: 12px; /* Applying padding */
            text-align: left; /* Align text to left */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* Background color for table header cells */
            font-weight: bold; /* Making the text bold */
        }

        /* Styling for elements with class "action-buttons" */
        .action-buttons {
            display: flex; /* Using flexbox for layout */
            gap: 10px; /* Adding gap between flex items */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Taking full width */
            padding: 10px; /* Applying padding */
            margin-top: 20px; /* Applying margin on top */
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
            color: #333; /* Applying color to h2 elements */
            margin-bottom: 20px; /* Applying margin on bottom */
        }

    </style>
</head>
<body>
    <!-- including the header  -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="container">
        <h1>Edit Featured Information</h1>

        <!-- displaying the succes message along with the isset function to perform sanitanization-->
        <?php if (isset($successMessage)) : ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <!-- creating a form to display the featured properties -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
            <table>
                <tr>
                    <th>Property Name</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>

                <!-- creating a foreach loop for the images of the index.php page-->
                <?php foreach ($featuredProperties as $property) : ?>
                    <tr>
                        <td><input type="text" name="property_name[]" value="<?php echo htmlspecialchars($property['property_name']); ?>"></td>
                        <td><textarea name="description[]" rows="4"><?php echo htmlspecialchars($property['description']); ?></textarea></td>
                        <td><input type="text" name="price[]" value="<?php echo htmlspecialchars($property['price']); ?>"></td>
                        <input type="hidden" name="property_id[]" value="<?php echo $property['id']; ?>">
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" class="submit-button" value="Save Changes">
        </form>
    </div>

    <!-- adding the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>