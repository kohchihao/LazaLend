<?php
$root = __DIR__ . "/";
require_once $root . "cfg.php";
require_once $root . "./template/template.php";
session_start();
if (isset($_SESSION['loggedInUserId'])) {
    if (isset($_GET['id'])) {
        $item_id = $_GET['id'];
        $done = deleteItem($item_id);
        if ($done) {
          header("Location: /LazaLend");
        }
    }
}

?>