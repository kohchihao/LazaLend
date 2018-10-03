<?php
  $root = __DIR__ . "/";
  require_once $root . "cfg.php";
  require_once $root . "./template/template.php";

  if (isset($_POST['search'])) {

    $choice = $_POST['choice'];
    $search = $_POST['search'];
    
    $items = getItemsBasedOnName($search);
    
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
        ',
    ),
  );
  require $root . "template/01-head.php";
?>


<div>
    <h6 class="padding-left-15">Fresh Finds</h6>
    <div class="finds-container">
        <?php foreach ($items as $item_id => $item) {?>
            <a href="item-dashboard?id=<?=$item_id?>"> 
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


<?php
require $root . "template/footer.php";
?>