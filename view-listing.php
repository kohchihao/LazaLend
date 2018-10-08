<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";
    require_once $root . "./template/template_jy.php";

    $item_id = $_GET['id'];

    session_start();

    $errors = Array();

    // Create Bids
    if(isset($_POST['submit_bid'])) {
        $success = createBid($_SESSION['loggedInUserId'], $item_id, $_POST['bid_day'], $_POST['bid_fee'], $_POST['date_of_loan']);

        if($success == false) {
            $errors['create_bids'] = "Oops! Something went wrong in the bidding process.";
        } else {
            // TODO: Change to go to bids page
            // header("Location: /LazaLend");
            header("Location: /LazaLend/bids");
        }
    }
    // End of Create Bids

    // Item Details
    $item_details = getItem($item_id);
    // End of item details

    // Get All Item Bids
    $bids = getItemBids($item_id);
    // End of Get All Item Bids

    // Get user bid
    $user_bid = Array("bid_price" => "", "duration_of_loan" => "", "date_of_loan" => date('Y-m-d'));
    $user_has_bid = false;
    if (isset($_SESSION['loggedInUserId'])) {
        $temp_user_bid = getUserBid($_SESSION['loggedInUserId'], $item_id);

        if($temp_user_bid) {
            $user_has_bid = true;
            $user_bid = $temp_user_bid;
        }
    }
    // End of user bid

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
                        <a href="delete-item?id=<?=$item_id?>" class = "btn btn-danger delete-btn">Delete Listing</a>
                    </div>
                <?php } ?>

            </div>
            <div class = "col-md-6 left-div">
                <section class = "detail-container">
                    <h4><?=$item_details['name'] ?></h4>

                    <div class = "mt-4 item-action">
                        <!-- only show edit option if owner is logged in -->
                        <?php if (isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] == $item_details['user_id']) {?>

                            <a href="edit-listing?id=<?=$item_id?>" class = "btn blue-btn">Edit Listing</a>
                        <?php } else if (isset($_SESSION['loggedInUserId']) && $user_has_bid) { ?>
                            <a data-toggle="modal" data-target="#make-bid" class = "btn blue-btn">Update Offer</a>

                        <?php } else { ?>
                            <a data-toggle="modal" data-target="#make-bid" class = "btn blue-btn <?=isNotLoggedIn()?>">Make Offer</a>
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

                <?php if (!isset($_SESSION['loggedInUserId']) || isset($_SESSION['loggedInUserId']) && $_SESSION['loggedInUserId'] != $item_details['user_id']) {?>
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

    <div class="modal" id="make-bid">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">

                    <h4>Bid for <?=$item_details['name']?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div>
                        <div class = "row justify-content-center">
                            <div class = "col-sm-4 bids-info">
                                <p>Lowest Bid:</p>
                                <span>
                                    S$
                                    <?php 
                                        if (empty($bids['min_bid'])) {
                                            echo "0";
                                        } else {
                                            echo $bids['min_bid'];
                                        }  
                                    ?>
                                </span>
                            </div>
                            <div class = "col-sm-2"></div>
                            <div class = "col-sm-4 bids-info">
                                <p>Highest Bid:</p>
                                <span>
                                    S$
                                    <?php 
                                        if (empty($bids['max_bid'])) {
                                            echo "0";
                                        } else {
                                            echo $bids['max_bid'];
                                        }  
                                    ?>
                                    </span>
                            </div>
                        </div>
                    </div>

                    <div class = "item-info mt-4">
                        <div class = "row justify-content-center">
                            <div class = "col-sm-3">
                                <span>Fee per day:</span>
                            </div>
                            
                            <div class = "col-sm-8">
                                <span>S$<?=$item_details['fee']?></span>
                            </div>
                        </div>
                    </div>

                    <div class = "bidder-input-container mt-4">
                        <h4>Your bid: </h4>
                        <form method = "POST" action = "">
                            <input type = "text" class = "form-control mt-3" name = "bid_day" placeholder = "Number of days to loan" value = "<?=$user_bid['duration_of_loan']?>">
                            <input type = "text" class = "form-control mt-2" name = "bid_fee" placeholder = "Enter Bid Amount (Amount is taken as per day)" value = "<?=$user_bid['bid_price']?>">
                            <input type = "date" name = "date_of_loan" placeholder = "Date of Loan" class = "form-control mt-2" value = "<?=$user_bid['date_of_loan']?>">
                            
                            <div class = "btn-container mt-3">
                                <input type = "submit" class = "btn submit-bid" name = "submit_bid" value = "Place Bid">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php
    require $root."template/footer.php";

    if (sizeof($errors)) { 
        foreach($errors as $error) {
?>

            <script>show_error('<?=$error?>');</script>

<?php
        }        
    } 
?>