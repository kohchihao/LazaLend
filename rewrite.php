<?php
    $root = __DIR__."/";

    $just_url = explode("?", $_SERVER['REQUEST_URI']);

    if($just_url[0] == "/") {
        require $root."index.php";
    } elseif ($just_url[0] == "/LazaLend/item-listing") {
        require $root."item-listing.php";
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
?>