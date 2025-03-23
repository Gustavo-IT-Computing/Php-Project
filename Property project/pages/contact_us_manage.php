<?php 
$currentPage = 'Manage Contact'; // declaring the current page as Manage Contact

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

// Retrieve contact messages from the database
$sql = "SELECT * FROM messages ORDER BY created_at DESC"; // sql for the SELECT query
$result = $db_connection->query($sql);// using result to store the result in the function 

// Close database connection
$db_connection->close(); // closing the connection 
?>

<!DOCTYPE html> 
<html>
<head> 
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--  adding the title to manage the contact -->
    <title>Manage Contact</title> 
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
            width: 600px; /* applying  600px as the width */
            background-color: white; /* applying white as the background color */
            padding: 20px; /* applying  20px as the padding */
            border-radius: 8px; /* applying  8px as the border-radius */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* applying the box-shadow */
            display: flex; /* applying flex as a display */
            flex-direction: column; /* applying column as the flex direction */
            align-items: center; /* applying  center as the align-items */
        }

        /* Styling for table elements */
        table {
            width: 100%; /* table takes full width */
            border-collapse: collapse; /* collapse border spacing */
            margin-top: 20px; /* applying  20px margin on top */
        }

        /* Styling for table header and data cells */
        th, td {
            border: 1px solid #ddd; /* border for table cells */
            padding: 8px; /* applying  8px padding */
            text-align: left; /* align text to left */
        }

        /* Styling for table header cells */
        th {
            background-color: #f2f2f2; /* background color for table header cells */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* applying color to h2 elements */
            margin-bottom: 20px; /* applying  20px margin on bottom */
        }

        /* Styling for elements with class "button" */
        .button {
            display: inline-block; /* display as inline-block */
            padding: 10px 20px; /* applying padding */
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            text-decoration: none; /* Remove underline */
            border: none; /* Remove border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition for hover effects */
            text-align: center; /* center text */
        }

        /* Styling for elements with class "button" on hover */
        .button:hover {
            background-color: #2980b9; /* Darker blue on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

    </style>
</head> 
<body>
     <!-- include the header in the page -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>
    <div class="container">
        <h2>Contact Messages</h2>
        <?php
        // displaying the table along with the messages stored in the messages database when there is any
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>{$row['message']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else { // otherwise disply the error message 
            echo "<p>No contact messages found.</p>";
        }
        ?> 
        <!-- displaying a link to the index.ph-->
        <a href="index.php" class="button">Back to Home</a></button>
    </div>
    <!-- including the footer.php file -->
    <?php include 'footer.php'; ?>
</body>
</html>
