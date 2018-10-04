<?php

session_start();
$adminRoot = __DIR__ . "/";
$root = __DIR__ . "/../";
require_once $root . "cfg.php";


$_M = array(
  'HEAD' => array(
      'TITLE' => 'LazaLend Admin',
      'CSS' => '
          <!-- Include Your CSS Link Here -->
          
      ',
  ),
  'FOOTER' => array(
      'JS' => '
          <!-- Include Your JavaScript Link Here -->
      ',
  ),
);

require $adminRoot . "template/01-head.php";
?>

<div>
  Penis
</div>



<?php
require $adminRoot . "template/footer.php";
?>
