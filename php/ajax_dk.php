<?php 
    $root = __DIR__."/../";

    switch ($_POST['action']) {
        case 'getBorrowBids':
            require_once $root."template/template_dk.php";

            $html = getBorrowBids();

            echo json_encode($html);
        break;

        case 'getAllBids':
            require_once $root."template/template_dk.php";

            $html = getAllBids();

            echo json_encode($html);
        break;

        case 'getLendBids':

            require_once $root."template/template_dk.php";

            $html = getLendBids();

            echo json_encode($html);
        break;

        case 'getLendBidDisplay':

            require_once $root."template/template_dk.php";

            $html = getLendBidDisplay($_POST['bid_id']);

            echo json_encode($html);
        break;

        case 'getBorrowBidDisplay':

            require_once $root."template/template_dk.php";

            $html = getBorrowBidDisplay($_POST['bid_id']);

            echo json_encode($html);
        break;

        case 'updateBidPrice':

            require_once $root."template/template_dk.php";

            $html = updateBidPrice($_POST['bid_id'], $_POST['bid_price']);

            echo json_encode($html);

        break;

        case 'cancelBidPrice':

            require_once $root."template/template_dk.php";

            $html = cancelBidPrice($_POST['bid_id']);

            echo json_encode($html);

        break;

        case 'acceptBidBtn':

            require_once $root."template/template_dk.php";

            $html = acceptBidBtn($_POST['bid_id']);

            echo json_encode($html);

        break;

    }
?>