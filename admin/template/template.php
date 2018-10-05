<?php

// Login admin function 
function login($username, $password) {
  $adminLoggedIn = false; 
  //select admin based on username
  $query = "SELECT id, username, password FROM admins WHERE username = ". string($username);
  $go_q = pg_query($query);
  
  if (pg_num_rows($go_q) == 0) { //no username found
    $adminLoggedIn = false;
  } else {
    $admin = pg_fetch_assoc($go_q);
    $dbPassword = $admin['password'];
     
    //verify the password is similar to db password
    if (password_verify($password, $dbPassword)) {
      $adminLoggedIn = true;
    } else {
      $adminLoggedIn = false;
    }
  }

  if ($adminLoggedIn) {
    return $admin;
  } else {
    return null;
  }
}

// Register admin function
function register($username, $password) {
  
  $hashedPassword =  password_hash($password, PASSWORD_BCRYPT);
  $query = "INSERT INTO admins(username, password) VALUES ("
    . string($username) . ","
    . string($hashedPassword)
    . ") ";

  $go_q = pg_query($query);
  
  if ($go_q) { //registered successfully
    $query = "SELECT id, username FROM admins WHERE username = ". string($username);
    $go_q = pg_query($query);
    $admin = pg_fetch_assoc($go_q);
    return $admin;
  } else { 
    return null;
  }
}

//Logout
function logout() {
  session_start();
  session_destroy();
}

//Fetch all items
function getAllItems() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.id DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];
    
    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

function updateItemPromotedStatus($item_id, $promoted) {
  $query = "
  UPDATE items
  SET promoted = " . $promoted
  . " WHERE id = " . $item_id;

  $go_q = pg_equery($query);
  pg_fetch_assoc($go_q);
  if (pg_affected_rows($go_q) == 1 ) {
    return true;
  } else {
    return false;
  }
}

//delete item based on item.
function deleteItem($item_id) {
  $query = "
  DELETE FROM items 
  WHERE id = " . $item_id;

  $go_q = pg_query($query);
  if (pg_affected_rows($go_q) == 1 ) {
    return true;
  } else {
    return false;
  }
}

// =================== SORT BY ITEM ID =========================
function getAllItemsSortIdAsc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.id ASC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];
    
    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

//Fetch all items
function getAllItemsSortIdDesc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.id DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

// =================== SORT BY USERNAME ID =========================
function getAllItemsSortUsernameAsc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY u.username ASC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

function getAllItemsSortUsernameDesc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY u.username DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

// =================== SORT BY DATE AVAILABLE =========================
function getAllItemsSortDateAvailableAsc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.date_available ASC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

function getAllItemsSortDateAvailableDesc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.date_available DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}
// =================== SORT BY PROMOTED AND ITEM ID =========================
function getAllItemsSortPromotedAsc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  AND item.promoted = true
  ORDER BY item.id ASC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

function getAllItemsSortPromotedDesc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  AND item.promoted = true
  ORDER BY item.id DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];
    
    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

// =================== SORT BY ITEM FEE =========================
function getAllItemsSortFeeAsc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.fee ASC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];
    
    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}

function getAllItemsSortFeeDesc() {
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
  u.id AS user_id,
  image.image_link,
  image.cover AS cover_image
  FROM items item, item_images image, categories c, users u
  WHERE item.id = image.item_id 
  AND item.category_id = c.id
  AND item.user_id = u.id
  ORDER BY item.fee DESC";
  
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
    $items[$fe_q['item_id']]['user_id'] = $fe_q['user_id'];

    if ($fe_q['cover_image'] == t) {
      $items[$fe_q['item_id']]['cover_image'] = $fe_q['image_link'];
    } else {
      $items[$fe_q['item_id']]['images'][] = array('image_link' => $fe_q['image_link']);
    }
    
  }
  
  return $items;
}


//
function getIndividualUserStats($userid) {
  $query = "SELECT 
  u.id AS user_id,
  us.count AS total_items,
  us.most_expensive AS most_expensive,
  us.most_cheapest AS most_cheapest,
  u.username AS username,
  u.first_name AS first_name,
  u.last_name AS last_name,
  u.email AS email,
  u.profile_image_url AS profile_image_url
  FROM USER_STATS us, users u 
  WHERE us.user_id = u.id
  AND u.id =" . $userid;

  $go_q = pg_query($query);
  $userStats = pg_fetch_assoc($go_q);
  return $userStats;
}



//utility - Wrap value inside a ' ' 
function string($value) {
  return "'$value'";
}
?>