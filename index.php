<?php 
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_avaiable FROM items WHERE borrowed = FALSE';
    $go_q = pg_query($query);
    
    $items = Array();

    while($fe_q = pg_fetch_assoc($go_q)) {
        $items[$fe_q['id']] = $fe_q;
    }

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => 'LazaLend',
            'CSS' => '
                <!-- BEGIN PAGE LEVEL STYLES -->
                <style>
                
                </style>
                <!-- END PAGE LEVEL STYLES -->
            '
        ),
       'FOOTER' => Array (
            'JS' => '
                <script>
                
                </script>
            '
       )
    );

    require $root."tpl/01-head.php";
?>

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
            <?php foreach($items as $item_id => $item) { ?>
                <tr>
                    <td class = "text-center">
                        <a href = "item-dashboard?id=<?=$item_id?>"><?=$item['name']?></a>
                    </td>

                    <td class = "text-center">
                        <?=$item['fee']?>
                    </td>   
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    
</section>