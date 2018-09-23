<?php 
    $root = __DIR__."/";
    require_once $root."cfg.php";
    require_once $root."template/template_jy.php";

    session_start();

    if(!$_SESSION['loggedInUserId']) {
        header('Location: /LazaLend');
    } 

    $user_id = $_SESSION['loggedInUserId'];
    $errors = Array();

    if(isset($_POST['loan_item_submit'])) {
        $base_url = "https://" . MAPS_HOST . "/maps/api/geocode/json?key=" . GMAPS_API_KEY;
        
        $request_pickup_url = $base_url . "&address=" . urlencode($_POST['item_pickup']);
        // $pickup_json = file_get_contents($request_pickup_url);
        // $pickup_obj = json_decode($pickup_json);

        // $pickup_status = $pickup_obj->status;
        $pickup_status = "OK";
        $pickup_lat = "";
        $pickup_long = "";
        $return_lat = "";
        $return_long = "";

        if($pickup_status == "OK") {
            // $pickup_lat = $pickup_obj->results[0]->geometry->location->lat;
            // $pickup_long = $pickup_obj->results[0]->geometry->location->lng;
            $pickup_lat = "1.28733380";
            $pickup_long = "1.28733380";
            if($_POST['item_return'] == "") {
                $return_lat = $pickup_lat;
                $return_long = $pickup_long;
            }
        } else {
            $errors['location'] = "Invalid Pickup Location";
        }

        if($_POST['item_return'] != "") {
            // $request_return_url = $base_url . "&address=" . urlencode($_POST['item_pickup']);
            // $return_json = file_get_contents($request_return_url);
            // $return_obj = json_decode($pickup_json);
            $return_status = "OK";
            // $return_status = $return_obj->status;

            if($return_status == "OK") {
                // $return_lat = $return_obj->results[0]->geometry->location->lat;
                // $return_long = $return_obj->results[0]->geometry->location->lng;
                $return_lat = "1.28733380";
                $return_long = "1.28733380";
            } else {
                if(sizeof($errors)) {
                    $errors['location'] = "Invalid Pickup & Return Location";
                } else {
                    $errors['location'] = "Invalid Return Location";
                }
            }
        }

        if(sizeof($errors) == 0 && $_POST['item_fee'] < 1000000) {
           $insert = "INSERT INTO items (user_id, category_id, fee, name, description, pickup_lat, pickup_long, return_lat, return_long, date_available)
                VALUES (".$user_id.", ".$_POST['select_category'].", ".$_POST['item_fee'].", '".$_POST['item_name']."', '".$_POST['item_description']."', '".$pickup_lat."', '".$pickup_long."', '".$return_lat."', '".$return_lat."', '".$_POST['item_available']."') 
                RETURNING id
                ";
           
           $go_i = pg_equery($insert);

           $item_id = pg_fetch_row($go_i)[0];
        } else {
            if($_POST['item_fee'] >= 1000000) {
                $errors['fee'] = "Item Fee can be more than $999,999";
            }

            var_dump($errors);
        }

        die;
    }

    $categories = Array();

    $q_c = 'SELECT id, name FROM categories';
    $go_qc = pg_query($q_c);

    while($fe_qc = pg_fetch_assoc($go_qc)) {
        $categories[] = $fe_qc;
    }

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => 'LazaLend',
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
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(1)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(2)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(3)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(4)?>
            </div>
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
                    <a class = "category-text" onclick = "select_category(<?=$category['id']?>)"><?=$category['name']?></a>
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
                <div class = "item-details required">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "text" name = "item_name" placeholder = "Item Name" class = "items-input">
                        </span>
                    </div>
                </div>

                <div class = "item-details required">
                    <div class = "itd-b">
                        <span class = "itd-c">
                            <input type = "number" step = "0.01" name = "item_fee" placeholder = "Item Fee" class = "items-input">
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
                            <input type = "date" name = "item_available" placeholder = "Item Availability Date" class = "items-input" value = "<?=date('Y-m-d')?>">
                        </span>
                    </div>
                </div>
            </fieldset>

            <fieldset class = "fs">
                <div class = "fs-description">
                    <div>Location</div>
                </div>

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
                            <textarea rows = "3" placeholder = "Describe what you are loaning and include any details a borrower might be interested in." name = "item_description" class = "items-input"></textarea>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>

         <div class = "btn-container submit-btn-container">
            <input type = "submit" class = "btn" id = "loan-item-submit" name = "loan_item_submit" value = "Loan It!"> 
        </div>
    </section>
</form>

<?php  
    require $root."template/footer.php";
?>