<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";
    require_once $root . "./template/template_jy.php";

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
    // $query = 'SELECT id, user_id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available, borrowed
    //   FROM items WHERE id = ' . $item_id;
    // $go_q = pg_equery($query);
    // $item_details = array();
    // $item_details = pg_fetch_assoc($go_q);
    // // Item Images
    // $query = 'SELECT image_link, cover FROM item_images WHERE item_id = ' . $item_id;
    // $go_q = pg_equery($query);
    // $item_images = array();
    // while ($fe_q = pg_fetch_assoc($go_q)) {
    //     if ($fe_q['cover'] == 'TRUE') {
    //         array_unshift($item_images, $fe_q['image_link']);
    //     } else {
    //         array_push($item_images, $fe_q['image_link']);
    //     }
    // }
    // End of Item Details

    // Item Details
    $item_details = getItem($item_id);
    // End of item details

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
                        foreach ($item_details['image_link'] as $image_index=>$image_link) {
                            echo '<li data-target="#item-image-carousel" data-slide-to="'.$image_index.'"'
                                    .($image_index==0?' class="active"':'').'></li>';
                        }
                        ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php
                        foreach ($item_details['image_link'] as $image_index=>$image_link) {
                            echo '<div class = "carousel-item'.($image_index==0?' active':'').'">
                                    <img class="img-carousel" src = ".'.$image_link.'" alt = "Item Image"/>
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
                    <div class = "mt-4 mb-5 del-btn-container"> 
                        <a href="delete-item.php?id=<?=$item_id?>" class = "btn btn-danger delete-btn">Delete Listing</a>
                    </div>
                <?php } ?>

            </div>
            <div class = "col-md-6 left-div">
                <section class = "detail-container">
                    <h4><?=$item_details['name'] ?></h4>

                    <div class = "mt-4 item-action">
                        <!-- only show edit option if owner is logged in -->
                        <?php if (isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] == $item_details['user_id']) {?>
                            <a href="item-dashboard.php?id=<?=$item_id?>" class = "btn blue-btn">Edit Listing</a>
                        <?php } else { ?>
                            <a href = "bid?id=<?=$item_id?>" class = "btn blue-btn">Make Offer</a>
                        <?php } ?>
                    </div>

                    <div class = "row mt-3 mb-3">
                        <div class = "col-md-4  description-label"><i class="fas fa-tag"></i></div>
                        <div class = "col-md-8">S$<?=$item_details['fee'] ?>/day</div>
                    </div>
                    <div class = "row mt-3 mb-3description-label">
                        <div class = "col-md-4  description-label"><i class="fas fa-info-circle"></i></div>
                        <div class = "col-md-8"><?=$item_details['description'] ?></div>
                    </div>
                    <div class = "row mt-3 mb-3">
                        <div class = "col-md-4  description-label"><i class="fas fa-align-justify"></i></div>
                        <div class = "col-md-8">In <a href = "categories?id=<?=$item_details['category_id']?>"><?=$item_details['category']?></a></div>
                    </div>
                    <div class = "row mt-3 mb-3">
                        <div class = "col-md-4  description-label">Date Available:</div>
                        <div class = "col-md-8"><?=$item_details['date_available'] ?></div>
                    </div>
                </section>

                <section class = "mt-4 addresses detail-container">
                    <h4 class = "owner-header">Loaning This</h4>

                    <div class = "row mt-3 mb-3">
                        <div class = "col-md-4  description-label">Pickup Location:</div>
                        <div class = "col-md-8">
                            <a href = "https://www.google.com/maps/place/<?=$item_details['pickup_lat']?>, <?=$item_details['pickup_long']?>" target = "_blank">
                                <?=$item_details['pickup_address']?>
                            </a>
                        </div>
                    </div>
                    <div class = "row my-3">
                        <div class = "col-md-4  description-label">Return Location:</div>
                        <div class = "col-md-8">
                            <a href = "https://www.google.com/maps/place/<?=$item_details['return_lat']?>, <?=$item_details['return_long']?>" target = "_blank">
                                <?=$item_details['return_address']?>
                            </a>
                        </div>
                    </div>
                </section>

                <?php if (isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] != $item_details['user_id']) {?>
                    <section class = "mt-4 detail-container">
                        <h4 class = "owner-header">Meet the Owner</h4>

                        <figure class = "user-info">
                            <img src = ".<?=$item_details['user_profile_image']?>" alt = "Profile Picture"/>
                            <figcaption class = "user-info-details">
                                <summary>
                                    <a href = "#" class = "username"><?=$item_details['username']?></a>
                                </summary>
                                <div class = "user-details">
                                    <label class = "user-joined"><?=$item_details['user_joined']?></label>
                                </div>
                            </figcaption>
                        </figure>
                    </section>
                <?php } ?>
            </div>
        </div>
    </section>

<?php
    require $root."template/footer.php";
?>