<?php
ob_start(); // Start output buffering
// using the ob_start utput buffering to capture any output and prevent it from being sent to the browser

$currentPage = 'Search'; // declaring the current page as Search

require_once '../functionality/session_management.php';// requiring the session_management to check if the user is logged and the role 
require_once 'connection.php'; // requiring the connection script to connect to the database
require_once 'cookies.php'; // requiring the cookies.php file

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

$cookieConsent = hasConsentedToCookies(); // using cookieConsent to check if the user has already consented to cookies

// creating an if statement for the cookieConsent, when the user clicks the button, it set the cookie and hide the banner
if (isset($_POST['cookieConsent']) && $_POST['cookieConsent'] === 'true') {
    setCookieConsent();
}

$area = isset($_GET['area']) ? $_GET['area'] : ''; // initializing the area variable adding the isset method for sanitanization
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null; // initializing the min_price variable adding the isset method for sanitanization
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null; // initializing the max_price variable adding the isset method for sanitanization
$bedrooms = isset($_GET['bedrooms']) ? (int)$_GET['bedrooms'] : null; // initializing the bedrooms variable adding the isset method for sanitanization
$tenancy_length = isset($_GET['tenancy_length']) ? (int)$_GET['tenancy_length'] : null; // initializing the tenancy_length variable adding the isset method for sanitanization

// creating an if statement for when the attributes that we have just declared are not empty
if (!empty($area) || !empty($min_price) || !empty($max_price) || !empty($bedrooms) || !empty($tenancy_length)) {
    $searchParameters = [
        'area' => $area,
        'min_price' => $min_price,
        'max_price' => $max_price,
        'bedrooms' => $bedrooms,
        'tenancy_length' => $tenancy_length
    ];
    updateSearchParametersInCookie($searchParameters); // using the updateSearchParametersInCookie to save search parameters to cookies
}

// retrieving search parameters from cookies and pre-fill search fields
$cookieParams = getSearchParametersFromCookie(); // calling the function getSearchParametersFromCookie
$area = isset($cookieParams['area']) ? $cookieParams['area'] : ''; // passing the cookies and saved parameters to the area field
$min_price = isset($cookieParams['min_price']) ? $cookieParams['min_price'] : ''; // passing the cookies and saved parameters to the min_price field
$max_price = isset($cookieParams['max_price']) ? $cookieParams['max_price'] : ''; // passing the cookies and saved parameters to the max_price field
$bedrooms = isset($cookieParams['bedrooms']) ? $cookieParams['bedrooms'] : ''; // passing the cookies and saved parameters to the bedrooms field
$tenancy_length = isset($cookieParams['tenancy_length']) ? $cookieParams['tenancy_length'] : ''; // passing the cookies and saved parameters to the tenancy_length field

$sql = "SELECT * FROM properties WHERE 1"; // using sql for the SELECT query
$params = array(); // using params to store the array

// appending the conditions based on provided search criteria
if (!empty($area)) {
    $sql .= " AND eircode LIKE ?";
    $params[] = '%' . $area . '%';
}
if (!empty($min_price)) {
    $sql .= " AND rental_price >= ?";
    $params[] = $min_price;
}
if (!empty($max_price)) {
    $sql .= " AND rental_price <= ?";
    $params[] = $max_price;
}
if (!empty($bedrooms)) {
    $sql .= " AND bedrooms = ?";
    $params[] = $bedrooms;
}
if (!empty($tenancy_length)) {
    $sql .= " AND tenancy_length >= ?";
    $params[] = $tenancy_length;
}

$stmt = $db_connection->prepare($sql); // using the stmt to prepare the SQL statement that will compile the SQL query 

// the params are not empty
if (!empty($params)) {
    $types = str_repeat('s', count($params)); // Assuming all parameters are strings
    $stmt->bind_param($types, ...$params); // using checkExistingPropertyStmt to to bind the parameters to avoid SQL injection
}

$stmt->execute();  // using stmt to execute the prepared statement 
$result = $stmt->get_result();  // using result to get the result in the function
$stmt->close(); // closing the query

$db_connection->close(); // closing the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- giving the title of property search-->
    <title>Property Search</title>
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
            height: 100vh; /* Full viewport height */
        }
        /* Styling for input and select elements */
        input, select {
            width: 100%; /* Taking full width of the parent minus padding */
            padding: 10px; /* Adding padding inside input fields and select */
            margin-top: 10px; /* Adding margin space on top */
            border-radius: 5px; /* Adding rounded corners to input fields and select */
            border: 1px solid #ccc; /* Adding border to input fields and select */
            box-sizing: border-box; /* Including padding and border in the width calculation */
        }

        /* Styling for elements with class "search-button" */
        .search-button {
            width: 100%; /* Taking full width of the parent */
            padding: 10px; /* Adding padding inside the search button */
            margin-top: 20px; /* Adding margin space on top */
            background-color: #3498db; /* Background color for the search button */
            color: white; /* Text color for the search button */
            border: none; /* Removing border from the search button */
            border-radius: 5px; /* Adding rounded corners to the search button */
            cursor: pointer; /* Changing cursor to pointer on hover */
        }

        /* Styling for button elements on hover */
        button:hover {
            background-color: #2980b9; /* Background color change on hover for buttons */
        }

        /* Styling for h2 elements */
        h2 {
            color: #333; /* Setting color for h2 elements */
            margin-bottom: 20px; /* Adding margin space below h2 elements */
        }
        .property-search {
            width: 400px; /* Setting the width of the login box */
            height: 600px; /* Setting the height of the login box */
            background-color: white; /* Background color for the login box */
            padding: 20px; /* Adding padding inside the login box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the login box */
            display: flex; /* Using flexbox for layout */
            flex-direction: column; /* Stacking flex items vertically */
            align-items: center; /* Horizontally centering flex items */
            justify-content: center;
            margin-right: 10px;
        }


        /* Styling for elements with class "search-results" */
        .search-results {
            width: 400px; /* Setting the width of the login box */
            height: 600px; /* Setting the height of the login box */
            background-color: white; /* Background color for the login box */
            padding: 20px; /* Adding padding inside the login box */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 8px; /* Adding rounded corners to the login box */
            display:<?php echo (isset($_GET['area']) || isset($_GET['min_price']) || isset($_GET['max_price']) || isset($_GET['bedrooms']) || isset($_GET['tenancy_length'])) ? 'block' : 'none'; ?>; /* Dynamic display based on condition */
            align-items: center; /* Horizontally centering flex items */
            justify-content: center;
            overflow: auto;
            margin-left: 10px;

        }

        /* Styling for elements with class "cookie-banner" */
        .cookie-banner {
            width: 100%; /* Taking full width of its parent */
            background-color: #f2f2f2; /* Background color for the cookie banner */
            padding: 20px; /* Adding padding inside the cookie banner */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adding shadow effect */
            border-radius: 0; /* Setting border radius */
            display: <?php echo $cookieConsent ? 'none' : 'flex'; ?>; /* Dynamic display based on condition */
            flex-direction: row; /* Aligning items in a row */
            justify-content: space-between; /* Distributing space between items */
            align-items: center; /* Vertically centering items */
            position: fixed; /* Positioning the banner */
            top: 0; /* Positioning from the top */
            left: 0; /* Positioning from the left */
            z-index: 1000; /* Setting z-index to ensure it appears on top */
        }

        /* Styling for elements within the cookie banner */
        .cookie-banner p {
            margin: 0; /* Resetting margin */
        }

        .cookie-banner button {
            background-color: #3498db; /* Background color for buttons */
            color: white; /* Text color for buttons */
            border: none; /* Removing border from buttons */
            border-radius: 5px; /* Adding rounded corners to buttons */
            cursor: pointer; /* Changing cursor to pointer on hover */
            padding: 10px 20px; /* Adding padding inside buttons */
        }

        .cookie-banner button:hover {
            background-color: #2980b9; /* Background color change on hover for buttons */
        }

    </style>

</head>
<body>
    <!-- including the header  -->
    <header>
        <?php include $permissions['menu'];?>
    </header>

    <!-- if statement for when the user doesn't have consent of the cookies-->
    <?php if (!hasConsentedToCookies()) : ?>
    <form id="cookie-consent-form" method="post" style="display: none;" novalidate>
        <input type="hidden" name="cookieConsent" value="true">
    </form>

    <!-- dipalying the cookies banner -->
    <section class="cookie-banner">
        <div>This site uses cookies. By continuing to use this site, you consent to the use of cookies. 
        <button onclick="setCookieConsent()">I Understand</button></div>
    </section>
    <?php endif; ?>

    <!--displaying the forn for searching the property -->
    <section class="property-search">
     <h2>Search Properties</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" novalidate>
        <label for="area">Area (Eircode prefix):</label>
        <input type="text" id="area" name="area" value="<?= htmlentities($area) ?>"><br><br>

        <label for="min_price">Minimum Price:</label>
        <input type="number" id="min_price" name="min_price" value="<?= htmlentities($min_price) ?>"><br><br>

        <label for="max_price">Maximum Price:</label>
        <input type="number" id="max_price" name="max_price" value="<?= htmlentities($max_price) ?>"><br><br>

        <label for="bedrooms">Number of Bedrooms:</label>
        <input type="number" id="bedrooms" name="bedrooms" value="<?= htmlentities($bedrooms) ?>"><br><br>

        <label for="tenancy_length">Tenancy Length (months):</label>
        <input type="number" id="tenancy_length" name="tenancy_length" value="<?= htmlentities($tenancy_length) ?>"><br><br>

        <input type="submit" class="search-button" value="Search">
    </form>
</section>

<!-- displaying the search results -->
<section class="search-results">
    <?php if ($result && $result->num_rows > 0) : ?>
        <h2>Search Results:</h2>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="property">
                <h3><?= htmlentities($row['title']) ?></h3>
                <p>Location: <?= htmlentities($row['location']) ?></p>
                <p>Rental Price: €<?= number_format($row['rental_price'], 2) ?>/month</p>
                <p>Bedrooms: <?= htmlentities($row['bedrooms']) ?></p>
                <p>Tenancy Length: <?= htmlentities($row['tenancy_length']) ?> months</p>
                <p>Eircode: <?= htmlentities($row['eircode']) ?></p>
            </div>
        <?php endwhile; ?>

        <!-- otherwise display the error message-->
    <?php else : ?>
        <p>No properties found matching the search criteria.</p>
    <?php endif; ?>
    
</section>

<!-- We decided to use Javascript for the cookies banner pop up, so when teh user accpets the cookies clicking on the Understand
button, the banners disappears and the user can search for the page-->
<script>
     function setCookieConsent() { // creating a function called setCookieConsent
        document.cookie = "cookie_consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/"; // Cookie valid for a long time
        document.querySelector(".cookie-banner").style.display = "none"; // Hide the banner after setting the cookie
    }
</script>
</body>
</html>
<?php
ob_end_flush(); // Flush output buffer and send content to the browser
?>

