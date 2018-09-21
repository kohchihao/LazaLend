<?php
session_start();
$root = __DIR__ . "/";
require_once $root . "cfg.php";
require_once $root . "./template/template.php";

$query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available FROM items WHERE borrowed = FALSE';
$go_q = pg_query($query);

$items = array();

while ($fe_q = pg_fetch_assoc($go_q)) {
    $items[$fe_q['id']] = $fe_q;
}

$_M = array(
    'HEAD' => array(
        'TITLE' => 'LazaLend',
        'CSS' => '
            <!-- Include Your CSS Link Here -->
            <link rel="stylesheet" href="./css/index.css"></link>
        ',
    ),
    'FOOTER' => array(
        'JS' => '
            <!-- Include Your JavaScript Link Here -->
            <script src = "./js/index.js"></script>
        ',
    ),
);

// To display categories inside index.php
$categories = getAllCategories();

// Login
if (isset($_POST['login']) && !empty($_POST['email']) && !empty($_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = login($email, $password);
    
    if ($user) {
        $_SESSION['loggedInUserId']= $user['id'];
        $_SESSION['loggedInUsername']= $user['username'];
        $_SESSION['loggedInUserEmail']= $user['email'];
    } else {
        $loggedFail = true;
    }
}

// Register
if (isset($_POST['register']) && !empty($_POST['email']) && !empty($_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    
}

require $root . "template/01-head.php";
?>



<div>
    <h6 class="padding-left-15">Explore LazaLend</h6>
    <div class="scrolling-wrapper-flexbox">
        <?php foreach ($categories as $category_id => $category) {?>
            <?php if ($category_id === 1) {?>
                <div class="col-5 col-md-2 active">
            <?php } else {?>
                <div class="col-5 col-md-2">
            <?php }?>

                <div class="panel panel-default">
                    <div class="panel-thumbnail">
                        <a href="#" title="image 1" class="thumb">
                            <img class="img-fluid mx-auto d-block img-carousel" src="<?=$category['image_url']?>" alt="slide 1">
                        </a>
                        <div class="container centertext">
                            <span>
                                <?=$category['name']?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
</div>


<section id = "ll">
    <div class = "">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class = "text-center">Name</th>
                    <th class = "text-center">Fee</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($items as $item_id => $item) {?>
                <tr>
                    <td class = "text-center">
                        <a href = "item-dashboard?id=<?=$item_id?>"><?=$item['name']?></a>
                    </td>

                    <td class = "text-center">
                        <?=$item['fee']?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</section>


<div class="modal" id="login-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">

        <h4 class="modal-title">Login to Your Account</h4><br>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="loginmodal-container">
            <form class="login" action="" method="POST">
                <input type="text" name="email" placeholder="Email">
                <input type="password" name="password" placeholder="Password">
                <input type="submit" name="login" class="login loginmodal-submit" value="Login">
            </form>

            <div class="login-help">
                <a href="#">Register</a> - <a href="#">Forgot Password</a>
            </div>
        </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<div class="modal" id="register-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">

        <h4 class="modal-title">Register your account</h4><br>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="loginmodal-container">
            <form class="register" action="" method="POST">
                <input type="text" name="email" placeholder="Email">
                <input type="password" name="password" placeholder="Password">
                <input type="submit" name="login" class="login loginmodal-submit" value="Login">
            </form>

            <div class="login-help">
                <a href="#">Register</a> - <a href="#">Forgot Password</a>
            </div>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<?php
require $root . "template/footer.php";
?>

<?php 
if ($loggedFail) { ?>
    <script>loginFail();</script>
<?php } ?> 
