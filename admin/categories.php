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
          <link rel="stylesheet" href="./css/all-user.css"></link>
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


require $adminRoot . "template/01-head.php";
?>


<?php
require $adminRoot . "template/footer.php";
?>