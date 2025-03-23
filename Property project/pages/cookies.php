<?php 
$currentPage = 'Cookies'; // declaring the current page as Cookies

require_once '../functionality/session_management.php'; // requiring the session_management to check if the user is logged and the role 

$userType = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : 'Public';
$permissions = getUserRolePermissions($userType);

error_reporting(E_ALL); // declaring an error_reporting method that sets the error reportinng. The E_ALLreport errors and warnings
ini_set('display_errors', 1); // declaring an ini_set function that sets the value of config ption at runtime. Assigning to 1 to display on the screen directly

// Function to check if the user has given consent for cookies
function hasConsentedToCookies() {
    return isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === 'true';
}

// Function to set the cookie consent
function setCookieConsent() {
    setcookie('cookie_consent', 'true', time() + (86400 * 30), '/'); // Cookie valid for 30 days
}

// Function to save search parameters in a cookie
function saveSearchParametersToCookie($parameters) {
    setcookie('search_params', json_encode($parameters), time() + (86400 * 30), '/'); // Cookie valid for 30 days
}

// Function to retrieve search parameters from the cookie
function getSearchParametersFromCookie() {
    if (isset($_COOKIE['search_params'])) {
        return json_decode($_COOKIE['search_params'], true);
    }
    return [];
}

// Function to update search parameters in the cookie
function updateSearchParametersInCookie($parameters) {
    $existingParams = getSearchParametersFromCookie();
    $updatedParams = array_merge($existingParams, $parameters);
    saveSearchParametersToCookie($updatedParams);
}

// Function to clear search parameters from the cookie
function clearSearchParametersCookie() {
    setcookie('search_params', '', time() - 3600, '/'); // Expire cookie immediately
}

// Function to clear all cookies
function clearAllCookies() {
    setcookie('cookie_consent', '', time() - 3600, '/'); // Clear cookie consent
    setcookie('search_params', '', time() - 3600, '/'); // Clear search parameters cookie
    setcookie('property_form_data', '', time() - 3600, '/'); // Clear property form data cookie
}

?>
