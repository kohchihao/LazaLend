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

//utility - Wrap value inside a ' ' 
function string($value) {
  return "'$value'";
}
?>