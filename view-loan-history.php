<?php
$root = __DIR__ . "/";
require_once $root . "cfg.php";
require_once $root . "./template/template.php";
session_start();
$user_id = $_SESSION['loggedInUserId'];

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

      ',
    ),
);

$items = getLoanHistory($user_id);

require $root . "template/01-head.php";
?>


<div>
    <h6 class="padding-left-15">Loaned out items</h6>
    <div class="finds-container">
        <?php foreach ($items as $item_id => $item) {?>
            <a href="view-listing?id=<?=$item_id?>">
                <div class="item-container col-md-4">
                    <div class="item-header">
                        <div class="item-header-content">
                            <span class="item-header-content-time">Listed <?=get_time_ago(strtotime($item['created']));?></span>
                        </div>
                    </div>

                    <div class="item-content">
                        <img class="item-picture" src="<?='.' . $item['cover_image']?>">
                        <div class="item-content-name"><?=$item['name']?></div>
                        <div class="item-content-fee">$S<?=$item['fee']?>/day</div>
                        <div class="item-content-description"><?=$item['description']?></div>
                    </div>

                    <div class="item-content mt-1">
                      Loaned to:
                    </div>
                    <div class="item-header">
                        <div class="pull-left">
                            <img class="card-header-profile" src="<?='.'.$item['borrower_profile_image_url']?>" >                 
                        </div>

                        <div class="item-header-content">
                            <span class="item-header-content-username"><?=$item['borrower_username']?></span>
                        </div>
                    </div>
                </div>
            </a>
        <?php }?>
    </div>
</div>


<?php
require $root . "template/footer.php";
?>