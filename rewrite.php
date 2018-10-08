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
    } elseif  ($just_url[0] == "/LazaLend/bids") {
        require $root."bids.php";
    } elseif  ($just_url[0] == "/LazaLend/edit-listing") {
        require $root."edit-listing.php";
    } elseif  ($just_url[0] == "/LazaLend/view-history") {
        require $root."view-history.php";
    } elseif  ($just_url[0] == "/LazaLend/delete-item") {
        require $root."delete-item.php";
    }

    if($just_url[0] == "/admin") {
        require $adminRoot."index.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/homepage") {
        require $adminRoot."homepage.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/individual-user") {
        require $adminRoot."individual-user.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/all-user") {
        require $adminRoot."all-user.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/categories") {
        require $adminRoot."categories.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/loan-item") {
        require $adminRoot."listing.php";
    } elseif  ($just_url[0] == "/LazaLend/admin/edit-listing") {
        require $adminRoot."edit-listing.php";
    }
?>