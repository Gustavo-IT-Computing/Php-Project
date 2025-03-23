<?php 
$currentPage = 'Manage Testimonial'; // declaring the current page as Manage Testimonial

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);


// Creating an if statement to check if the form data has been submitted using the POST method 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['approve'])) { // if statement to check if the approve button was posted 
        $id = $_POST['approve']; // defining id to the approve button

        echo "<script>alert('Testimonial with ID $id was approved successfully!');</script>"; // printing the success message 

    } elseif (isset($_POST['delete'])) { // else if statement for the delete button
        $id = $_POST['delete']; // defining id as delete

        $sql_delete = "DELETE FROM testimonials WHERE id = $id"; // using sql_delete for the DELETE query

        if ($db_connection->query($sql_delete) === TRUE) { // if the query is executed and equals to true

            echo "<script>alert('Testimonial with ID $id deleted successfully!');</script>"; // display an alert message for the succes in deleting 

        } else { // otherwise display the error message 
            echo "Error deleting record: " . $db_connection->error;
        }
    }
}

// Retrieve testimonials from the database
$sql = "SELECT * FROM testimonials"; // using sql for the SELECT stament 
$result = $db_connection->query($sql); // using result to execute the query

$db_connection->close(); // closing the connection
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to manage the testimonials-->
    <title>Manage Testimonials </title> 
    <style>
        /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting the background color for the entire page */
            font-family: "Roboto", sans-serif; /* Choosing the font family for text */
            padding: 0; /* Resetting padding */
            display: flex; /* Using flexbox for layout */
            justify-content: center; /* Horizontally centering content */
            align-items: center; /* Vertically centering content */
            flex-direction: column; /* Stacking flex items vertically */
            height: 100vh; /* Full viewport height */
        }

        /* Styling for elements with class "container" */
        .container {
            margin-top: 300px; /* Adding top margin */
            background-color: white; /* Background color for the container */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
        }

        /* Styling for table elements */
        table {
            width: 100%; /* Setting the width of the table */
            border-collapse: collapse; /* Collapsing borders */
            margin-top: 20px; /* Adding top margin */
        }

        /* Styling for th and td elements within tables */
        th, td {
            border: 1px solid #ddd; /* Adding border */
            padding: 12px; /* Adding padding */
            text-align: left; /* Aligning text to the left */
        }

        /* Styling specifically for th elements within tables */
        th {
            background-color: #f2f2f2; /* Background color for header cells */
            font-weight: bold; /* Setting font weight to bold */
        }

        /* Styling for elements with class "action-buttons" */
        .action-buttons {
            display: flex; /* Using flexbox for layout */
            gap: 10px; /* Adding gap between buttons */
        }

        /* Styling for elements with class "submit-button" */
        .submit-button {
            width: 100%; /* Taking full width of the parent */
            padding: 10px; /* Adding padding */
            margin-top: 20px; /* Adding top margin */
            background-color: #3498db; /* Background color for the button */
            color: white; /* Text color for the button */
            border: none; /* Removing border */
            border-radius: 5px; /* Adding rounded corners */
            cursor: pointer; /* Changing cursor to pointer */
        }

        /* Styling for the hover state of elements with class "submit-button" */
        .submit.button:hover {
            background-color: #2980b9; /* Background color change on hover */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Setting text color */
            margin-bottom: 20px; /* Adding bottom margin */
        }

    </style>
    
</head> 
<body>
      <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <!-- displaying the manage tstimonial fields in a table-->
    <div class="container">
        <h2>Manage Testimonials</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Parent's Name</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <!-- dispalying the values from the testimonial database -->
            <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['service_name']}</td>";
                        echo "<td>{$row['parent_name']}</td>";
                        echo "<td>{$row['comment']}</td>";
                        echo "<td>{$row['date']}</td>";
                        echo "<td class='action-buttons'>";
                        echo "<form method='POST'>";
                        echo "<button type='submit' class='submit-button' name='approve' value='{$row['id']}'>Approve</button>";
                        echo "<button type='submit' class='submit-button' name='delete' value='{$row['id']}'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else { // otherwise display the error message
                    echo "<tr><td colspan='6'>No testimonials found.</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
    <!-- including the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>
