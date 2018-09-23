<?php
// Login user function 
function login($email, $password) {
  $userLoggedIn = false; 
  //select user based on email
  $query = "SELECT id, username, password, first_name, last_name, email FROM users WHERE email = '". $email . "' ";
  $go_q = pg_query($query);
  
  if (pg_num_rows($go_q) == 0) { //no email found
    $userLoggedIn = false;
  } else {
    $user = pg_fetch_assoc($go_q);
    $dbPassword = $user['password'];
     
    //verify the password is similar to db password
    if (password_verify($password, $dbPassword)) {
      $userLoggedIn = true;
    } else {
      $userLoggedIn = false;
    }
  }

  if ($userLoggedIn) {
    return $user;
  } else {
    return null;
  }
}

// Register user function
function register($email, $username, $password, $first_name, $last_name) {
  
  $hashedPassword =  password_hash($password, PASSWORD_BCRYPT);
  $query = "INSERT INTO users(username, password, first_name, last_name, email) VALUES ("
    . string($username) . ","
    . string($hashedPassword) . ","
    . string($first_name) . ","
    . string($last_name) . ","
    . string($email) 
    . ") ";

  $go_q = pg_query($query);
  
  if ($go_q) { //registered successfully
    $query = "SELECT id, username, first_name, last_name, email FROM users WHERE email = ". string($email);
    $go_q = pg_query($query);
    $user = pg_fetch_assoc($go_q);
    return $user;
  } else { 
    return null;
  }
  
}

//Logout
function logout() {
  session_start();
  session_destroy();
}

//Fetch all categories 
function getAllCategories() {
  $query = "SELECT id,name, image_url FROM categories";
  $go_q = pg_query($query);
  $categories = array();

  while ($fe_q = pg_fetch_assoc($go_q)) {
    $categories[$fe_q['id']] = $fe_q;
  }

  return $categories;
}

//Fetch all promoted items 
function getAllPromotedItems() {
  $query = "SELECT item.id, item.name, item.description, image.image_link FROM items item, item_images image
  WHERE item.id = image.item_id
  AND item.promoted = true";

  $go_q = pg_query($query);
  $promoted = array();

  while ($fe_q = pg_fetch_assoc($go_q)) {
    $promoted[$fe_q['id']]['name'] = $fe_q['name'];
    $promoted[$fe_q['id']]['description'] = $fe_q['description'];
    $promoted[$fe_q['id']]['images'][] = array('image_link' => $fe_q['image_link']);
  }
  
  return $promoted;

}

//Fetch all items in chrological order 
function getAllItemsChronological() {
  $query = "SELECT 
    item.id AS item_id,
    item.name AS item_name,
    item.fee AS item_fee,
    item.description AS item_description,
    item.pickup_lat AS item_pickup_lat,
    item.pickup_long AS item_pickup_long,
    item.return_lat AS item_return_lat,
    item.return_long AS item_return_long,
    item.date_available AS item_date_available,
    item.borrowed AS item_borrowed,
    item.promoted AS item_promoted,
    item.created AS item_created,
    item.last_updated AS item_last_updated,
    c.name AS categories_name,
    c.image_url AS categories_image_url,
    u.username AS user_username,
    u.profile_image_url AS user_profile_image_url,
    image.image_link
    FROM items item, item_images image, categories c, users u
    WHERE item.id = image.item_id 
    AND item.category_id = c.id
    AND item.user_id = u.id
    ORDER BY item.last_updated DESC";
  
  $go_q = pg_query($query);
  $items = array();

  while ($fe_q = pg_fetch_assoc($go_q)) {
    $items[$fe_q['item_id']]['id'] = $fe_q['item_id'];
    $items[$fe_q['item_id']]['name'] = $fe_q['item_name'];
    $items[$fe_q['item_id']]['fee'] = $fe_q['item_fee'];
    $items[$fe_q['item_id']]['description'] = $fe_q['item_description'];
    $items[$fe_q['item_id']]['pickup_lat'] = $fe_q['item_pickup_lat'];
    $items[$fe_q['item_id']]['pickup_long'] = $fe_q['item_pickup_lat'];
    $items[$fe_q['item_id']]['return_lat'] = $fe_q['item_return_lat'];
    $items[$fe_q['item_id']]['return_long'] = $fe_q['item_return_long'];
    $items[$fe_q['item_id']]['date_available'] = $fe_q['item_date_available'];
    $items[$fe_q['item_id']]['borrowed'] = $fe_q['item_borrowed'];
    $items[$fe_q['item_id']]['promoted'] = $fe_q['item_promoted'];
    $items[$fe_q['item_id']]['created'] = $fe_q['item_created'];
    $items[$fe_q['item_id']]['last_updated'] = $fe_q['item_last_updated'];
    $items[$fe_q['item_id']]['categories_name'] = $fe_q['categories_name'];
    $items[$fe_q['item_id']]['categories_image_url'] = $fe_q['categories_image_url'];
    $items[$fe_q['item_id']]['username'] = $fe_q['user_username'];
    $items[$fe_q['item_id']]['profile_image_url'] = $fe_q['user_profile_image_url'];
    $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
  }

  return $items;

}

//utility - Wrap value inside a ' ' 
function string($value) {
  return "'$value'";
}

//utility - Get how long the time is compared to now. Eg: 1 hour ago.
function get_time_ago( $time )
{
    $time_difference = time() - $time;

    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}


?>