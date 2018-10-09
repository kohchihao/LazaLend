<?php
  $root = __DIR__."/";
  require_once $root."cfg.php";
//   require_once $root . "./template/template_dk.php";
  require_once $root . "./template/template_jy.php";

  session_start();

  if(!$_SESSION['loggedInUserId']) {
    header('Location: /LazaLend');
  }

  $user_id = $_SESSION['loggedInUserId'];
  
  $_M = array(
    'HEAD' => array(
        'TITLE' => 'LazaLend',
        'CSS' => '
            <!-- Include Your CSS Link Here -->
            <link rel="stylesheet" href="./css/bids.css"></link>
        ',
    ),
    'FOOTER' => array(
        'JS' => '
            <!-- Include Your JavaScript Link Here -->
            <script src = "./js/bids.js"></script>
        ',
    ),
  );
    // To display items inside bids.php
    $bidsName = getAllBidsName();
    $allBids = getAllBids($user_id);

    require $root."template/01-head.php";
?>

<div>
    <div class="bids_container">
        <div class="row">
            <div class="col-sm-4">
                <!-- <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Item Name
                    </button>
                    <div class="dropdown-menu">
                        <?php foreach ($bidsName as $id => $bid) {?>
                        <a class="dropdown-item" href="#"><?=$bid['name']?></a>
                        <?php } ?>
                    </div>
                </div> -->

                <div class="bids-buttons" id="bids-buttons">
                    <button id="allBidsBtn" type="button" class="btn btn-outline-secondary btn-sm active">All bids</button>
                    <button id="borrowBtn" type="button" class="btn btn-outline-secondary btn-sm">Borrowing</button>
                    <button id="lendBtn" type="button" class="btn btn-outline-secondary btn-sm">Lending</button>
                </div>

                <div class="bids-container" id="bids-content">
                    <?=getBidsContent($allBids, $user_id)?>
                </div>
            </div>

            <div class="col">
                <div class="col">
                    <div class="row">
                        <div class="col"></div>
                            <div class="col-6" id="bid-display">
                            </div>
                        <div class="col"></div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require $root."template/footer.php";
?>