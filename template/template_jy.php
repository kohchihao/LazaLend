<?php
    /* ================ Page Queries ==================== */
    function getItem($item_id) {
        $pickup_address = "";
        $return_address = "";
        $base_url = "https://" . MAPS_HOST . "/maps/api/geocode/json?key=" . GMAPS_API_KEY;
        $lat_lng_url = $base_url . "&latlng=";
        
        $query = 'SELECT 
            it.id, 
            it.user_id, 
            it.name, 
            u.username,
            u.profile_image_url,
            u.created AS user_created,
            it.description, 
            it.fee, 
            it.pickup_lat, 
            it.pickup_long, 
            it.return_lat, 
            it.return_long, 
            it.date_available, 
            ii.image_link,
            ii.cover,
            c.name AS category,
            c.id AS category_id
        FROM items it, item_images ii, categories c, users u
        WHERE it.id = ' . $item_id . '
        AND ii.item_id = it.id 
        AND it.category_id = c.id
        AND it.user_id = u.id';
        
        $go_q = pg_query($query);

        $item = Array();
        $item['image_link'] = Array();

        while($fe_q = pg_fetch_assoc($go_q)) {
            $item['id'] = $fe_q['id'];
            $item['user_id'] = $fe_q['user_id'];
            $item['name'] = $fe_q['name'];
            $item['username'] = $fe_q['username'];
            $item['user_profile_image'] = $fe_q['profile_image_url'];
            $item['user_joined'] = get_date_ago($fe_q['user_created']);
            $item['description'] = $fe_q['description'];
            $item['fee'] = $fe_q['fee'];
            $item['date_available'] = $fe_q['date_available'];
            $item['category'] = $fe_q['category'];
            $item['category_id'] = $fe_q['category_id'];
            $item['pickup_lat'] = $fe_q['pickup_lat'];
            $item['pickup_long'] = $fe_q['pickup_long'];
            $item['return_lat'] = $fe_q['return_lat'];
            $item['return_long'] = $fe_q['return_long'];

            
            // Handle Images
            if ($fe_q['cover'] == t) {
                array_unshift($item['image_link'], $fe_q['image_link']);
            } else {
                array_push($item['image_link'], $fe_q['image_link']);
            }
        }

         // Handle Location
         $pickup_url = $lat_lng_url . $item['pickup_lat'] . ",%20" . $item['pickup_long'];
         $return_url = $lat_lng_url . $item['return_lat'] . ",%20" . $item['return_long'];
         $pickup_address = latlongToAddress($pickup_url);
         $return_address = latlongToAddress($return_url);
         
         $item['pickup_address'] = $pickup_address;
         $item['return_address'] = $return_address;

        return $item;
    }

    function getItemBids($item_id) {
        $bids = Array(); 

        $query = "SELECT it.fee, MIN(b.bid_price) AS min_bid, MAX(b.bid_price) AS max_bid
            FROM  items it, bids b
            WHERE it.id = ".$item_id."
            AND b.item_id = it.id
            GROUP BY it.id, it.fee
            ";

        $go_q = pg_query($query);

        $bids = pg_fetch_assoc($go_q);

        return $bids;
    }

    function getUserBid($user_id, $item_id) {
        $query = "SELECT bid_price, duration_of_loan, date_of_loan FROM bids WHERE bidder_id = " . $user_id . " AND item_id = " . $item_id;
        $go_q = pg_query($query);
        $user_bid = pg_fetch_assoc($go_q);

        return $user_bid;
    }

    function createBid($bidder_id, $item_id, $bid_day, $bid_fee, $date_of_loan) {
        $query = "SELECT user_id FROM items WHERE id = " . $item_id;   
        $go_q = pg_query($query);
        $owner_id = pg_fetch_assoc($go_q)['user_id'];

        if(empty($owner_id)) {
            return false;
        }

        $insert = "INSERT INTO bids (owner_id, bidder_id, item_id, bid_price, duration_of_loan, date_of_loan)
            VALUES (".$owner_id.", ".$bidder_id.", ".$item_id.", ".pg_escape_string($bid_fee).", ".pg_escape_string($bid_day).", '".pg_escape_string($date_of_loan)."')
            ON CONFLICT (owner_id, bidder_id, item_id) DO UPDATE 
            SET bid_price = excluded.bid_price,
            duration_of_loan = excluded.duration_of_loan,
            date_of_loan = excluded.date_of_loan";

        $go_i = pg_query($insert);

        if(pg_affected_rows($go_i) <= 0) {
            return false;
        }

        return true;
    }
    /* ================ End of Page Queries ==================== */


    /* ================ Create Items Page ==================== */    
    function no_image_selected($image_id) {      
        $html = '<div id = "image_'.$image_id.'">
                    <label for="loan_images_'.$image_id.'" class="loan-thumb">
                        <div class="loan-thumb-content">
                            <i class="far fa-image"></i>';
                            if($image_id == 1) $html .= '<span class = "loan-cover">Cover</span>';
                        $html.= '</div>
                    </label>
                </div>
                <input type="file" name="loan_images[]" id="loan_images_'.$image_id.'" class = "image_input hidden">';

        return $html;
    }

    function image_selected($image_id, $image_url) {
        $html = '
            <div class = "loan-thumb">
                <div class = "loan-thumb-content">
                    <img src = "'.$image_url.'">';
                    if($image_id == 1) $html .= '<span class = "loan-cover">Cover</span>';
                    $html .= '<button type = "button" class = "btn del-loan-images" id = "del_loan_image_'.$image_id.'" onclick="del_loan_image(this)"><i class="far fa-trash-alt"></i></button>
                </div>
            </div>
        ';

        return $html;
    }
    /* ================ End of Create Items Page ==================== */

    /* ================ Utils ==================== */
    function isNotLoggedIn() {
        if (!isset($_SESSION['loggedInUserId'])) {
            echo 'disabled';
        } 
    }
    
    function isValidImage($target_file) {
        $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            return false;
        }

        // Allow certain file formats
        if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg") {
            return false;
        }

        return true;
    }

    function isEmptyField($field) {
        if($field == "") {
            return true;
        }

        return false;
    }

    function latlongToAddress($url) {
        $address = "";
        
        $json = file_get_contents($url);
        $obj = json_decode($json);

        $status = $obj->status;

        if($status == "OK") {
            $address = $obj->results[0]->address_components[2]->short_name;
        }

        return $address;
    }

    function get_date_ago($date) {
        $string_diff = "Joined ";
        $TIMEZONE = "Asia/Singapore";
        date_default_timezone_set($TIMEZONE);
        $today = date("Y-m-d");
        $today = new DateTime($today);
        $date = new DateTime($date);
        
        $diff = $today->diff($date);
        
        if ($diff->y != 0) {
            $string_diff = $diff->y . " years ";
        }

        if ($diff->m != 0) {
            $string_diff .= $diff->m . " months ";
        }

        if ($diff->d != 0) {
            $string_diff .= $diff->d . " days ";
        }

        if (empty($string_diff)) {
            $string_diff = "Just now"; 
        } else {
            $string_diff .= "ago";
        }

        return $string_diff;
    }
    /* ================ End of Utils ==================== */
?>