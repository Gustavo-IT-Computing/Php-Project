<?php
ob_start(); // Start output buffering
// using the ob_start utput buffering to capture any output and prevent it from being sent to the browser

$currentPage = 'Testimonial'; // declaring the current page as Property Listing

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

function getTestimonials() { // creating a function to fetch testimonials from the database
global $db_connection; // defining db_connection as our global

    $sql = "SELECT * FROM testimonials"; // using sql for the SELECT query
    $stmt = $db_connection->prepare($sql); // using the stmt to prepare the SQL statement that will compile the SQL query 
    $stmt->execute(); // using stmt to execute the prepared statement 
    $stmt->bind_result($id, $service_name, $parent_name, $comment, $date); // using stmt to to bind the parameters to avoid SQL injection

// Fetch testimonials as an associative array
$testimonials = array();
while ($stmt->fetch()) {
    $testimonials[] = array(
        'id' => $id,
        'service_name' => $service_name,
        'parent_name' => $parent_name,
        'comment' => $comment,
        'date' => $date
    );
}

return $testimonials; // return the testimonials 
}

// Fetch testimonials from the database
$testimonials = getTestimonials();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title to testimonials -->
    <title>Testimonials</title> 
    <style>
        /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting the background color */
            font-family: "Roboto", sans-serif; /* Choosing the font family */
            margin-top: 350px; /* Assigning 350px for the margin */
            padding: 0; /* Resetting padding */
            display: flex; /* Using flexbox for layout */
            justify-content: center; /* Horizontally centering content */
            align-items: center; /* Vertically centering content */
            flex-direction: column; /* Stacking flex items vertically */
            height: 100vh; /* Full viewport height */
        }

        /* Styling for the main element */
        main {
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Centering the child elements */
            margin-top: 100px; /* Adding top margin */
        }

        /* Styling for elements with class "advert-ad" */
        .advert-ad {
            width: 100%; /* Adjusting width to fill the entire container */
            background-color: white; /* Background color */
            margin: 20px 0; /* Adding vertical spacing between ads */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners */
            overflow: hidden; /* Hiding overflowing content */
            display: table; /* Changing display property to table */
            border-collapse: collapse; /* Collapsing borders between cells */
        }

        .advert-info {
            display: table-cell; /* Making the info div behave like a table cell */
            padding: 20px; /* Adding padding */
            text-align: left; /* Aligning text to the left */
            vertical-align: middle; /* Centering content vertically */
            width: 50%; /* Setting width */
            border-right: 1px solid #ccc; /* Adding border between image and info */
        }

        .advert-image {
            display: table-cell; /* Making the image div behave like a table cell */
            width: 50%; /* Setting width */
            border-right: 1px solid #ccc; /* Adding border between image and info */
        }

        .advert-image img {
            display: block; /* Ensuring the image is a block element */
            width: 100%; /* Making the image fill its container */
            height: auto; /* Ensuring aspect ratio is maintained */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 0.5em; /* Smaller space after headings */
        }

        /* Styling for p elements */
        p {
            margin-bottom: 1em; /* Adequate space after paragraphs */
        }

        /* Styling for h4 elements */
        h4 {
            margin-bottom: 1em; /* Space before the button */
        }

        /* Styling for links */
        a {
            text-decoration: none; /* Removing underline */
        }

        /* Styling for elements with class "button" */
        .button {
            display: inline-block; /* Ensuring the button behaves like a block element with padding */
            padding: 10px 20px; /* Adding padding */
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            text-decoration: none; /* Remove underline */
            border: none; /* Removing border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition for hover effects */
            text-align: center; /* Centering text */
        }

        /* Styling for the hover state of elements with class "button" */
        .button:hover {
            background-color: #2980b9; /* Darker blue on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

</style>
</head>
<body>
     <!-- including the header -->
    <header>
        <?php include $permissions['menu'];?>
    </header>

    <main>

    <!-- displaying the testimonials in a table format-->
    <table>
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="advert-ad">
                <div class="advert-info">
                    <h2><?php echo htmlspecialchars($testimonial['service_name']); ?></h2>
                    <p><strong>Parent Name:</strong> <?php echo htmlspecialchars($testimonial['parent_name']); ?></p>
                    <p><?php echo htmlspecialchars($testimonial['comment']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($testimonial['date']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        </table>
    </main>
    <!-- inclsuing the footer-->
    <?php include 'footer.php'; ?>
</body>
</html>
<?php
ob_end_flush(); // Flush output buffer and send content to the browser
?>
