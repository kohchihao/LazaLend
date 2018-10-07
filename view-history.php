<?php
session_start();
$root = __DIR__ . "/";
require_once $root . "cfg.php";
require_once $root . "./template/template.php";

if(!$_SESSION['loggedInUserId']) {
    header('Location: /LazaLend');
}

$user_id = $_SESSION['loggedInUserId'];
$errors = Array();

$items = getItemsBasedOnUser($user_id);
$_M = array(
    'HEAD' => array(
        'TITLE' => 'View your Listings',
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

require $root . "template/01-head.php";
?>

<div>
    <h6 class="padding-left-15">Your Items</h6>
    <div class="finds-container">
        <?php foreach ($items as $item_id => $item) {?>
            <a href="view-listing?id=<?=$item_id?>">
                <div class="item-container col-md-4">   
                    <div class="item-header">
                        <div class="item-header-content">
                            <span class="item-header-content-time">Listed <?=get_time_ago( strtotime($item['created']) );?></span>
                        </div>
                    </div>
                
                    <div class="item-content">
                        <img class="item-picture" src="<?='.'.$item['cover_image']?>">
                        <div class="item-content-name"><?=$item['name']?></div>
                        <div class="item-content-fee">$S<?=$item['fee']?>/day</div>
                        <div class="item-content-description"><?=$item['description']?></div>
                    </div>
                </div>
            </a>
        <?php } ?>
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
