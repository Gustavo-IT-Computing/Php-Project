<?php
session_start(); // Start the session 

require 'roles.php'; // requiring the roles.php page for the credentials regarding each role

// Check if the user is logged in and a role is set in the session
if (isset($_SESSION['userRole']) && isset($_SESSION['userRole'])) {
    
    $userRole = $_SESSION['userRole']; // Use the role from the session if available
    
} else { // otherwise
    
    $userRole = ''; 
    $_SESSION['userType'] = $userRole; // defining the userTupe ad the userType
}

?> 



