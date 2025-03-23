<?php
error_reporting(E_ALL); // declaring an error_reporting method that sets the error reportinng. The E_ALLreport errors and warnings
ini_set('display_errors', 1); // declaring an ini_set function that sets the value of config ption at runtime. Assigning to 1 to display on the screen directly

// Connect to the database
// Check if we are on the live server or a local XAMPP environment 
if ($_SERVER['SERVER_NAME'] == 'knuth.griffith.ie') {
    // Path for the Knuth server 
    $path_to_mysql_connect = __DIR__ .'/../../../../mysql_connect.php';
} else {
    // Path for the local XAMPP server
    $path_to_mysql_connect = __DIR__ . '/../../../../../mysql_connect.php';
}

// Require the mysql_connect.php file using the determined path
require $path_to_mysql_connect; //Xampp connection
?>
