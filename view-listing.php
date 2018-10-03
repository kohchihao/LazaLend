<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $item_id = $_GET['id'];

    // Update Items
    if(isset($_POST['update_item'])) {
        $update = "UPDATE items SET name = '" . $_POST['item_name'] . "', fee = " . $_POST['item_fee'] . " WHERE id = " . $item_id;
        $go_u = pg_query($update);

        header("Location: /LazaLend/");
        die();
    }
    // End of Update Items

    // Item Details
    $query = 'SELECT id, user_id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available, borrowed
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
            'TITLE' => 'Item Listing: '.$item_details['name'],
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
            <div class = "col-md-6">
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
                <!-- only show delete option if owner is logged in -->
                <?php if (isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] == $item_details['user_id']) {?>
                    <a href="edit-listing.php?delete=true&id=<?=$item_id?>">Delete Listing</a>
                <?php } ?>

            </div>
            <div class = "col-md-6">
                <div class = "row">
                    <div class = "h3"><?=$item_details['name'] ?></div>
                </div>
                <!-- only show edit option if owner is logged in -->
                <?php if (isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] == $item_details['user_id']) {?>
                    <a href="edit-listing.php?id=<?=$item_id?>">Edit Listing</a>
                <?php } ?>
                <div class = "row mt-5 mb-3">
                    <div class = "col-md-4">Description:</div>
                    <div class = "col-md-8"><?=$item_details['description'] ?></div>
                </div>
                <div class = "row mt-3 mb-5">
                    <div class = "col-md-4">Fee:</div>
                    <div class = "col-md-8">$<?=$item_details['fee'] ?>/day</div>
                </div>
                <div class = "row mt-5 mb-5">
                    <div class = "col-md-4">Date Available:</div>
                    <div class = "col-md-8"><?=$item_details['date_available'] ?></div>
                </div>
                <!-- TODO: Add location display -->
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
    </section>

<?php
    require $root."template/footer.php";
?>