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
        $_SESSION['loggedInUserId'] = $user['id'];
        $_SESSION['loggedInUsername'] = $user['username'];
        $_SESSION['loggedInUserEmail'] = $user['email'];

        $loggedFail = false;
        header("location: /LazaLend");
    } else {
        $loggedFail = true;
    }
}

// Register
if (isset($_POST['register']) && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])
    && !empty($_POST['first_name']) && !empty($_POST['last_name'])) {

    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    $user = register($email, $username, $password, $first_name, $last_name);
    if ($user) {
        $_SESSION['loggedInUserId'] = $user['id'];
        $_SESSION['loggedInUsername'] = $user['username'];
        $_SESSION['loggedInUserEmail'] = $user['email'];
        $registerFail = false;
        header("location: /LazaLend");
    } else {
        $registerFail = true;
    }
}

$promoted = getAllPromotedItems();
$items = getAllItemsChronological();

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
                        <a href="categories?id=<?=$category_id?>" title="image 1" class="thumb">
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

<div>
    <h6 class="padding-left-15">Promoted Items</h6>
    <div class="scrolling-wrapper-flexbox">
        <?php foreach ($promoted as $item_id => $item) {?>
            <?php if ($item_id === 1) {?>
                <div class="col-5 col-md-4 active">
            <?php } else {?>
                <div class="col-5 col-md-4">
            <?php }?>

                <div class="panel panel-default">
                    <div class="panel-thumbnail-promoted">
                        <a href="#" title="image 1" class="thumb-promoted">
                            <img class="img-promoted" src="<?='.'.$item['images'][0]['image_link']?>" alt="slide 1">
                            <div class="container bottom-left">
                                <span class="whitetext">
                                    <?=$item['name']?>
                                </span>
                            </div>
                            
                        </a>
                        
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
</div>

<div>
    <h6 class="padding-left-15">Fresh Finds</h6>
    <div class="finds-container">
        <?php foreach ($items as $item_id => $item) {?>
            <a href="view-listing.php?id=<?=$item_id?>">
                <div class="item-container col-md-4">   
                    <div class="item-header">
                        <div class="pull-left">
                            <img class="card-header-profile" src="<?='.'.$item['profile_image_url']?>" >                 
                        </div>

                        <div class="item-header-content">
                            <span class="item-header-content-username"><?=$item['username']?></span>
                            
                            <span class="item-header-content-time"><?=get_time_ago( strtotime($item['created']) );?></span>
                        </div>
                    </div>
                
                    <div class="item-content">
                        <img class="item-picture" src="<?='.'.$item['images'][0]['image_link']?>">
                        <div class="item-content-name"><?=$item['name']?></div>
                        <div class="item-content-fee">$S<?=$item['fee']?>/day</div>
                        <div class="item-content-description"><?=$item['description']?></div>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
</div>


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
                <a href="#">Forgot Password</a>
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
        <div class="registermodal-container">
            <form class="register" action="" method="POST">
                <input type="text" name="email" placeholder="Email">
                <input type="text" name="username" placeholder="Username">
                <input type="password" name="password" placeholder="Password">
                <input type="text" name="first_name" placeholder="First Name">
                <input type="text" name="last_name" placeholder="Last Name">
                <input type="submit" name="register" class="register registermodal-submit" value="Register">
            </form>
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
if ($loggedFail) {?>
    <script>loginFail();</script>
<?php } elseif ($registerFail) {?>
    <script>registerFail();</script>
<?php } ?>
