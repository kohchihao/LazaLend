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

        case 'getBorrowBids':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";
            session_start(); 

            $user_id = $_SESSION['loggedInUserId'];

            $borrowBids = getBorrowBids($user_id);
            $html = getBidsContent($borrowBids, $user_id);

            echo json_encode($html);
        break;

        case 'getAllBids':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";
            session_start(); 

            $user_id = $_SESSION['loggedInUserId'];

            $allBids = getAllBids($user_id);
            $html = getBidsContent($allBids, $user_id);

            echo json_encode($html);
        break;

        case 'getLendBids':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";
            session_start(); 

            $user_id = $_SESSION['loggedInUserId'];

            $lendBids = getLendBids($user_id);
            $html = getBidsContent($lendBids, $user_id);

            echo json_encode($html);
        break;

        case 'getLendBidDisplay':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";

            $lendBid = getLoanBidInfo($_POST['bid_id']);
            $html = getLendBidDisplay($lendBid);

            echo json_encode($html);
        break;

        case 'getBorrowBidDisplay':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";

            $borrowBid = getBorrowBidInfo($_POST['bid_id']);
            $html = getBorrowBidDisplay($borrowBid);

            echo json_encode($html);
        break;

        case 'updateBidPrice':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";

            $updateBid = updateBidPrice($_POST['bid_id'], $_POST['bid_price']);
            $html = getBorrowBidDisplay($updateBid);

            echo json_encode($html);

        break;

        case 'cancelBidPrice':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";

            cancelBidPrice($_POST['bid_id']);
            $html = '';

            echo json_encode($html);

        break;

        case 'acceptBidBtn':
            require_once $root."cfg.php";
            require_once $root."template/template_jy.php";

            acceptBidBtn($_POST['bid_id']);
            $html = '';

            echo json_encode($html);

        break;
    }