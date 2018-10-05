<?php
$adminRoot = __DIR__ . "/../";
$root = __DIR__ . "/../../";
require_once $root . "cfg.php";
require_once $adminRoot . "./template/template.php";

switch ($_POST['action']) {
    case 'updatePromotedItem':
        $html = updateItemPromotedStatus($_POST['item_id'], $_POST['promoted']);
        break;

    case 'showLoanImage':
        require_once $adminRoot . "template/template_jy.php";

        $html = image_selected($_POST['image_id'], $_POST['image_url']);

        echo json_encode($html);
        break;

    case 'removeLoanImage':
        require_once $adminRoot . "template/template_jy.php";

        $html = no_image_selected($_POST['image_id']);

        echo json_encode($html);
        break;

}
