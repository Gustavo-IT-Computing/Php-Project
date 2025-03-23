<?php
$currentPage = 'Contact Us'; // declaring the current page as Contact Us

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$errors = []; // Initialize an array to store errors

function sanitizeInput($data) { // creating a fucntion to sanitize the inputs 
    return htmlspecialchars(trim($data)); // return the data trimmed and with the htmlspecialchars method
}

function validateName($name) { // creating a function to validate the name 
    global $errors; // declaring a global variable called errors
    if (empty($name)) { // if statement for when the name is empty to display the error message 
        $errors[] = "Please write your name";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) { // pregmatch to just accpet letters and not numbers 
        $errors[] = "The name is invalid! Please use only letters.";
    }
}

// Function to validate email
function validateEmail($email) { // creating a fucntion called validateEmail for the email
    global $errors; // calling the global variable 
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { // if statement for when the email is empty or it is wrong to display the error message 
        $errors[] = "Please provide a valid email address";
    }
}

// Function to validate phone number
function validatePhone($phone) { // creating a fucntion called validatePhone for handling the phone 
    global $errors; // calling the global variable 
    // creating an else if statement when the $mobile doesn't follow the regular expression (extracted from the lecture material) that checks for only accepting 10 digit numbers
    if (!empty($phone) && !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Please enter a valid 10-digit phone number";
    }
}

// Function to validate message
function validateMessage($message) { // creating a fucntion called validateMessage for the message 
    global $errors; // calling the global variable 
    if (empty($message)) { // if messages is empty, display the error
        $errors[] = "Please write your message";
    }
}

// Creating an if statement to check if the form data has been submitted using the POST method;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';  //check the post for the name
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : ''; // check the post for the email
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : ''; // check the post for the phone
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : ''; // check the post for the message

    validateName($name); // validating the function validateName
    validateEmail($email); // validating the function validateEmail
    validatePhone($phone); // validating the function validatePhone
    validateMessage($message); // validating the function validateMessage

    // Insert message into database if there are no errors
    if (empty($errors)) { // if statement for when the errors variable is empty
        $sql = "INSERT INTO messages (name, email, phone, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())"; // sql query for the INSERT 
        $stmt = $db_connection->prepare($sql); // using the stmt to prepare the SQL statement that will compile the SQL query 
        $stmt->bind_param("ssss", $name, $email, $phone, $message); // using stmt to to bind the parameters to avoid SQL injection
        
        if ($stmt->execute()) { // using stmt to execute the prepared statement  and display the success message 
            $successMessage = "Message submitted successfully. We will get back to you soon.";
        } else {// otherwise display the error message 
            $errorMessage = "Error: " . $sql . "<br>" . $db_connection->error;
        }

        $stmt->close(); // closing the execution of the INSERT query
    }
    
    $db_connection->close(); // closing the connection 
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to Contac Us-->
    <title>Contact Us</title>
    <style>
        /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* applying the background color of the body */
            font-family: "Roboto", sans-serif; /* applying the font-family of the body */
            margin: 0; /* applying 0 px as the margin of the body */
            padding: 0; /* applying 0 as padding */
            display: flex; /* applying flex as display */
            justify-content: center; /* applying center as the justify content */
            align-items: center; /* applying center as the align items */
            flex-direction: column; /* applying column as the flex direction */
            height: 100vh; /* applying  100vh as the height */
        }

        /* Styling for elements with class "contact-box" */
        .contact-box {
            width: 400px; /* setting width to 400px */
            background-color: white; /* setting background color to white */
            padding: 20px; /* applying 20px padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* applying box shadow */
            border-radius: 8px; /* applying border radius */
            display: flex; /* applying flex as display */
            flex-direction: column; /* applying column as the flex direction */
            align-items: center; /* centering children horizontally */
        }

        /* Styling for input and textarea elements */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%; /* taking full width */
            padding: 10px; /* applying 10px padding */
            margin-top: 10px; /* applying 10px margin on top */
            border-radius: 5px; /* applying border radius */
            border: 1px solid #ccc; /* applying border */
            box-sizing: border-box; /* including padding and border in the width */
            font-family: "Roboto", sans-serif; /* Ensures consistent font */
            font-size: 16px; /* Optional: Ensures consistent font size */
        }

        /* Styling for textarea element */
        textarea {
            height: 100px; /* Sufficient space for a message */
            resize: none; /* Prevent resizing the textarea */
        }

        /* Styling for elements with class "send-button" */
        .send-button {
            width: 100%; /* taking full width */
            padding: 10px; /* applying 10px padding */
            margin-top: 20px; /* applying 20px margin on top */
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            border: none; /* Remove border */
            border-radius: 5px; /* applying border radius */
            cursor: pointer; /* Pointer cursor on hover */
        }

        /* Styling for elements with class "send-button" on hover */
        .send-button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* applying color to h2 elements */
            margin-bottom: 20px; /* applying 20px margin on bottom */
        }

        /* Styling for elements with class "alert" */
        .alert {
            padding: 10px; /* applying 10px padding */
            margin-top: 20px; /* applying 20px margin on top */
            border-radius: 5px; /* applying border radius */
        }

        /* Styling for elements with class "error-message" */
        .error-message {
            color: red; /* applying red color */
        }

        /* Styling for elements with class "success-message" */
        .success-message {
            color: green; /* applying green color */
        }

    </style>
</head>
<body>
    <!-- including the header-->
<header>
    <?php include $permissions['menu']; ?>
</header>

<div class="contact-box">
    <h2>Contact Us</h2>
    <!-- creating a form to display the contact fields to the user to fill and performing the sanitanization of the variables -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate >
        <input type="text" name="name" placeholder="Your Name" value="<?php echo isset($name) ? $name : ''; ?>" required>
        <input type="email" name="email" placeholder="Your Email" value="<?php echo isset($email) ? $email : ''; ?>" required>
        <input type="tel" name="phone" placeholder="Your Phone Number" value="<?php echo isset($phone) ? $phone : ''; ?>">
        <textarea name="message" placeholder="Your Message" required><?php echo isset($message) ? $message : ''; ?></textarea>
        <button type="submit" class="send-button">Send Message</button>
    </form>

    <!-- displaying the errors variable in red -->
    <?php if (!empty($errors)) : ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- displaying the success message in green-->
    <?php elseif (isset($successMessage)) : ?>
        <div class="success-message"><?php echo $successMessage; ?></div>

        <!-- diplaying the errorMessage -->
    <?php elseif (isset($errorMessage)) : ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
</div>
<!-- including the footer.php file -->
<?php include 'footer.php'; ?>
</body>
</html>