<?php 
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_available FROM items WHERE borrowed = FALSE';
    $go_q = pg_query($query);
    
    $items = Array();

    while($fe_q = pg_fetch_assoc($go_q)) {
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
                <script src = "./js/link.js"></script>
            '
       )
    );

    require $root."template/01-head.php";
?>

<?php  
    require $root."template/footer.php";
?>