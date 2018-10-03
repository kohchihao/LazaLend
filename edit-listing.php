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
    $q_i = 'SELECT user_id, category_id, fee, name, description, pickup_lat, pickup_long, return_lat, return_long, date_available, borrowed FROM items WHERE id = '.$item_id;
    $go_qi = pg_query($q_i);
    $item_details = pg_fetch_assoc($go_qi);
    if($item_details['user_id'] != $user_id) {
        header('Location: /LazaLend');
    }

    $q_ii = 'SELECT image_link, cover FROM item_images WHERE item_id = ' . $item_id;
    $go_qii = pg_query($query);
    $item_images = array();
    while ($fe_q = pg_fetch_assoc($go_q)) {
        if ($fe_q['cover'] == 'TRUE') {
            array_unshift($item_images, $fe_q['image_link']);
        } else {
            array_push($item_images, $fe_q['image_link']);
        }
    }
    if(isset($_POST['loan_item_submit'])) {
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

        // Check if file is valid
        $count = 0;
        foreach($_FILES['loan_images']['name'] as $file_name) {
            if($file_name != "") {
                $target_file = $file_target_dir . time() . $count . "-" . basename($file_name) ;
                $target_files[] = $target_file;
                $files_tmp_names[] = $_FILES["loan_images"]["tmp_name"][$count];
                $valid_image = isValidImage($target_file);
                
                if(!$valid_image) {
                    $errors['images'] = 'Oops! One of the image is an invalid image';    
                }
            }
            $count++;
        }

        // Check if there is a cover image
        if($_FILES['loan_images']['size'][0] == 0) {
            $errors['cover_image'] = 'Oops! Please supply a cover image';
        }

        if(sizeof($errors) == 0 && $_POST['item_fee'] < 1000000) {
            $update = "UPDATE items SET
                user_id = ".$user_id.", category_id = ".pg_escape_string($_POST['select_category']).", fee = ".pg_escape_string($_POST['item_fee']).", name = '".pg_escape_string($_POST['item_name'])."', description = '".pg_escape_string($_POST['item_description'])."', pickup_lat = '".$pickup_lat."', pickup_long = '".$pickup_long."', return_lat = '".$return_lat."', return_long = '".$return_long."', date_available = '".pg_escape_string($_POST['item_available'])."')
                WHERE id = ;
                ";
           
           $go_i = pg_query($insert);

           $item_id = pg_fetch_row($go_i)[0];

           if($item_id >= 0) {
                // Upload Image
                for($i = 0; $i < sizeof($target_files); $i++) {
                    $is_cover = 'FALSE';
                    if (move_uploaded_file($files_tmp_names[$i], $target_files[$i])) {
                        // Insert into item_images
                        if($i == 0) $is_cover = 'TRUE';  
                        $insert_ii = "INSERT INTO item_images (item_id, image_link, cover) VALUES (".$item_id.", '/".$target_files[$i]."', ".$is_cover.")";
                        
                        $go_ii = pg_query($insert_ii);
                    } else {
                        $errors['upload_img'] = 'Oops something went wrong when uploading image. Please try again!';
                    }
                }
           } else {
               $errors['insert_loan'] = "Something went wrong while creating loan. Please try again.";
           }
        } else {
            if($_POST['item_fee'] >= 1000000) {
                $errors['fee'] = "Item Fee can be more than $999,999";
            }
        }

        if(sizeof($errors) == 0) {
            header("Location: /LazaLend");
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
                <link rel="stylesheet" href="./css/listing.css">
            '
        ),
       'FOOTER' => Array (
            'JS' => '
                <!-- Include Your JavaScript Link Here -->
                <script src = "./js/listing.js"></script>
            '
       )
    );

    require $root."template/01-head.php";
?>

<form method = "POST" enctype="multipart/form-data" action = "">
    <!-- Images -->
    <section class = "loan-item" id = "loan-images">
        <div class = "loan-navigation">
            <a href = "/LazaLend" class = "back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class = "nav-title"><span>Choose Photos</span></h4>
        </div>

        <div class = "loan-images-container row">
            <?php foreach($item_images as $counter=>$item_image) {?>
                    <div class="col-sm-6 loan-images">
                        <?=image_selected($counter, $item_image['image_link'])?>
                    </div>
                <?php}
                for($i = count($item_images); $i < 4; $i++) {?>
                    <div class="col-sm-6 loan-images">
                        <?=no_image_selected($i)?>
                    </div>
                <?php}?>
        </div>

        <div class = "btn-container">
            <button type = "button" class = "btn" id = "go-to-category-btn" onclick = "go_to_loan_category()" disabled>Next: Choose a category</button>
        </div>    
    </section>

    <!-- Categories -->
    <section class = "loan-item hidden" id = "loan-categories">
        <div class = "loan-navigation">
            <a class = "back-btn" onclick = "back_to_loan_image()">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class = "nav-title"><span>Choose a Category</span></h4>
        </div>

        <div class = "">
            <input type = "text" class = "hidden" name = "select_category" id = "select_category" value = "1">

            <ul class = "categories">
                <?php foreach($categories as $category) { ?>
                <li class = "category">
                    <a class = "category-text" onclick = "select_category(<?=$category['id']?>)"><?=$category['name']?><?=($category['id'] == $item_details['category_id'])?'(Current Category)':''?></a>
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
            <input type = "submit" class = "btn" id = "loan-item-submit" name = "loan-item-submit" value = "Update Listing!">
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