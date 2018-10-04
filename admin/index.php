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
          <link rel="stylesheet" href="./css/index.css"></link>
      ',
  ),
  'FOOTER' => array(
      'JS' => '
          <!-- Include Your JavaScript Link Here -->
      ',
  ),
);

if (isset($_SESSION['loggedInAdminId'])) {
    header("location: /LazaLend/admin/homepage");
}

// Login
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $admin = login($username, $password);

    if ($admin) {
        $_SESSION['loggedInAdminId'] = $admin['id'];
        $_SESSION['loggedInUsername'] = $admin['username'];

        $loggedFail = false;
        header("location: /LazaLend/admin/homepage");
    } else {
        $loggedFail = true;
        header("location: /LazaLend/admin");
    }
}



require $adminRoot . "template/01-head.php";
?>

<div class="d-flex flex-fill flex-column justify-content-center align-items-center h-100 ">
    <div class="d-flex flex-fill flex-column justify-content-center align-items-center loginmodal-container">
        <h3>LazaLend Admin Page</h3>
        <form class="d-flex flex-column " method="POST">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="submit" name="login" class="loginmodal-submit" value="Login">
        </form>
    </div>
    
</div>



<?php
require $adminRoot . "template/footer.php";
?>
