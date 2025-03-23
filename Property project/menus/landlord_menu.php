<?php

// adding error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../functionality/session_management.php'; // requiring the roles.php inside the functionality folder

// Define current page for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF']); // Get the current page filename

// Check if user is logged in and retrieve user information
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// Get user role permissions for 'landlord' role from roles.php
$userPermissions = getUserRolePermissions('landlord');

// List of pages accessible to the landlord
$allowedPages = $userPermissions['pages'] ?? [];

// Helper function to extract page name from URL
function getPageName($url) {
    $path_parts = pathinfo($url);
    return $path_parts['filename'];
}

// Handle logout form submission
if (isset($_POST['logout'])) {
    // Redirect to the logout script
    header('Location: ../pages/logout.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--giving the title  for the landlord page -->
    <title> Hynesdrade Dublin Property - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
      <style>
       /* Styling for the body element */
        body {
            background-color: rgb(244, 244, 244); /* Setting the background color */
            font-family: "Roboto", sans-serif; /* Choosing the font family */
            font-style: normal; /* Setting font style to normal */
        }

        /* Styling for the nav element */
        nav {
            position: fixed; /* Fixed positioning */
            top: 0; /* Position at the top of the viewport */
            left: 0; /* Position at the left of the viewport */
            width: 100%; /* Full width */
            height: 80px; /* Height of the navigation bar */
            background-color: #fff; /* Background color */
            display: flex; /* Using flexbox for layout */
            justify-content: space-between; /* Space between items */
            align-items: center; /* Centering items vertically */
            padding: 0 60px; /* Padding */
            box-sizing: border-box; /* Including padding and border in the width */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Adding shadow effect */
            z-index: 1000; /* Setting z-index */
        }

        /* Styling for h1 elements inside nav */
        nav h1 {
            margin: 0; /* Resetting margin */
        }

        /* Styling for ul elements inside nav */
        nav ul {
            list-style-type: none; /* Removing bullet points */
            display: flex; /* Using flexbox for layout */
            align-items: center; /* Centering items vertically */
            padding: 0; /* Resetting padding */
            margin: 0; /* Resetting margin */
            height: 100%; /* Taking full height */
        }

        /* Styling for li elements inside ul */
        nav ul li {
            position: relative; /* Positioning relative to its normal position */
            padding: 10px 20px; /* Padding */
            cursor: pointer; /* Setting cursor to pointer */
        }

        /* Styling for links inside li */
        nav ul li a {
            text-decoration: none; /* Removing underline */
            color: #333; /* Setting text color */
            display: block; /* Displaying as block element */
        }

        /* Hover effect for top-level links only */
        nav > ul > li > a:hover,
        .nav-active > a {
            color: #3498db; /* Changing text color on hover */
            border-bottom: 3px solid #3498db; /* Adding bottom border on hover */
        }

        /* Styling for dropdown */
        .dropdown {
            position: relative; /* Positioning relative to its normal position */
            display: inline-block; /* Displaying as inline-block */
        }

        /* Styling for the dropbtn */
        .dropbtn {
            background-color: #3498db; /* Background color */
            color: white; /* Text color */
            padding: 10px; /* Padding */
            font-size: 16px; /* Font size */
            border: none; /* Removing border */
            cursor: pointer; /* Setting cursor to pointer */
            border-radius: 4px; /* Adding rounded corners */
        }

        /* Styling for dropdown content */
        .dropdown-content {
            display: none; /* Hiding dropdown content by default */
            position: absolute; /* Absolute positioning */
            background-color: #f9f9f9; /* Background color */
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Adding shadow effect */
            z-index: 1; /* Setting z-index */
            left: 50%; /* Centering horizontally */
            transform: translateX(-50%); /* Perfect center alignment */
            min-width: 160px; /* Minimum width */
        }

        /* Styling for dropdown content links */
        .dropdown-content a {
            color: black; /* Text color */
            padding: 12px 16px; /* Padding */
            text-decoration: none; /* Removing underline */
            display: block; /* Displaying as block element */
            text-align: left; /* Aligning text to the left */
        }

        /* Styling for hover effect on dropdown content links */
        .dropdown-content a:hover {
            background-color: #f1f1f1; /* Background color on hover */
        }

        /* Displaying dropdown content on hover */
        .dropdown:hover .dropdown-content {
            display: block; /* Displaying dropdown content on hover */
        }

        /* Changing dropbtn background color on hover */
        .dropdown:hover .dropbtn {
            background-color: #2980b9; /* Background color on hover */
        }

        /* Styling for logout button */
        #logout {
            background-color: red; /* Background color */
            color: white; /* Text color */
            padding: 10px; /* Padding */
            font-size: 16px; /* Font size */
            border: none; /* Removing border */
            cursor: pointer; /* Setting cursor to pointer */
            border-radius: 4px; /* Adding rounded corners */
        }

    </style>
 
</head>
<body>
    <header>
        <nav>
            <h1>HYNESDRADE</h1>
            <ul>
                <li class="<?php echo ($currentPage == 'Home') ? 'nav-active' : ''; ?>"><a href="../pages/index.php">Home</a></li>
                <li class="<?php echo ($currentPage == 'Search') ? 'nav-active' : ''; ?>"><a href="../pages/search.php">Search</a></li>
                <li class="<?php echo ($currentPage == 'Adverts') ? 'nav-active' : ''; ?>"><a href="../pages/adverts.php">Adverts</a></li>

                <!-- Listings Dropdown -->
                <li class="dropdown <?php echo ($currentPage == 'Property Listing' || $currentPage == 'Edit Listings') ? 'nav-active' : ''; ?>">
                    <a>Listings</a>
                    <div class="dropdown-content">
                        <a href="../pages/property_listing.php">Property Listings</a>
                        <a href="../pages/property_edit.php">Edit Listings</a>
                    </div>
                </li>
                
                <!-- Inventory Dropdown -->
                <li class="dropdown <?php echo ($currentPage == 'Inventory Details' || $currentPage == 'Inventory Edit') ? 'nav-active' : ''; ?>">
                    <a>Inventory</a>
                    <div class="dropdown-content">
                        <a href="../pages/inventory_details.php">Inventory Details</a>
                        <a href="../pages/inventory_details_edit.php">Edit Inventory</a>
                    </div>
                </li>
                
                <!-- Testimonials Dropdown -->
                <li class="dropdown <?php echo ($currentPage == 'Testimonial' || $currentPage == 'Testimonial Add') ? 'nav-active' : ''; ?>">
                    <a>Testimonials</a>
                    <div class="dropdown-content">
                        <a href="../pages/testimonial.php">Testimonial</a>
                        <a href="../pages/testimonial_add.php">Add Testimonial</a>
                    </div>
                </li>

                <!-- Account Dropdown -->
                <li class="dropdown <?php echo ($currentPage == 'Landlord Account'|| $currentPage == 'Landlord Setings') ? 'nav-active' : ''; ?>">
                    <a>Account</a>
                    <div class="dropdown-content">
                        <a href="../pages/landlords_account.php">Landlord Account</a>
                    </div>
                </li>

                <li class="<?php echo ($currentPage == 'Contact Us') ? 'nav-active' : ''; ?>"><a href="../pages/contact_us.php">Contact Us</a></li>

                <li class="<?php echo ($currentPage == 'Password Reset') ? 'nav-active' : ''; ?>"><a href="../pages/password_reset.php">Password</a></li>
            </ul>

            <div class="dropdown">
                <?php if ($userEmail) : ?>
                    <!-- Display user email and logout button if logged in -->
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                    <button type="submit" name="logout" class="dropbtn">Landlord Logout (<?php echo $userEmail; ?>)</button>
                    </form>
                <?php else : ?>
                    <!-- Display login button if not logged in -->
                    <a href="../pages/login.php"><button class="dropbtn">Login</button></a>
                    <a href="../pages/register.php"><button class="dropbtn">Register</button></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

</body>
</html>
