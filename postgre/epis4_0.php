<?php

    #KONEKSI DATABASE
    $db_connection = pg_connect("host=192.168.0.50 port=5432 dbname=epis_4_0 user=bri password=bri");
//    for($i = 1; $i <= 7; $i++ ){

    $date = (!empty($argv[1])) ? $argv[1] : date('Y-m-d', strtotime("-1 days"));
    $startdate = $date." 00:00:00";
    $enddate = $date." 23:59:59";
    if (!$db_connection) {
        echo "An error occurred.\n";
        exit;
    }

    
    #QUERY
    $result = pg_query($db_connection, "select * from historytransaksi where tglmasuk >= '$startdate' and tglmasuk <= '$enddate'  and transaksifee != '0'; ");
    $string = '';
    
    while ($row = pg_fetch_assoc($result)) {
    $string .= $row['nostruk'].'|'.$row['tglmasuk'].'|'.$row['transaksifee']."\n";
    }
    

    $fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
    fwrite($fp, $string);

//}
?>
