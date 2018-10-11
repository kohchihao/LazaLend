<?php
    /* ================ Page Queries ==================== */
    function createItem($user_id, $item_name, $item_fee, $item_category, $item_description, $pickup_lat, $pickup_long, $return_lat, $return_long, $date_available) {
        $insert = "INSERT INTO items (user_id, category_id, fee, name, description, pickup_lat, pickup_long, return_lat, return_long, date_available)
                VALUES (".$user_id.", ".pg_escape_string($item_category).", ".pg_escape_string($item_fee).", '".pg_escape_string($item_name)."', '".pg_escape_string($item_description)."', '".$pickup_lat."', '".$pickup_long."', '".$return_lat."', '".$return_long."', '".pg_escape_string($date_available)."') 
                RETURNING id
                ";
        
        $go_i = pg_query($insert);

        $item_id = pg_fetch_row($go_i)[0];

        return $item_id;
    }

    function createItemImages($item_id, $file_name, $is_cover) {
        $insert = "INSERT INTO item_images (item_id, image_link, cover) VALUES (".$item_id.", '/".$file_name."', ".$is_cover.")";
                        
        $go = pg_query($insert);
    }

    function getAllCategories() {
        $categories = Array();
        
        $q_c = 'SELECT id, name FROM categories';
        $go_qc = pg_query($q_c);

        while($fe_qc = pg_fetch_assoc($go_qc)) {
            $categories[] = $fe_qc;
        }

        return $categories;
    }
    
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

    // Fetch all bid item name
    function getAllBidsName() {
        $query = "SELECT b.id, i.name FROM bids b, items i WHERE b.item_id = i.id";
        // ^ haven't add session state of user tag to owner and bidder yet 
        $go_q = pg_query($query);
        $items = array();
  
        while ($fe_q = pg_fetch_assoc($go_q)) { 
            $items[$fe_q['id']]['name'] = $fe_q['name'];
        }
  
        return $items;
    }

    // Fetch all bids 
    function getAllBids($user_id) {
        
        $query = "
            SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.bidder_id = '".$user_id."')
            AND (u.id = b.owner_id)
            AND b.item_id NOT IN (SELECT l.item_id FROM loans l)
            UNION
            SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.owner_id = '".$user_id."')
            AND (u.id = b.bidder_id)
            AND b.item_id NOT IN (SELECT l.item_id FROM loans l)
            Order by last_updated DESC
            ";

        $go_q = pg_query($query);
        $bids = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bids[$fe_q['id']]['id'] = $fe_q['id'];
            $bids[$fe_q['id']]['owner_id'] = $fe_q['owner_id'];
            $bids[$fe_q['id']]['name'] = $fe_q['name'];
            $bids[$fe_q['id']]['description'] = $fe_q['description'];
            $bids[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bids[$fe_q['id']]['last_updated'] = $fe_q['last_updated'];
            $bids[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bids[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bids[$fe_q['id']]['username'] = $fe_q['username'];
        }

        return $bids;
    }

     // Fetch borrow bids 
     function getBorrowBids($user_id) {

        $query = "SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.bidder_id = '".$user_id."')
            AND (u.id = b.owner_id)
            AND b.item_id NOT IN (SELECT l.item_id FROM loans l)
            Order by last_updated DESC
            ";

        $go_q = pg_query($query);
        $bids = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bids[$fe_q['id']]['id'] = $fe_q['id'];
            $bids[$fe_q['id']]['owner_id'] = $fe_q['owner_id'];
            $bids[$fe_q['id']]['name'] = $fe_q['name'];
            $bids[$fe_q['id']]['description'] = $fe_q['description'];
            $bids[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bids[$fe_q['id']]['last_updated'] = $fe_q['last_updated'];
            $bids[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bids[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bids[$fe_q['id']]['username'] = $fe_q['username'];
        }

        return $bids;
    }

    // Fetch lend bids 
    function getLendBids($user_id) {

        $query = "SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.owner_id = '".$user_id."')
            AND (u.id = b.bidder_id)
            AND b.item_id NOT IN (SELECT l.item_id FROM loans l)
            Order by last_updated DESC
            ";

        $go_q = pg_query($query);
        $bids = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bids[$fe_q['id']]['id'] = $fe_q['id'];
            $bids[$fe_q['id']]['owner_id'] = $fe_q['owner_id'];
            $bids[$fe_q['id']]['name'] = $fe_q['name'];
            $bids[$fe_q['id']]['description'] = $fe_q['description'];
            $bids[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bids[$fe_q['id']]['last_updated'] = $fe_q['last_updated'];
            $bids[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bids[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bids[$fe_q['id']]['username'] = $fe_q['username'];
        }

        return $bids;
    }

    function getLoanBidInfo($bid_id) {
        $query = "SELECT
            b.id, b.bid_price, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            From bids b, items i, item_images ii, users u
            WHERE b.id = '".$bid_id."'
            AND i.id = b.item_id
            AND i.id = ii.item_id
            AND u.id = b.bidder_id
            ";

        $go_q = pg_query($query);
        $bid = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bid[$fe_q['id']]['id'] = $fe_q['id'];
            $bid[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bid[$fe_q['id']]['description'] = $fe_q['description'];
            $bid[$fe_q['id']]['name'] = $fe_q['name'];
            $bid[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bid[$fe_q['id']]['username'] = $fe_q['username'];
            $bid[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bid[$fe_q['id']]['offer'] = $fe_q['username']. ' offered S$ ' .$fe_q['bid_price'];

        }

        return $bid;
    }

    function getBorrowBidInfo($bid_id) {
        $query = "SELECT
            b.id, b.bid_price, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            From bids b, items i, item_images ii, users u
            WHERE b.id = '".$bid_id."'
            AND i.id = b.item_id
            AND i.id = ii.item_id
            AND u.id = b.owner_id
            ";

        $go_q = pg_query($query);
        $bid = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bid[$fe_q['id']]['id'] = $fe_q['id'];
            $bid[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bid[$fe_q['id']]['description'] = $fe_q['description'];
            $bid[$fe_q['id']]['name'] = $fe_q['name'];
            $bid[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bid[$fe_q['id']]['username'] = $fe_q['username'];
            $bid[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bid[$fe_q['id']]['offer'] = 'You offered S$ ' .$fe_q['bid_price'];

        }

        return $bid;
    }

    // Update bid price
    function updateBidPrice($bid_id, $bid_price) {

        $updateQuery = "UPDATE bids SET bid_price = $bid_price WHERE id = '".$bid_id."'";
        $go_q = pg_query($updateQuery);

        $query = "SELECT
            b.id, b.bid_price, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            From bids b, items i, item_images ii, users u
            WHERE b.id = '".$bid_id."'
            AND i.id = b.item_id
            AND i.id = ii.item_id
            AND u.id = b.owner_id
            ";

        $go_q = pg_query($query);
        $bid = array();

        while ($fe_q = pg_fetch_assoc($go_q)) {
            $bid[$fe_q['id']]['id'] = $fe_q['id'];
            $bid[$fe_q['id']]['bid_price'] = $fe_q['bid_price'];
            $bid[$fe_q['id']]['description'] = $fe_q['description'];
            $bid[$fe_q['id']]['name'] = $fe_q['name'];
            $bid[$fe_q['id']]['image_link'][] = array('image_link' => $fe_q['image_link']);
            $bid[$fe_q['id']]['username'] = $fe_q['username'];
            $bid[$fe_q['id']]['profile_image_url'] = $fe_q['profile_image_url'];
            $bid[$fe_q['id']]['offer'] = 'You offered S$ ' .$fe_q['bid_price'];
        }

        return $bid;
    }

    // To accept the bid
    function acceptBidBtn($bid_id) {
        $query = "INSERT INTO loans (owner_id, borrower_id, item_id, bid_id)
        SELECT owner_id, bidder_id, item_id, id FROM bids WHERE id = '".$bid_id."'";
        $go_q = pg_query($query);

        $itemQuery = "UPDATE ITEMS SET borrowed = true WHERE id = (SELECT item_id FROM BIDS WHERE id = '".$bid_id."')";
        $go_q = pg_query($itemQuery);
    }

    // To delete the bid 
    function cancelBidPrice($bid_id) {
        $query = "DELETE FROM bids WHERE id = '".$bid_id."' ";
        $go_q = pg_query($query);
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

    /* ================ Bids Page ==================== */ 

    function getBidsContent($bids, $user_id) {
        $html = '';

        foreach ($bids as $id => $bid) {
            $html.= '
            <div class="row-sm-3 each-bid-row" data-id='.$bid['id'].' data-owner_id='.$bid['owner_id'].' data-user_id='.$user_id.'>
                <div class="bid-row">
                    <div class="pull-left">
                        <img class="card-header-profile" src=".'.$bid['profile_image_url'].'">
                    </div>

                    <div class="bid-content">
                        <div class="bid-header-row">
                            <div class="bid-content-username">'.$bid['username'].'</div>
                            <div class="bid-content-time">'.get_date($bid['last_updated']).'</div>
                        </div>
                        <div class="bid-bottom-row">
                            <div class="item-content">
                                <div class="bid-content-name">'.$bid['name'].'</div>
                                <div class="bid-content-description">'.$bid['description'].'</div>
                            </div>
                            <div class="item-content-pic">
                                <img class="item-pic" src=".'.$bid['image_link'][0]['image_link'].'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return $html;
    }

    function getLendBidDisplay($bid) {
       
        foreach ($bid as $id => $bid) {
            $html = '
            <div class="bid-display-box">
                <div class="bid-display-header">
                    <img class="bid-display-profile" src=".'.$bid['profile_image_url'].'">
                    <div class="bid-display-username">'.$bid['username'].'</div>
                </div>
                <div class="bid-display-header">
                    <img class="bid-display-content-pic" src=".'.$bid['image_link'][0]['image_link'].'">
                    <div class="bid-display-content">
                        <div class="bid-display-content-name">'.$bid['name'].'</div>
                        <div class="bid-display-content-fee">'.$bid['offer'].'</div>
                    </div>
                </div>
                <div class="bid-display-header">
                    <div class="bid-display-content-description">'.$bid['description'].'</div>
                </div>
            </div>
            <div class ="bid-offer-buttons">
                <div class ="pull-left">
                    <button type="button" class="btn btn-primary btn-sm acceptBidBtn" data-id='.$bid['id'].'>Accept Bid</button>
                    <button type="button" class="btn btn-outline-primary btn-sm cancelOfferBtn" data-id='.$bid['id'].'>Decline Bid</button>
                </div>
            </div>';
        }

        return $html;
    }

    function getBorrowBidDisplay($bid) {
        foreach ($bid as $id => $bid) {
            $html = '
            <div class="bid-display-box">
                <div class="bid-display-header">
                    <img class="bid-display-profile" src=".'.$bid['profile_image_url'].'">
                    <div class="bid-display-username">'.$bid['username'].'</div>
                </div>
                <div class="bid-display-header">
                    <img class="bid-display-content-pic" src=".'.$bid['image_link'][0]['image_link'].'">
                    <div class="bid-display-content">
                        <div class="bid-display-content-name">'.$bid['name'].'</div>
                        <div class="bid-display-content-fee">'.$bid['offer'].'</div>
                        <form>
                        <div class="input-group mb-1 mt-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text ml-3" >S$</span>
                            </div>
                            <input class="form-control" id="updatePriceInput" type="number" required step="0.01" value="'.$bid['bid_price'].'" required>
                        </div>
                    </div>
                </div>
                <div class="bid-display-header">
                    <div class="bid-display-content-description">'.$bid['description'].'</div>
                </div>
            </div>
            <div class ="bid-offer-buttons">
                <div class ="pull-left">
                    <button type="button" class="btn btn-primary btn-sm updateOfferBtn" data-id='.$bid['id'].'>Update Offer</button>
                    </form>
                    <button type="button" class="btn btn-outline-primary btn-sm cancelOfferBtn" data-id='.$bid['id'].'>Cancel Offer</button>
                </div>
            </div>';
        }

        return $html;
    }

    /* ================ End of Bids Page ==================== */ 

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

    // Get the last updated date and convert to text. Eg: Sep 26, 2018.
    function get_date($time) {
        $date = explode(" ", $time);

        return date("M jS, Y", strtotime($date[0]));  
    }
    /* ================ End of Utils ==================== */
?>