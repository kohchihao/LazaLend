<?php
    $root = __DIR__."/";
    $adminRoot = __DIR__."/admin/";

    $just_url = explode("?", $_SERVER['REQUEST_URI']);

    if($just_url[0] == "/") {
        require $root."index.php";
    } elseif ($just_url[0] == "/LazaLend/view-listing") {
        require $root."view-listing.php";
    } elseif ($just_url[0] == "/LazaLend/logout") {
        require $root."logout.php";
    } elseif ($just_url[0] == "/LazaLend/categories") {
        require $root."categories.php";
    } elseif ($just_url[0] == "/LazaLend/logout") {
        require $root."logout.php";
    } elseif ($just_url[0] == "/LazaLend/search") {
        require $root."search.php";
    } elseif  ($just_url[0] == "/LazaLend/loan-item") {
        require $root."listing.php";
    }

    if($just_url[0] == "/admin") {
        require $adminRoot."index.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/homepage") {
        require $adminRoot."homepage.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/individual-user") {
        require $adminRoot."individual-user.php";
    }
?>