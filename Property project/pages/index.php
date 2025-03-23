<?php
ob_start(); // Start output buffering
// using the ob_start utput buffering to capture any output and prevent it from being sent to the browser

$currentPage = 'Home'; // declaring the current page as Home

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public'; 

$permissions = getUserRolePermissions($userType); // using permissions to fetch the permissions defined in the roles

function getFeaturedProperties($db_connection) // creating a function calle getFeaturedProperties that takes the databse connection as parameter 
{
    $sql = "SELECT * FROM featured_properties ORDER BY id LIMIT 3"; // sql to SELECT query to be executed 
    $result = $db_connection->query($sql); //result to execute the sql query 

    if ($result->num_rows > 0) { // if statement for when there is data in the featured_properties database
        return $result->fetch_all(MYSQLI_ASSOC); // return all the data fetched
    } else { // otherwise
        return array(); // return an empty array
    }
}

$featuredProperties = getFeaturedProperties($db_connection); // featuredProperties to fetch the the featured properties from the database

$db_connection->close(); // closing the connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Hynesdrade Dublin Property - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
       /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting background color */
            font-family: "Roboto", sans-serif; /* Setting font family */
            margin: 0; /* Resetting margin */
            padding: 0; /* Resetting padding */
            box-sizing: border-box; /* Ensuring padding and border are included in the element's total width and height */
        }

        /* Styling for the main element */
        main {
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking child elements vertically */
            align-items: center; /* Horizontally centering child elements */
            margin-top: 100px; /* Adding margin on top */
        }

        /* Styling for elements with class "intro-box" */
        .intro-box {
            width: 1000px; /* Setting width */
            background-color: white; /* Setting background color */
            margin: 20px 0; /* Adding vertical margin */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            padding: 20px 30px 40px 30px; /* Adding padding */
            box-sizing: border-box; /* Ensuring padding and border are included in the element's total width and height */
            border-radius: 8px; /* Adding rounded corners */
            text-align: center; /* Center-aligning text */
        }

        /* Styling for elements with class "property-ad" */
        .property-ad {
            width: 1000px; /* Setting width */
            height: 400px; /* Setting height */
            background-color: white; /* Setting background color */
            margin: 20px 0; /* Adding vertical margin */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding box shadow */
            display: flex; /* Using flexbox for layout */
            border-radius: 8px; /* Adding rounded corners */
            overflow: hidden; /* Hiding overflowing content */
        }

        /* Styling for elements with class "property-image" */
        .property-image {
            flex: 1; /* Taking half the space of the .property-ad */
            width: 50%; /* Setting width */
            height: 100%; /* Taking full height */
            object-fit: cover; /* Ensuring the image covers the entire container */
        }

        /* Styling for elements with class "property-info" */
        .property-info {
            flex: 1; /* Taking the remaining space */
            padding: 20px; /* Adding padding */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking child elements vertically */
            justify-content: center; /* Vertically centering child elements */
            align-items: flex-start; /* Left-aligning child elements */
            text-align: left; /* Left-aligning text */
        }

        /* Styling for h2 elements */
        h2 {
            margin-bottom: 0.5em; /* Adding margin on bottom */
        }

        /* Styling for p elements */
        p {
            margin-bottom: 1em; /* Adding margin on bottom */
        }

        /* Styling for h4 elements */
        h4 {
            margin-bottom: 1em; /* Adding margin on bottom */
        }

        /* Styling for anchor elements */
        a {
            text-decoration: none; /* Removing underline */
        }

        /* Styling for elements with class "button" */
        .button {
            display: inline-block; /* Displaying as inline-block */
            padding: 10px 20px; /* Adding padding */
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            text-decoration: none; /* Removing underline */
            border: none; /* Removing border */
            border-radius: 5px; /* Adding rounded corners */
            cursor: pointer; /* Changing cursor to pointer on hover */
            transition: background-color 0.3s, transform 0.3s; /* Adding transition effects */
            text-align: center; /* Center-aligning text */
        }

        /* Styling for elements with class "button" on hover */
        .button:hover {
            background-color: #2980b9; /* Darker blue on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

    </style>
</head>
<body>
     <!-- including the header -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>
   
    <main>
        <!--displaying the weclome message in the home page with a link to contact us and other one adverts -->
        <div class="intro-box">
            <h1>Welcome to HynesDrade</h1>
            <p>We are the number 1 platform for property listings. Whether you wish to buy or rent, HynesDrade is the perfect platform.<br> 
                Our founders, Garreth and Gustavo, have been running the platform since 2024 as part of their Server Side Development assignment.<br><br>
                Below our some of featured properties this week. These properties are highly sougth after so be sure to <a href="contact_us.php">contact us</a> quickly if you would like to arrange a viewing <br><br>
                Alternatively if you would view a list of all our partners checkout out the full list <a href="adverts.php">here</a> 
            </p>
        </div>

            <?php
            // if statamen for when the featuredProperties variable is empty 
            if (!empty($featuredProperties)) {
            foreach ($featuredProperties as $key => $property) { // foreach loop to pass this variable into property 
            $imagePath = "../images/property" . ($key + 1) . ".jpeg"; // retrieving the images from the images folders related to path established 
            ?>

        <div class="property-ad">
            <!-- dispalying the images in the home page -->
            <img src="<?php echo $imagePath; ?>" alt="<?php echo $property['property_name']; ?>" class="property-image">
            <div class="property-info">
                <!-- displaying the fileds property_name and description from the database featured_properties-->
                <h2><?php echo $property['property_name']; ?></h2>
                <p><?php echo $property['description']; ?></p>
                <td><?php echo '€' . number_format((float) str_replace(',', '', $property['price']), 0, ',', '.'); ?></td>

                <!-- if statement to check the userEmail to see the credentials -->
                <?php if ($userEmail) : ?>
                <p><a href="index_edit.php?id=<?php echo $property['id']; ?>" class="button">See more</a></p>
                <?php else : ?> <!-- else statament for when the user is not the admin-->
                <?php endif; ?>
            </div>
        </div>
    <?php
        }
    } else { // otherwise display the error mesages
        echo '<p>No featured properties available.</p>';
    }
    ?>

</main>
<!-- including the footer-->
<?php include 'footer.php'; ?>
</body>
</html>
<?php
ob_end_flush(); // Flush output buffer and send content to the browser
?>

