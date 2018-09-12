<?php 
    require_once $root."secrets.php";
    
    function pg_equery($query) {
        $g = pg_query($query) or die($query."\n".pg_last_error()."\n");
        
        return $g;
    }
    
    /* DB CONNECTION */
    $db_name = "cs2102";
    $db_user = "postgres";
    $host = "localhost";
    $port = 5432;

    $hostname = gethostname();
    if(substr($hostname,0,3) == "ip-") {
        // Production
        $host = PROD_HOST;
        $port = PROD_PORT;
        $db_name = PROD_DBNAME;
        $db_user = PROD_USER;
        $db_pass = PROD_PASSWORD;
        
    } else {
        //Server DB but local env.
        $host = HOST;
        $port = PORT;
        $db_name = DBNAME;
        $db_user = USER;
        $db_pass = PASSWORD;
    }
    
    pg_connect("host=" . $host . " port=" . $port . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_pass);
    pg_query('SET search_path TO lazalend');	
?>