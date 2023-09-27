<?php

    // for($i = 1; $i <= 1; $i++){
    $date = (!empty($argv[1])) ? $argv[1] : date('d-m-Y', strtotime("-1 days"));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://192.168.1.22:8181/api-pajak/list-trans.json?date=$date&start=1&end=2000"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $responses = curl_exec($ch); 
    $responses = json_decode($responses, true);
    
    // $log_start  = date('Y-m-d H:i:s').' - '."Program Starting ...".PHP_EOL;
    // file_put_contents('/var/log/get_data.log', $log_start, FILE_APPEND);
    
    if (count($responses) == 0){
        // $log_fail_1     = date('Y-m-d H:i:s').' - '."(FAIL !) - Tidak terhubung ke API wajib pajak".PHP_EOL;
        $log_fail_2     = date('Y-m-d H:i:s').' - '."(FAIL !) - Tidak mendapat Response apapun dari API".PHP_EOL;
        $log_fail_bash  = "(FAIL !) - Condition : False".PHP_EOL;
        // file_put_contents('/var/log/get_data.log', $log_fail_1, FILE_APPEND);
        file_put_contents('/var/log/get_data.log', $log_fail_2, FILE_APPEND);
        file_put_contents('/var/log/response_get.log', $log_fail_bash, FILE_APPEND);
        $string = '';

        $string .= "Fail : No Respon";
    
        $fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
        fwrite($fp, $string);
        echo "Data $date gagal diambil".' : '."No Responses API"."\n";
        exit;
    }

    // $log_succ_1     = date('Y-m-d H:i:s').' - '."(Success !) - Terhubung ke API wajib pajak".PHP_EOL;
    $log_succ_2     = date('Y-m-d H:i:s').' - '."(Sucesss !) - Terdapat Response dari API".PHP_EOL;
    $log_succ_bash  = "(Success !) - Condition : True".PHP_EOL;
    // file_put_contents('/var/log/get_data.log', $log_succ_1, FILE_APPEND);
    file_put_contents('/var/log/get_data.log', $log_succ_2, FILE_APPEND);
    file_put_contents('/var/log/response_get.log', $log_succ_bash, FILE_APPEND);
    
    $string = '';

    foreach ($responses as $value) {
        $trxid = $value['nomorStruk'];
        $trxAmount = $value['amount'];
        $trxDate = $value['tanggal'].' '.$value['jam'];

        $string .= $trxid.'|'.$trxDate.'|'.$trxAmount."\n";

        // print_r($string);exit;
    }
	$fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
	fwrite($fp, $string);
    	echo "Data $date berhasil diambil"."\n";
    	// $log_end  = date('Y-m-d H:i:s').' - '."Program Ended ...".PHP_EOL;
    	// file_put_contents('/var/log/get_data.log', $log_end_bash, FILE_APPEND);
    	// }
?>
