<?php
$adminRoot = __DIR__ . "/../";
$root = __DIR__ . "/../../";
require_once $root . "cfg.php";
require_once $adminRoot . "./template/template.php";

switch ($_POST['action']) {
    case 'updatePromotedItem':
        $html = updateItemPromotedStatus($_POST['item_id'], $_POST['promoted']);
        break;

}
