<?php 
    $root = __DIR__."/";
    require_once $root."cfg.php";
    require_once $root."template/template_jy.php";

    session_start();

    if(!$_SESSION['loggedInUserId']) {
        header('Location: /LazaLend');
    } 

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => 'LazaLend',
            'CSS' => '
                <!-- Include Your CSS Link Here -->
                <link rel="stylesheet" href="./css/listing.css">
            '
        ),
       'FOOTER' => Array (
            'JS' => '
                <!-- Include Your JavaScript Link Here -->
                <script src = "./js/listing.js"></script>
            '
       )
    );

    require $root."template/01-head.php";
?>

<section class = "loan-item">
    <div class = "loan-navigation">
        <a href = "/LazaLend" class = "back-to-home">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class = "nav-title"><span>Choose Photos</span></h4>
    </div>

    <form methed = "POST" enctype="multipart/form-data" action = "">
        <div class = "loan-images-container row">
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(1)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(2)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(3)?>
            </div>
            <div class="col-sm-6 loan-images">
                <?=no_image_selected(4)?>
            </div>
        </div>

        <div class = "submit-btn-container">
            <input type = "submit" class = "btn loan-submit" disabled name = "loan_image_submit" value = "Next: Choose a category">
        </div>
    </form>
    
</section>

<?php  
    require $root."template/footer.php";
?>