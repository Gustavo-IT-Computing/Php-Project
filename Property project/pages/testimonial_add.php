<?php 
$currentPage = 'Add Testimonial'; // declaring the current page as Add Testimonial

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$service_name = $parent_name = $comment = $date = ''; // initializing the service_name variable to store data
$errors = array(); // initializing the errors as array

// Creating an if statement to check if the form data has been submitted using the POST method 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate form data
    // Sanitization for the service_name variable 
    if(isset($_POST['service_name'])) {
        $service_name = trim($_POST['service_name']);
        if (empty($service_name)) { // if empty, display the error message and store in the array 
            $errors['service_name'] = 'Service name is required';
        }
    }

    // Sanitization for the parent_name variable
    if(isset($_POST['parent_name'])) {
        $parent_name = trim($_POST['parent_name']);
        if (empty($parent_name)) { // if empty, display the error message and store in the array 
            $errors['parent_name'] = 'Parent name is required';
        }
    }

    // Sanitization for the comment variable
    if(isset($_POST['comment'])) {
        $comment = trim($_POST['comment']);
        if (empty($comment)) { // if empty, display the error message and store in the array 
            $errors['comment'] = 'Comment is required';
        }
    }

    // Sanitize and validate the date field
    $date = $_POST['date'];
    if (empty($date)) { // if empty, display the error message and store in the array 
        $errors['date'] = 'Date is required';
    } else {
        // Validate the date format
        if (!strtotime($date)) {
            $errors['date'] = 'Invalid date format';
        }
    }

    // if statement for when the errors arrays is empty
    if (empty($errors)) { 

        $sql = "INSERT INTO testimonials (service_name, parent_name, comment, date) VALUES (?, ?, ?, ?)"; // using sql for the INSERT query
        $stmt = $db_connection->prepare($sql);  // using the stmt to prepare the SQL statement that will compile the SQL query 
        $stmt->bind_param('ssss', $service_name, $parent_name, $comment, $date); // using stmt to to bind the parameters to avoid SQL injection
        if ($stmt->execute()) { // if statement to execute the prepared statement 
            // Display success message
            echo "<p>Testimonial submitted successfully!</p>";
        } else {
            // Display error message
            echo "Error: " . $sql . "<br>" . $stmt->error;
        }
        $stmt->close(); // closing the statement 
    }
    }

$db_connection->close(); // closing the connection
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to add the testimonial-->
    <title> Add Testimonial </title> 
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

        /* Styling for elements with class "testimonial-box" */
        .testimonial-box {
            width: 400px; /* Setting the width of the testimonial box */
            background-color: white; /* Background color for the testimonial box */
            padding: 20px; /* Adding padding inside the testimonial box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the testimonial box */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for input, date, and textarea elements */
        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%; /* Taking full width of the parent */
            padding: 10px; /* Adding padding inside input fields and textarea */
            margin-top: 10px; /* Adding margin space on top */
            border-radius: 5px; /* Adding rounded corners to input fields and textarea */
            border: 1px solid #ccc; /* Adding border to input fields and textarea */
            box-sizing: border-box; /* Including padding and border in the width calculation */
            font-family: "Roboto", sans-serif; /* Ensuring consistent font */
            font-size: 16px; /* Setting font size */
        }

        /* Styling specifically for textarea elements */
        textarea {
            height: 100px; /* Setting height of textarea */
            resize: none; /* Preventing resizing of the textarea */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Taking full width of the parent */
            padding: 10px; /* Adding padding inside the button */
            margin-top: 20px; /* Adding margin space on top */
            background-color: #3498db; /* Background color for the button */
            color: white; /* Text color for the button */
            border: none; /* Removing border from the button */
            border-radius: 5px; /* Adding rounded corners to the button */
            cursor: pointer; /* Changing cursor to pointer */
        }

        /* Styling for the hover state of elements with class "submit-button" */
        .submit-button:hover {
            background-color: #2980b9; /* Background color change on hover */
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

    <!-- displaying the add testimonial form and also inserting the isset and htmlspecialchars function and the required attribute on the list -->
    <div class="testimonial-box">
        <h2>Add Testimonial</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
            <input type="text" name="service_name" placeholder="Service Name" value="<?php echo htmlentities($service_name); ?>" required>
            <?php if(isset($errors['service_name'])) echo '<p style="color:red;">' . $errors['service_name'] . '</p>'; ?>
            
            <input type="text" name="parent_name" placeholder="Name" value="<?php echo htmlentities($parent_name); ?>" required>
            <?php if(isset($errors['parent_name'])) echo '<p style="color:red;">' . $errors['parent_name'] . '</p>'; ?>
            
            <textarea name="comment" placeholder="Your Comment" required><?php echo htmlentities($comment); ?></textarea>
            <?php if(isset($errors['comment'])) echo '<p style="color:red;">' . $errors['comment'] . '</p>'; ?>
            
            <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
            <?php if(isset($errors['date'])) echo '<p style="color:red;">' . $errors['date'] . '</p>'; ?>
            
            <button type="submit" class="submit-button">Submit Testimonial</button>
        </form>
    </div>

    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>
