<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";
    require_once $root."template/template_jy.php";

    session_start();

    if(!$_SESSION['loggedInUserId'] || !isset($_GET['id'])) {
        header('Location: /LazaLend');
    }

    $user_id = $_SESSION['loggedInUserId'];
    $errors = Array();

    $item_id = $_GET['id'];
    $item_details = getItem($item_id);

    if($item_details['user_id'] != $user_id) {
        header('Location: /LazaLend');
    }

    if(isset($_POST['edit-item-submit'])) {
        $base_url = "https://" . MAPS_HOST . "/maps/api/geocode/json?key=" . GMAPS_API_KEY;
        $file_target_dir = "storage/items/";
        $target_files = Array();
        $files_tmp_names = Array();
        $request_pickup_url = json_encode(array('status' => 'ERROR'));
        $request_return_url = json_encode(array('status' => 'ERROR'));

        if(isEmptyField($_POST['item_name'])) {
            $errors['name'] = "Oops! Item name cannot be empty";
        }

        if(isEmptyField($_POST['item_fee'])) {
            $errors['location'] = "Oops! Item fee cannot be empty";
        }

        /*
        if(sizeof($errors) == 0) {
            $request_pickup_url = $base_url . "&address=" . urlencode($_POST['item_pickup']);
        }

        $pickup_json = file_get_contents($request_pickup_url);
        $pickup_obj = json_decode($pickup_json);

        $pickup_status = $pickup_obj->status;
        $pickup_lat = "";
        $pickup_long = "";
        $return_lat = "";
        $return_long = "";

        if($pickup_status == "OK") {
            $pickup_lat = $pickup_obj->results[0]->geometry->location->lat;
            $pickup_long = $pickup_obj->results[0]->geometry->location->lng;
            if($_POST['item_return'] == "") {
                $return_lat = $pickup_lat;
                $return_long = $pickup_long;
            }
        } else {
            $errors['location'] = "Oops! Invalid Pickup Location";
        }

        if($_POST['item_return'] != "" && sizeof($errors) == 0) {
            $request_return_url = $base_url . "&address=" . urlencode($_POST['item_return']);
            $return_json = file_get_contents($request_return_url);
            $return_obj = json_decode($return_json);
            $return_status = $return_obj->status;

            if($return_status == "OK") {
                $return_lat = $return_obj->results[0]->geometry->location->lat;
                $return_long = $return_obj->results[0]->geometry->location->lng;
            } else {
                if(sizeof($errors)) {
                    $errors['location'] = "Oops! Invalid Pickup & Return Location";
                } else {
                    $errors['location'] = "Oops! Invalid Return Location";
                }
            }
        }
        */

        if(sizeof($errors) == 0 && $_POST['item_fee'] < 1000000) {
            $update = "UPDATE items SET ".
                "category_id = ".pg_escape_string($_POST['select_category']).
                ", fee = ".pg_escape_string($_POST['item_fee']).
                ", name = '".pg_escape_string($_POST['item_name']).
                "', description = '".pg_escape_string($_POST['item_description']).
                /*"', pickup_lat = '".$pickup_lat.
                "', pickup_long = '".$pickup_long.
                "', return_lat = '".$return_lat.
                "', return_long = '".$return_long.*/
                "', date_available = '".pg_escape_string($_POST['item_available']).
                "' WHERE id = ".$item_id.";";

           $go_u = pg_query($update);
        } else if($_POST['item_fee'] >= 1000000) {
            $errors['fee'] = "Item Fee cannot be more than $999,999";
        }

        if(sizeof($errors) == 0) {
            header("Location: view-listing.php?id=".$item_id);
        }
    }

    $categories = Array();

    $q_c = 'SELECT id, name FROM categories';
    $go_qc = pg_query($q_c);

    while($fe_qc = pg_fetch_assoc($go_qc)) {
        $categories[] = $fe_qc;
    }

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => 'Edit Listing: '.$item_details['name'],
            'CSS' => '
                <!-- Include Your CSS Link Here -->
                <link rel="stylesheet" href="./css/edit-listing.css">
            '
        ),
       'FOOTER' => Array (
            'JS' => '
                <!-- Include Your JavaScript Link Here -->
                <script src = "./js/edit-listing.js"></script>
            '
       )
    );

    require $root."template/01-head.php";
?>

<form method = "POST" enctype="multipart/form-data" action = "">
    <!-- Categories -->
    <section class = "loan-item" id = "loan-categories">
        <div class = "loan-navigation">
            <a href = "view-listing.php?id=<?=$item_id?>" class = "back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class = "nav-title"><span>Choose a Category (Images cannot be edited)</span></h4>
        </div>

        <div class = "">
            <input type = "text" class = "hidden" name = "select_category" id = "select_category" value = "1">

            <ul class = "categories">
                <?php foreach($categories as $category) { ?>
                <li class = "category">
                    <a class = "<?=($category['id'] == $item_details['category_id'])?'current-category-text':'category-text'?> " onclick = "select_category(<?=$category['id']?>)"><?=$category['name']?><?=($category['id'] == $item_details['category_id'])?' (Current Category)':''?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </section>

    <!-- Item Details -->
    <section class = "loan-item hidden" id = "loan-details">
        <div class = "loan-navigation">
            <a class = "back-btn" onclick = "back_to_loan_category()">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class = "nav-title"><span>Basic Details</span></h4>
            <div class = "extra-info">
                <span class = "highlighted-text">Highlighted</span> fields are required
            </div>
        </div>

        <div class = "details-container">
            <fieldset class = "fs">
                <div class = "fs-description">
                    <div>Item Name</div>
                </div>
                <div class = "item-details required">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "text" name = "item_name" value = "<?=$item_details['name'] ?>" class = "items-input">
                        </span>
                    </div>
                </div>

                <div class = "fs-description">
                    <div>Fee per day</div>
                </div>
                <div class = "item-details required">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "number" step = "0.01" name = "item_fee" value = "<?=$item_details['fee'] ?>" class = "items-input">
                        </span>
                    </div>
                </div>
            </fieldset>

            <fieldset class = "fs">
                <div class = "fs-description">
                    <div>Availability</div>
                </div>

                <div class = "item-details">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "date" name = "item_available" value = "<?=$item_details['date_available'] ?>" class = "items-input">
                        </span>
                    </div>
                </div>
            </fieldset>

            <fieldset class = "fs">
                <div class = "fs-description">
                    <div>Location</div>
                </div>

               <!-- TODO: Add location display (and ability to edit it?) -->

                <div class = "item-details required">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "text" name = "item_pickup" placeholder = "Item Pickup Location" class = "items-input">
                        </span>
                    </div>
                </div>

                 <div class = "item-details">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "text" name = "item_return" placeholder = "Item Return Location" class = "items-input">
                        </span>
                    </div>
                </div>

                <div class = "more-details">
                    If return location is not specified, return location will be same as pickup location.
                </div>
            </fieldset>

            <fieldset>
                <div class = "fs-description">
                    <div>Description</div>
                </div>

                <div class = "item-details">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <textarea rows = "3" name = "item_description" class = "items-input"><?=$item_details['description']?></textarea>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>

         <div class = "btn-container submit-btn-container">
            <input type = "submit" class = "btn" id = "edit-item-submit" name = "edit-item-submit" value = "Update Listing!">
        </div>
    </section>
</form>

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