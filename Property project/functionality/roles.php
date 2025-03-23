<?php
// creating a function called getUserRolePermissions and assigning the userType as parameter
// also, defining the pages that will be accessible based on the role of public, admin, tenant and landlord
function getUserRolePermissions($userType) {
    $roles = [
        'public' => [
            'pages' => ['index.php', 'search.php', 'adverts.php', 'testimonial.php', 'contact_us.php', ],
            'menu' => '../menus/public_menu.php'
        ],
        'landlord' => [
            'pages' => ['index.php', 'search.php', 'property_listing.php', 'property_edit.php', 'adverts.php', 'inventory_details.php', 'inventory_details_edit.php', 'landlords_account.php','testimonial.php', 'testimonial_add.php', 'contact_us.php', 'password_reset.php'],
            'menu' => '../menus/landlord_menu.php'
        ],
        'tenant' => [
            'pages' => ['index.php', 'search.php', 'adverts.php', 'inventory_details.php', 'tenancy_account.php', 'testimonial.php', 'testimonial_add.php', 'contact_us.php', 'password_reset.php'],
            'menu' => '../menus/tenant_menu.php'
        ],
        'admin' => [
            'pages' => ['index.php', 'index_edit.php', 'search.php', 'property_listing.php', 'property_edit.php', 'adverts.php', 'inventory_details.php', 'inventory_details_edit.php', 'tenancy_account.php', 'tenants_account_edit.php', 'landlords_account.php', 'landlord_settings','landlords_account_edit.php', 'testimonial.php', 'testimonial_add.php', 'testimonial_manage.php', 'contact_us.php', 'contact_us_manage.php', 'password_reset.php'],
            'menu' => '../menus/admin_menu.php'
        ]
    ];

    return $roles[strtolower($userType)] ?? null; // return the userType in the roles array, and converting to lowercase to handle uppercase insertion
}


?>

