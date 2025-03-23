<?php 
$currentPage = 'Property Listing'; // declaring the current page as Property Listing

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$successMessage = '';  // creating an empty string called succesMessage to store the successful messages when the form is correct filled
$errorMessage = '';  // creating an empty string called errorMessage to store the error messages when the form is incorrect filled

// Creating an if statement to check if the form data has been submitted using the POST method and checking for the submision of the property 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_property'])) {
    
    $errors = []; // creating an empty array called errors to store error messages

    // creating a variable called required_fields that stores an array with the title, location, eircode, rental_price, bedrooms, tenancy_length and description
    $required_fields = ['title', 'location', 'eircode', 'rental_price', 'bedrooms', 'tenancy_length', 'description'];

    foreach ($required_fields as $field) { // foreach loop for the array of the data and displaying as the $field variable 
        if (empty($_POST[$field])) { // if $field is empty
            $errors[] = ucfirst($field) . " is required."; // store in the errors array the specific field thats wasn't filled 
        }
    }

    $eircode = trim($_POST['eircode']); // adding sanitanization for the eircode 
    if (empty($eircode)) { // if eircode is empty 
        $errors[] = "Please enter your eircode."; // store the message in the errors array

    /* using the preg_match for the regular expression for the eircode, that has to match ^D(0[1-9]|1\d|2[0-4]|6W)[A-Z][0-9]{3}$, where ^ is the start of the string, D is the uppercase letter at the beggining, 
     (0[1-9]) is to match the 01 to 09, 1\d to match the 10 to 19, 2[0-4] to match 20 to 24, 6W to match the exception, followed by [A-Z] for uppercase letters from the alphabet,
     [0-9]{3} to indicate three digits and $ to end the string. */ 
    } elseif (!preg_match('/^D(0[1-9]|1\d|2[0-4]|6W)[A-Z][0-9]{3}$/', $eircode)) {
        $errors[] = "Invalid Eircode format."; // displaying the error message and storing it to the errors array
    }

    if (empty($errors)) { // If there are no errors, proceed with checking the database to see if exists any with the same eircode

        $checkExistingPropertyQuery = "SELECT id FROM properties WHERE eircode = ?"; // creatinh the qurey for the select 

        // using the checkExistingPropertyStmt to prepare the SQL statement that will compile the SQL query 
        $checkExistingPropertyStmt = $db_connection->prepare($checkExistingPropertyQuery);
        $checkExistingPropertyStmt->bind_param("s", $eircode); // using checkExistingPropertyStmt to to bind the parameters to avoid SQL injection
        $checkExistingPropertyStmt->execute(); // using checkExistingPropertyStmt to execute the prepared statement 
        $checkExistingPropertyStmt->store_result(); // using checkExistingPropertyStmt to store the result in the function

        if ($checkExistingPropertyStmt->num_rows > 0) { // if statement to check with a property with same eircode exists 
            $errorMessage = "Property with the same Eircode already exists.";
        } else { // if it doesnt exist
            /*Using the mysqli_real_escape_string() function to to escape special characters in a String for use in a SQL statement.
            This function is used to help to prevent SQL injection attacks by ensuring that user input is properly formatted for safe use in SQL queries.
            Using the $title, $location, $rental_price, $bedrooms, $tenancy_length, $description
            The $db_connection holds the connection object to the MySQL database.*/
            $title = mysqli_real_escape_string($db_connection, $_POST['title']);
            $location = mysqli_real_escape_string($db_connection, $_POST['location']);
            $rental_price = floatval($_POST['rental_price']);
            $bedrooms = intval($_POST['bedrooms']);
            $tenancy_length = intval($_POST['tenancy_length']);
            $description = mysqli_real_escape_string($db_connection, $_POST['description']);

// Handle file uploads (photos)
$upload_dir = "property_photos/"; // creating upload_dir variable and declaring the folder where the images should be stored
$photo_paths = []; // creating an empty array called photo_paths for the path of the images 

if (!empty($_FILES['photos']['name'][0])) { // if statement to check if the files were uploaded 

    $num_files = count($_FILES['photos']['name']); // starting the count for the limit of photos
    $allowed_count = 5; // declaring the maximum of photos allowed to upload 

    for ($i = 0; $i < $num_files; $i++) { // for loop to iterate over each one of the 5 photos at maximum 
        if ($i >= $allowed_count) { // if statement when the index is greater or equal to 5 
            break; //sStop processing if we exceed the maximum allowed photos
        }

        $file_name = $_FILES['photos']['name'][$i]; 
        $tmp_name = $_FILES['photos']['tmp_name'][$i];
        $target_file = $upload_dir . basename($file_name);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $target_file = $upload_dir . $file_name;

        $check = getimagesize($tmp_name); // check if the file is an image 
        if ($check !== false) { // if its an image 
            if (move_uploaded_file($tmp_name, $target_file)) { // if statement to move the photo to folder declared 
                $photo_paths[] = $file_name; // storing the file_name in the array  
            } else { // otherwise display an error message 
                $errorMessage = 'Failed to move uploaded file.';
            }
        } else { // if its not an image , display the error 
            $errorMessage = 'Invalid file format - only images are allowed.';
        }
    }
}

if (empty($errorMessage)) { // if there's no errors 
    // preparing the query to be executed in the stmt variable 
    $stmt = $db_connection->prepare("INSERT INTO properties (title, location, eircode, rental_price, bedrooms, tenancy_length, description, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // binding the parameters to avoid SQL injection
    $stmt->bind_param("sssdiiss", $title, $location, $eircode, $rental_price, $bedrooms, $tenancy_length, $description, $photo_paths_str);

    // combining photo paths into a single string
    $photo_paths_str = implode(",", $photo_paths);

    if ($stmt->execute()) { // if the query is executed display the succesfull message 
        $successMessage = "Property registered successfully.";
    } else { // otherwise display the error message 
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close(); // close the execution of the query 
}
}
} else {
$errorMessage = implode("<br>", $errors); // displaying the error messages if validation fails
}
}

$db_connection->close(); // closing the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to add the Property-->
    <title>Property Listing - Hynesdrade</title>
    <!-- adding some CSS -->
    <style>
        /* applying CSS to the body of the page */
        body {
            background-color: rgb(244, 244, 244); /* applying the backgroung color of the body */
            font-family: "Roboto", sans-serif; /* applying the font-family of the body*/
            margin: 80px 0; /* applying 80 px as the margin of the body */
            padding: 0; /* applying 0 as padding  */
            display: flex; /* applying flex as display*/
            justify-content: center; /* applying center as the justify content */
            align-items: center; /* applying center as the align items */
            flex-direction: column; /* applying column as the flex direction */
            height: 100vh; /* applying  100vh as the height */
        }

        /* applying CSS to the class register-box of the page */
        .register-box {
            width: 600px; /* applying  600px as the width*/
            background-color: white; /* applying white as the background color */
            padding: 20px; /* applying  20px as the padding */
            border-radius: 8px;/* applying  8px as the border-radius */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* applying the box-shadow */
            display: flex; /* applying flex as a display */
            flex-direction: column;/* applying colum as the flex direction */
            align-items: center; /* applying  center as the align-items */
        }

        /* applying CSS to the input of the page where it is text, email, password, number and textarea */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"],select, textarea {
            width: 100%; /* applying 100% as the width */
            padding: 10px; /* applying 10 px as the padding */
            margin-top: 10px; /* applying 10px as the margin*/
            border-radius: 5px; /* applying 5px as the border-radius */
            border: 1px solid #ccc; /* applying  solid and the color to the border */
            box-sizing: border-box; /* applying border-box as the box-sizing */
            font-family: "Roboto", sans-serif; /* applying to the font-family */
            font-size: 16px; /* applying 16px as the font-size */
            color: grey; /* applying  grey as the color*/
        }

        /* applying CSS to the textarea  */
        textarea {
            height: 100px; /* applying  100 px as the height */
        }

        /* applying CSS to the input where it is file  */
        input[type="file"] {
            border: none; /* adding none as the border */
            margin-top: 10px; /* applying 10px as the margin-top */
        }

        /* applying CSS to the class submit-button */
        .submit-button {
            width: 100%; /* applying  100% to the width */
            padding: 10px; /* applying  10 px as pading*/
            margin-top: 20px; /* applying  20 px as margin-top*/
            background-color: #3498db; /* applying the background-color of the button */
            color: white;  /* applying  white as color */
            border: none; /* applying  none to the border */
            border-radius: 5px; /* applying  5px as the border-radius*/
            cursor: pointer; /* applying  pointer to the cursor*/
        }

        /* applying CSS to the submit.button when it hovers over */
        .submit.button:hover {
            background-color: #2980b9; /* applying  the background-color for when it hover over the button */
        }
        
        /* applying CSS to subheqading of the page  */
        h2 {
            color: #333; /* applying the color of the subheading */
            margin-bottom: 20px; /* applying  20 px as the margin-bottom*/
        }
    </style>
</head>
<body>
     <!-- including the header-->
    <header>
        <?php include $permissions['menu']; ?> <!-- including the navigation bar  -->
    </header>

    <div class="register-box">
    <h2>Register Your Property</h2> 

    <!-- if there's errors, display them in red color -->
    <?php if (!empty($errorMessage)) : ?>
        <p style="color: red;"><?php echo htmlentities($errorMessage); ?></p>
    <?php endif; ?>

    <!-- if there's no error, display the sucess message in green-->
    <?php if (!empty($successMessage)) : ?>
        <p style="color: green;"><?php echo htmlentities($successMessage); ?></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" novalidate>
        <!-- Property details form fields and adding novalidate to the form to remove client-side validation-->
        <!-- Adding the title to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list-->
        <label for="title">Title:</label>
<select id="title" name="title" required>
    <option value="">Select Property Title</option>
    <option value="Apartment" <?php if(isset($_POST['title']) && $_POST['title'] === 'Apartment') echo 'selected'; ?>>Apartment</option>
    <option value="House" <?php if(isset($_POST['title']) && $_POST['title'] === 'House') echo 'selected'; ?>>House</option>
    <option value="Studio" <?php if(isset($_POST['title']) && $_POST['title'] === 'Studio') echo 'selected'; ?>>Studio</option>
</select><br><br>

<!-- adding the location to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="location">Location:</label>
<input type="text" id="location" name="location" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required><br><br>

<!-- adding the eircode to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="eircode">Dublin Eircode (e.g: D16N230):</label>
<input type="text" id="eircode" name="eircode" value="<?php echo isset($_POST['eircode']) ? htmlspecialchars($_POST['eircode']) : ''; ?>" required><br><br>

<!-- adding the Rental Price to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="rental_price">Rental Price (€/month):</label>
<input type="number" id="rental_price" name="rental_price" value="<?php echo isset($_POST['rental_price']) ? htmlspecialchars($_POST['rental_price']) : ''; ?>" required><br><br>

<!-- adding the Number of Bedrooms: to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="bedrooms">Number of Bedrooms:</label>
<select id="bedrooms" name="bedrooms" required>
    <option value="">Select Number of Bedrooms</option>
    <option value="1" <?php if(isset($_POST['bedrooms']) && $_POST['bedrooms'] === '1') echo 'selected'; ?>>1 Bedroom</option>
    <option value="2" <?php if(isset($_POST['bedrooms']) && $_POST['bedrooms'] === '2') echo 'selected'; ?>>2 Bedrooms</option>
    <option value="3" <?php if(isset($_POST['bedrooms']) && $_POST['bedrooms'] === '3') echo 'selected'; ?>>3 Bedrooms</option>
    <option value="4" <?php if(isset($_POST['bedrooms']) && $_POST['bedrooms'] === '4') echo 'selected'; ?>>4 Bedrooms</option>
</select><br><br>

<!-- adding the Tenancy Length to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="tenancy_length">Tenancy Length (months):</label>
<select id="tenancy_length" name="tenancy_length" required>
    <option value="">Select Tenancy Length</option>
    <option value="3" <?php if(isset($_POST['tenancy_length']) && $_POST['tenancy_length'] === '3') echo 'selected'; ?>>3 Months</option>
    <option value="6" <?php if(isset($_POST['tenancy_length']) && $_POST['tenancy_length'] === '6') echo 'selected'; ?>>6 Months</option>
    <option value="12" <?php if(isset($_POST['tenancy_length']) && $_POST['tenancy_length'] === '12') echo 'selected'; ?>>1 Year</option>
</select><br><br>

<!-- adding the Description to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="description">Description:</label><br>
<textarea id="description" name="description" rows="4" cols="50" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea><br><br>

<!-- adding the Upload Photos to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<label for="photos">Upload Photos (Max 5 photos):</label>
<input type="file" id="photos" name="photos[]" accept="image/*" multiple required><br><br>

<!-- adding the location to the form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
<input type="submit" class="submit-button" name="submit_property" value="Register Property">
    </form>
</div>
<!-- including the footer in the page -->
<?php include 'footer.php'; ?>
</body>
</html>
