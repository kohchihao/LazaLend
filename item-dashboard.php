<?php
    $root = __DIR__."/";
    require_once $root."cfg.php";

    $item_id = $_GET['id'];

    // Update Items
    if(isset($_POST['update_item'])) {
        $update = "UPDATE items SET name = '" . $_POST['item_name'] . "', fee = " . $_POST['item_fee'] . " WHERE id = " . $item_id;
        $go_u = pg_equery($update);

        header("Location: /LazaLend/");
        die();
    }
    // End of Update Items
    
    
    // Item Details
    $query = 'SELECT id, name, description, fee, pickup_lat, pickup_long, return_lat, return_long, date_avaiable FROM items WHERE id = ' . $item_id;
    $go_q = pg_query($query);
    $item = pg_fetch_assoc($go_q); 
    // End of Item Details

    $_M = Array(
        'HEAD' => Array (
            'TITLE' => $item['name'],
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

<section id = "item-dashboard">
    <form method = "POST" action = "">
        
        <div class = "row mt-4">
            <div class = "field-label col-sm-4">
                Name:
            </div>

            <div class = "item-value col-sm-8">
                <input type = "text" class = "form-control" name = "item_name" value = "<?=$item['name']?>">
            </div>
        </div>

        <div class = "row mt-2">
            <div class = "field-label col-sm-4">
                Fee:
            </div>

            <div class = "item-value col-sm-8">
                <input type = "text" class = "form-control" name = "item_fee" value = "<?=$item['fee']?>">
            </div>
        </div>

        <div class = "row justify-content-center mt-2">
            <input type = "submit" name = "update_item" class = "btn btn-primary" value = "Update Item">
        </div>

    </form>
</section>