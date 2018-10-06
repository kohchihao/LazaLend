<?php
    $root = __DIR__."/../";
    require_once $root."cfg.php";

    session_start();

    //Fetch all bid item name
    function getAllBidsName() {
        $query = "SELECT b.id, i.name FROM bids b, items i WHERE b.item_id = i.id";
        // ^ haven't add session state of user tag to owner and bidder yet 
        $go_q = pg_query($query);
        $items = array();
  
        while ($fe_q = pg_fetch_assoc($go_q)) {
            // var_dump($fe_q); 
            $items[$fe_q['id']]['name'] = $fe_q['name'];
            // $items = $fe_q;
        }

        // var_dump($items); die;
  
        return $items;
    }

    // Fetch all bids 
    function getAllBids() {
        $query = "
            SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.bidder_id = '".$_SESSION['loggedInUserId']."')
            AND (u.id = b.owner_id)
            UNION
            SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.owner_id = '".$_SESSION['loggedInUserId']."')
            AND (u.id = b.bidder_id)
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

        $html = '';

        foreach ($bids as $id => $bid) {
            $html.= '
            <div class="row-sm-3 each-bid-row" data-id='.$bid['id'].' data-owner_id='.$bid['owner_id'].' data-user_id='.$_SESSION['loggedInUserId'].'>
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

    // Fetch borrow bids 
    function getBorrowBids() {

        $query = "SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.bidder_id = '".$_SESSION['loggedInUserId']."')
            AND (u.id = b.owner_id)
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

        $html = '';

        foreach ($bids as $id => $bid) {
            $html.= '
            <div class="row-sm-3 each-bid-row" data-id='.$bid['id'].' data-owner_id='.$bid['owner_id'].' data-user_id='.$_SESSION['loggedInUserId'].'>
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

    // Fetch lend bids 
    function getLendBids() {

        $query = "SELECT
            b.id, b.owner_id, b.bid_price, b.last_updated, i.description, i.name, ii.image_link, u.username, u.profile_image_url
            FROM bids b, items i, item_images ii, users u
            WHERE i.id = b.item_id
            AND i.id = ii.item_id
            AND (b.owner_id = '".$_SESSION['loggedInUserId']."')
            AND (u.id = b.bidder_id)
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

        $html = '';

        foreach ($bids as $id => $bid) {
            $html.= '<div class="row-sm-3 each-bid-row" data-id='.$bid['id'].' data-owner_id='.$bid['owner_id'].' data-user_id='.$_SESSION['loggedInUserId'].'>
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

    // Fetch bid information to be displayed 
    function getLendBidDisplay($bid_id) {

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

    // Fetch bid information to be displayed 
    function getBorrowBidDisplay($bid_id) {

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

    // To accept the bid
    function acceptBidBtn($bid_id) {
        $query = "INSERT INTO loans (owner_id, borrower_id, item_id, bid_id)
        SELECT owner_id, bidder_id, item_id, id FROM bids WHERE id = '".$bid_id."'";
        $go_q = pg_query($query);

        $html = '';
        return $html;
    }

    // To delete the bid 
    function cancelBidPrice($bid_id) {
        $query = "DELETE FROM bids WHERE id = '".$bid_id."' ";
        $go_q = pg_query($query);
        $html = '';
        
        return $html;
    }

    //utility - Get the last updated date and convert to text. Eg: Sep 26, 2018.
    function get_date($time)
    {
        $date = explode(" ", $time);

        return date("M jS, Y", strtotime($date[0]));  
    }

?>