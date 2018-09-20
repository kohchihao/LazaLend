<?php
$root = __DIR__ . "/";
require_once $root . "cfg.php";

$query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available FROM items WHERE borrowed = FALSE';
$go_q = pg_query($query);

$items = array();

while ($fe_q = pg_fetch_assoc($go_q)) {
    $items[$fe_q['id']] = $fe_q;
}

$_M = Array(
    'HEAD' => Array (
        'TITLE' => 'LazaLend',
        'CSS' => '
            <!-- Include Your CSS Link Here -->
            <link rel="stylesheet" href="./css/link.css">
        '
    ),
   'FOOTER' => Array (
        'JS' => '
            <!-- Include Your JavaScript Link Here -->
            <script src = "./js/link.js">
        '
   )
);

$query = 'SELECT id,name, image_url FROM categories';
$go_q = pg_query($query);
$categories = array();

while ($fe_q = pg_fetch_assoc($go_q)) {
    $categories[$fe_q['id']] = $fe_q;
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
            
            <form class="login">
                <input type="text" name="user" placeholder="Username">
                <input type="password" name="pass" placeholder="Password">
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

