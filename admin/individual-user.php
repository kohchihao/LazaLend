<?php

session_start();
$adminRoot = __DIR__ . "/";
$root = __DIR__ . "/../";
require_once $root . "cfg.php";
require_once $adminRoot . "./template/template.php";

$_M = array(
  'HEAD' => array(
      'TITLE' => 'LazaLend Admin',
      'CSS' => '
          <!-- Include Your CSS Link Here -->
          <link rel="stylesheet" href="./css/individual-user.css"></link>
      ',
  ),
  'FOOTER' => array(
      'JS' => '
          <!-- Include Your JavaScript Link Here -->
      ',
  ),
);

if (!isset($_SESSION['loggedInAdminId'])) {
    header("location: /LazaLend/admin/homepage");
}

$userid = $_GET['userid'];
$user = getIndividualUserStats($userid);
require $adminRoot . "template/01-head.php";
?>

<table class="table">
  <thead>
    <tr>
      <th scope="col">User ID</th>
      <th scope="col">Username</th>
      <th scope="col">Total Items</th>
      <th scope="col">Most Expensive item fee</th>
      <th scope="col">Most Cheapest item fee</th>
      <th scope="col" class="text-center">User</th>
    </tr>
  </thead>

  <tbody>
    
      <tr>
        <th scope="row"><?=$user['user_id']?></th>
        <td><?=$user['username']?></td>
        <td><?=$user['total_items']?></td>
        <td>$<?=$user['most_expensive']?></td>
        <td>$<?=$user['most_cheapest']?></td>
        
        <td>    
          <div class="d-flex flex-column justify-content-center align-items-center">
              <img class="user-header-profile" src="<?='/../LazaLend' . $user['profile_image_url']?>" >
              <span><?=$user['username']?></span>
          </div>
        </td>
      </tr>
  </tbody>
</table>

<?php
require $adminRoot . "template/footer.php";
?>