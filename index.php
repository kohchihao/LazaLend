<?php 
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $query = 'SELECT name FROM items WHERE id = 1';
    $go_q = pg_query($query);
    
    $items = Array();

    while($fe_q = pg_fetch_assoc($go_q)) {
        $items[] = $fe_q['name'];
    }

    var_dump($items);

    $update = 'UPDATE items SET fee = 9.9 WHERE id = 1';
    pg_query($update);
?>