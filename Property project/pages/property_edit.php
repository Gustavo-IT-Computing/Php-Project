<?php 
$currentPage = 'Property Edit'; // declaring the current page as Property Edit

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errorMessage = ''; // initializing the variable errorMessage
$property = null; // initializing the variable property and assigning it to null
$successMessage = ''; // initializing the variable successMessage

// Creating an if statement to check if the form data has been submitted using the POST method and checking for the submision of the eircode
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_eircode'])) {
    $eircode = $_POST['eircode']; // checking if the eircode was posted
    $sql = "SELECT * FROM properties WHERE eircode = '$eircode'"; // using sql for the SELECT query
    $result = $db_connection->query($sql); // using result to execute the query

    if ($result && $result->num_rows > 0) { // if statement for when there is data in the properties table 
        $property = $result->fetch_assoc(); // using property to fetch all the data
    } else { // otherwise display the error message 
        $errorMessage = 'Property not found for the specified Eircode.';
    }
}

// Creating an if statement to check if the form data has been submitted using the POST method and checking for the submision of the property 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_property'])) {

    $propertyId = $_POST['property_id']; // checking if the propertyId was posted 
    $title = $_POST['title'];  // checking if the title was posted 
    $location = $_POST['location']; // checking if the location was posted 
    $rental_price = $_POST['rental_price']; // checking if the rental_price was posted 
    $bedrooms = $_POST['bedrooms']; // checking if the bedrooms was posted 
    $tenancy_length = $_POST['tenancy_length']; // checking if the tenancy_length was posted 
    $description = $_POST['description']; // checking if the description was posted 

    // using updateSql for the UPDATE query
    $updateSql = "UPDATE properties SET title = '$title', location = '$location', rental_price = '$rental_price', bedrooms = '$bedrooms', tenancy_length = '$tenancy_length', description = '$description'
                  WHERE id = $propertyId";

if ($db_connection->query($updateSql) === TRUE) { // if the execution of the UPDATE query is equal to true, display the success message
    $successMessage = "Property details updated successfully.";
    // using property to fetch the properies values and storing them to be displayed in the form
    $property = ['id' => $propertyId,'title' => $title, 'location' => $location,'rental_price' => $rental_price,'bedrooms' => $bedrooms, 'tenancy_length' => $tenancy_length,'description' => $description];
} else { // otherwise displaying the error message 
    $errorMessage = "Error updating property: " . $db_connection->error;
}
}

$db_connection->close(); // closing the connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- giving the title of Edit Property-->
    <title>Edit Property - Hynesdrade</title>
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

        /* Styling for elements with class "register-box" */
        .register-box {
            width: 600px; /* Setting the width of the register box */
            background-color: white; /* Background color for the register box */
            padding: 20px; /* Adding padding inside the register box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the register box */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for input[type="text"], input[type="number"], input[type="email"], input[type="password"], select, and textarea elements */
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%; /* Taking full width of the parent minus padding */
            padding: 10px; /* Adding padding inside input fields */
            margin-top: 10px; /* Adding margin space on top */
            border-radius: 5px; /* Adding rounded corners to input fields */
            border: 1px solid #ccc; /* Adding border to input fields */
            box-sizing: border-box; /* Including padding and border in the width calculation */
            font-family: "Roboto", sans-serif; /* Ensuring consistent font */
            font-size: 16px; /* Optional: Ensures consistent font size */
            color: grey; /* Text color for input fields */
        }

        /* Styling for textarea elements */
        textarea {
            height: 100px; /* Setting the height of textarea */
        }

        /* Styling for input[type="file"] elements */
        input[type="file"] {
            border: none; /* Removing default border styling for file input */
            margin-top: 10px; /* Adding margin space on top */
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
        .submit.button:hover {
            background-color: #2980b9; /* Background color change on hover for the submit button */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Text color for h2 elements */
        }

    </style>
</head>
<body>
     <!-- including the header section -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <div class="register-box">
        <!-- if statement for when the property is empty, to display the eircode field for the user to input-->
        <?php if (empty($property)) : ?>
        <h2>Edit Listing</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
            <label for="eircode">Enter Eircode:</label>
            <input type="text" id="eircode" name="eircode" required><br><br>
            <input type="submit" class="submit-button" name="submit_eircode" value="Fetch Property Details">
        </form>
        <?php endif; ?>

        <!-- if statement for when the property is not emoty, to display the form with its characteristics -->
        <?php if (!empty($property)) : ?>
            <h3>Property Details:</h3>
           <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $property['title']; ?>" required><br><br>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo $property['location']; ?>" required><br><br>

                <label for="rental_price">Rental Price (€/month):</label>
                <input type="number" id="rental_price" name="rental_price" value="<?php echo $property['rental_price']; ?>" required><br><br>

                <label for="bedrooms">Number of Bedrooms:</label>
                <input type="number" id="bedrooms" name="bedrooms" value="<?php echo $property['bedrooms']; ?>" required><br><br>

                <label for="tenancy_length">Tenancy Length (months):</label>
                <input type="number" id="tenancy_length" name="tenancy_length" value="<?php echo $property['tenancy_length']; ?>" required><br><br>

                <label for="description">Description:</label><br>
                <textarea id="description" name="description" rows="4" cols="50" required><?php echo $property['description']; ?></textarea><br><br>

                <label for="photos">Upload Photos (Max 5 photos):</label>
                <input type="file" id="photos" name="photos[]" accept="image/*" multiple required><br><br>

                <input type="submit" class="submit-button" name="submit_property" value="Edit Property">
            </form>
            <?php endif; ?>

            <!-- displaying the success message in green-->
            <?php if (!empty($successMessage)) : ?>
                <p style="color: green;"><?php echo $successMessage; ?></p>
            <?php endif; ?>

            <!-- displaying the error message in red-->
            <?php if (!empty($errorMessage)) : ?>
                <p style="color: red;"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
</div>
<!-- including the footer -->
<?php include 'footer.php'; ?>
</body>
</html>