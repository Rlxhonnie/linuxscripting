<?php
#   looping
//  for($i = 1; $i <= 1; $i++){

    $date = (!empty($argv[1])) ? $argv[1] : date('d-m-Y', strtotime("-0 days"));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://10.103.12.205:8181/api-pajak/list-trans.json?date=$date&start=1&end=2000"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $responses = curl_exec($ch); 
    $responses = json_decode($responses, true);
    
    $string = '';

    foreach ($responses as $value) {
        $trxid = $value['nomorStruk'].$value['nomor'];
        $trxAmount = $value['amount'];
        $trxDate = $value['tanggal'].' '.$value['jam'];


        $string .= $trxid.'|'.$trxDate.'|'.$trxAmount."\n";
        
     }

$fptmp = fopen("/tmp/".$date.".dump","wb");
fwrite($fptmp, $string);

$filePath="/tmp/".$date.".dump";
$linecount = 0;
$handleFile = fopen("/tmp/".$date.".dump","r");

while(!feof($handleFile)){
	$line = fgets($handleFile, 4096);

	$linecount = $linecount + substr_count($line, PHP_EOL);
	}

fclose($handleFile);

print_r($linecount);exit;

$fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
fwrite($fp, $string);

#looping
//}

?>
