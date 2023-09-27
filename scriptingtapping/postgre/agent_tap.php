<?php

    #KONEKSI DATABASE
    $db_connection = pg_connect("host=10.103.13.116 port=5432 dbname=agent_tap user=tapper password=tapping1234");
//  for($i = 1; $i <= 26; $i++ ){

    $date = (!empty($argv[1])) ? $argv[1] : date('Y-m-d', strtotime("-1 days"));
//  $date = (!empty($argv[1])) ? $argv[1] : date('Y-m-d', strtotime("-$i days"));
    $startdate = $date." 00:00:00";
    $enddate = $date." 23:59:59";
    if (!$db_connection) {
        echo "An error occurred.\n";
        exit;
    }


    #QUERY
    $result = pg_query($db_connection, "select * from parking_slip where created_at >= '$startdate' and created_at <= '$enddate'  and parking_fee != '0'; ");
    $string = '';

    while ($row = pg_fetch_assoc($result)) {
    $string .= $row['id'].'|'.$row['created_at'].'|'.$row['parking_fee']."\n";
    }


    $fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
    fwrite($fp, $string);

//}
?>
