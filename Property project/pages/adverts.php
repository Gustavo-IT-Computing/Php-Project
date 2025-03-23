<?php 
$currentPage = 'Adverts'; // Set the current page

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adverts</title>
        <style>
        body {
            background-color: rgb(244, 244, 244); /* applying the background color of the body */
            font-family: "Roboto", sans-serif; /* applying the font-family of the body*/
            margin: 0; /* applying 0 as margin */
            padding: 0; /* applying 0 as padding */
            box-sizing: border-box; /* applying box-sizing as border-box */
        }
        main {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the child elements (the ads) */
            margin-top: 100px;
        }
        .advert-ad {
            width: 1000px;
            height: 400px;
            background-color: white;
            margin: 20px 0; /* Adds vertical spacing between ads */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            border-radius: 8px;
            overflow: hidden;
        }

        .advert-image {
            flex: 1; /* Takes half the space of the .advert-ad */
            width: 50%;
            height: 100%;
            object-fit: contain;
        }

        .advert-info {
            flex: 1; /* Takes the remaining space */
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
        }

        h2 {
            margin-bottom: 0.5em; /* Smaller space after headings */
        }

        p {
            margin-bottom: 1em; /* Adequate space after paragraph for readability */
        }

        h4 {
            margin-bottom: 1em; /* Space before the button */
        }

        a {
            text-decoration: none; /* Remove underline */
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            text-decoration: none; /* Remove underline */
            border: none;
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition for hover effects */
            text-align: center;
        }

        .button:hover {
            background-color: #2980b9; /* Darker blue on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

</style>
</head>
<body>
     <!-- include the header in the page  -->
    <header>
        <?php include $permissions['menu']; ?>
    </header>

    <main>

        <div class="advert-ad">
             <!-- adding the image and advert refered to the Bord Gais company with a link to their homepage -->
            <img src="../images/ad1.png" class="advert-image">
            <div class="advert-info">
                <h2>Bord Gais</h2>
                <p>Whether you’re new to Bord Gáis Energy, or already with us, we have the right plan for you. Find the best one to suit your needs and sign up online.</p>
                 <a href="https://www.bordgaisenergy.ie/" target="blank" class="button">See more</a>
            </div>
        </div>

        <div class="advert-ad">
             <!--  displaying the image of an advert along with the Vodafone company and a link to its website -->
            <img src="../images/ad3.png" class="advert-image">
            <div class="advert-info">
                <h2>Vodafone</h2>
                <p>No need to switch every 12 months. We're doing things differently. At Vodafone, the price won't double after your contract ends. So say goodbye to having to shop around for broadband.</p>
                 <a href="https://n.vodafone.ie/shop/broadband.html?c_source=Google&c_medium=cpc&c_name=IE_21_AO_Performance_Fixed_Search_Mass%20Market%20__National_Fixed_Brand&gad_source=1&gclid=CjwKCAjwuJ2xBhA3EiwAMVjkVNR3IfciNR6ymK6tOjRLp6zE3P636YHREBHzd2yd6EEMQwhslDNq_xoC8gYQAvD_BwE" target="blank" class="button">See more</a>
            </div>
        </div>

        <div class="advert-ad">
             <!--  displaying the image of advert along with the Aviva Home Insurance with a link to their homepage-->
            <img src="../images/ad2.png" class="advert-image">
            <div class="advert-info">
                <h2>Aviva Home Insurance</h2>
                <p>Home Insurance Quote. We have three different types of cover to suit your needs: building insurance, contents insurance or both.</p>
                 <a href="https://www.aviva.ie/insurance/home-insurance/" target="blank" class="button">See more</a>
            </div>
        </div>
        
    </main>
     <!-- including the footer.php file  -->
    <?php include 'footer.php'; ?>
</body>
</html>
