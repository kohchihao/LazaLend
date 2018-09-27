<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $item_id = $_GET['item'];

    // Update Items
    if(isset($_POST['update_item'])) {
        $update = "UPDATE items SET name = '" . $_POST['item_name'] . "', fee = " . $_POST['item_fee'] . " WHERE id = " . $item_id;
        $go_u = pg_query($update);

        header("Location: /LazaLend/");
        die();
    }
    // End of Update Items

    // Item Details
    $query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available
      FROM items WHERE id = ' . $item_id;
    $go_q = pg_equery($query);
    $item_details = array();
    $item_details = pg_fetch_assoc($go_q);
    // Item Images
    $query = 'SELECT image_link, cover FROM item_images WHERE item_id = ' . $item_id;
    $go_q = pg_equery($query);
    $item_images = array();
    while ($fe_q = pg_fetch_assoc($go_q)) {
        if ($fe_q['cover'] == 'TRUE') {
            array_unshift($item_images, $fe_q['image_link']);
        } else {
            array_push($item_images, $fe_q['image_link']);
        }
    }
    // End of Item Details

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => 'Item Listing: '.$fe_q['name'],
            'CSS' => '
                <!-- Include Your CSS Link Here -->
                <link rel="stylesheet" href="./css/item-listing.css">
            '
        ),
       'FOOTER' => Array (
            'JS' => '
                <!-- Include Your JavaScript Link Here -->
                <!--script src = "./js/item-listing.js"-->
            '
       )
    );

require $root."template/01-head.php";
?>

    <section id = "item-details">
        <div class = "row mt-3">
            <div class = "col-md-6 equal">
                <div id="item-image-carousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php
                        foreach ($item_images as $image_index=>$image_link) {
                            echo '<li data-target="#item-image-carousel" data-slide-to="'.$image_index.'"'
                                    .($image_index==0?' class="active"':'').'></li>';
                        }
                        ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php
                        foreach ($item_images as $image_index=>$image_link) {
                            echo '<div class = "carousel-item'.($image_index==0?' active':'').'">
                                <img class="d-block w-100 img-carousel" src = ".'.$image_link.'" />
                                </div>';
                        }
                        ?>
                    </div>
                    <a class="carousel-control-prev" href="#item-image-carousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#item-image-carousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <!--<div class = "row align-items-center">
                    <div class = "col-2">
                        <div id = "item_image_left" onclick = "prev_image()">&lt;</div>
                    </div>
                    <div class = "col-8" data-active-image = "0" data-last-image = "<=count($item_images) - 1?>" id = "item_image_wrapper">
                        <php
                        foreach ($item_images as $image_index=>$image_link) {
                            echo '<img class = "img-fluid.max-width: 100% height:auto"
                        id = "item_image_'.$image_index.'"
                        style = "display: '.($image_index==0?'initial':'none').'"
                        src = ".'.$image_link.'" />';
                        }
                        ?>
                    </div>
                    <div class = "col-2">
                        <div class = "col" id = "item_image_right" onclick = "next_image()">&gt;</div>
                    </div>
                </div>-->

            </div>
            <div class = "col-md-6 equal">
                <div class = "row">
                    <div class = "h3"><?=$item_details['name'] ?></div>
                </div>
                <div class = "row mt-5 mb-3">
                    <div class = "col-md-4">Description:</div>
                    <div class = "col-md-8"><?=$item_details['description'] ?></div>
                </div>
                <div class = "row mt-3 mb-5">
                    <div class = "col-md-4">Fee:</div>
                    <div class = "col-md-8">$<?=$item_details['fee'] ?></div>
                </div>
                <div class = "row mt-5 mb-5">
                    <div class = "col-md-4">Date Available:</div>
                    <div class = "col-md-8"><?=$item_details['date_available'] ?></div>
                </div>
                <div class = "row mt-5 mb-3">
                    <div class = "col-md-4">Pickup Location:</div>
                    <div class = "col-md-8">WIP</div>
                </div>
                <div class = "row my-3">
                    <div class = "col-md-4">Return Location:</div>
                    <div class = "col-md-8">WIP</div>
                </div>
            </div>
        </div>
        <div class = "row align-items-end">
            <div class = "col-md-6"></div>
            <div class = "col-md-6">
            <?php if (isset($_SESSION['loggedInUserId'])) {?>
                <a href="/LazaLend/view-bids" class="nav-link m-2 menu-item active text-light">View Bids</a>
                <a href="/LazaLend/make-bid" class="nav-link m-2 menu-item active text-light">Make Bid</a>
            <?php } else {?>
                <a href="#" class="nav-link m-2 menu-item" data-target="#login-modal" data-toggle="modal">Login</a>
                <a href="#" class="nav-link m-2 menu-item" data-target="#register-modal" data-toggle="modal">Sign Up</a>
            <?php }?>
            </div>
        </div>
    </section>

<?php
    require $root."template/footer.php";
?>