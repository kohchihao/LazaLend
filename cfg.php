<?php 
    /* DB CONNECTION */
    $db_name = "cs2102";
    $db_user = "postgres";
    $host = "localhost";
    $port = 5432;

    $hostname = gethostname();
    if(substr($hostname,0,3) == "ip-") {
        // Production
        $db_pass = "password";
        $host = "";
        $port = 5432;
    } elseif(gethostname() == "Jin-Ying-Tan") {
        $db_pass = "p@ssw0rd";
    }

    pg_connect("host=" . $host . " port=" . $port . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_pass);
    pg_query('SET search_path TO lazalend');	
?>