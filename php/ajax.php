<?php 
    $root = __DIR__."/../";

    switch ($_POST['action']) {
        case 'showLoanImage':
            require_once $root."template/template_jy.php";

            $html = image_selected($_POST['image_id'], $_POST['image_url']);

            echo json_encode($html);
        break;

        case 'removeLoanImage':
            require_once $root."template/template_jy.php";

            $html = no_image_selected($_POST['image_id']);

            echo json_encode($html);
        break;
    }