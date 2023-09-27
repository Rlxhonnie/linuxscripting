-+
<?php
	// $f=file_get_contents('/root/parsingstatus');

	// if ($f == 0){
	// 	echo "tidak ada parsing"; 
	// 	$open = fopen('/root/parsingstatus', 'w+');
	// 	fwrite($open,'1');
	// 	fclose($open); 
	// } else {
	// 	echo "lagi ada parsing";
	// 	exit();
	// }

	//BACA STRUK
    $folder = glob('/home/datawp/NEW/*.txt', GLOB_BRACE);

    #VERSI BARU 
	$data = array("username" => "batamdinas",
                "password" => "@bcd12345",
               "device" => "mdnkfcjamgin"
         );                                                                    
        $data_string = json_encode($data);  
        $curl_new = curl_init("http://103.145.175.5:2311/api/v3/login/device");
        curl_setopt($curl_new, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_new, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
					"Site-Destination: medan"
					));
					
        curl_setopt($curl_new, CURLOPT_POST, true);
        curl_setopt($curl_new, CURLOPT_POSTFIELDS, $data_string);
		$response = curl_exec($curl_new);
		
        
        #convert json to array
		$response = json_decode($response);
		curl_close($curl_new);	
		$token = $response->token;
        $id_merchant = $response->device->merchant->_id;
		$i = 1;
	

	foreach($folder as $file){
		@$filename = end(explode('/', $file));
        $data = file($file);
        $sum = count($data);
        $tax_type = 'Restoran';
        //print_r($folder);exit();
        foreach ($data as $key => $value){
            $nama_device = 'mdnkfcjamgin';
            $explode_row = explode('|', $value);

			$subtotal = $explode_row[2];
			$tax = $subtotal * 10/100;
		
			$tanggal = date('Y-m-d H:i:s', strtotime($explode_row[1]));
			$trxid = date('dmYHis', strtotime($tanggal));
		

			$trxid = $explode_row[0];
			// print_r($trxid);exit();

			$tax_type = 'Restoran';
			if ($subtotal > 0) {	
				#VERSI LAMA				
				$data_parsing = array(
					'rawData' => @$value,
					'trxId' => @$trxid,
					'trxAmount' => @(double)$subtotal,
					'trxService' => @(double)$service,
					'trxTax' => @(double)$tax,
					'trxDate' => @$tanggal,
					'taxType' => $tax_type,
					'status' => 'TRANSAKSI',
					'deviceId' => $nama_device
				);

			#VERSI BARU
			$dataTransaksi = array(
				'idMerchant' => $id_merchant,
				'rawData' => @$value,
				'trxId' => @$trxid,
				'trxAmount' => @(double)$subtotal,
				'trxService' => @(double)$service,
				'trxTax' => @(double)$tax,
				'trxDate' => @$tanggal,
				'taxType' => $tax_type,
				'status' => 'TRANSAKSI',
				'deviceId' => 'mdnkfcjamgin'
			);

			# KE WEB VERSI 3
			$dataTransaksi = json_encode($dataTransaksi, JSON_PRETTY_PRINT);
			$curl_new = curl_init("http://103.145.175.5:2311/api/v3/transactions");
			curl_setopt($curl_new, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_new, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Site-Destination: medan",
				"Authorization: $token"
			));
			curl_setopt($curl_new, CURLOPT_POST, true);
			curl_setopt($curl_new, CURLOPT_POSTFIELDS, $dataTransaksi);
			$response = curl_exec($curl_new);
			print_r($response);
			curl_close($curl_new);

		//    print_r($dataTransaksi);exit();
			} else {
				continue;
			}

			#VERSI LAMA
		
			// $data_parsing = json_encode($data_parsing, JSON_PRETTY_PRINT);
            
            // curl_setopt($curl, CURLOPT_POSTFIELDS, $data_parsing);
            // $response = curl_exec($curl);
            // echo "[".($key+1)."]".$response.PHP_EOL;
		}
		
		if(empty($data_parsing)){
				rename($file, "/home/datawp/CEK/$filename");
				$log  = date('Y-m-d H:i:s').' - '.$filename." format berubah".PHP_EOL;
				//Save string to log, use FILE_APPEND to append.
				file_put_contents('/var/log/parsing.log', $log, FILE_APPEND);
				
			}
		
		if ($key+1 < $sum){
			rename($file, "/home/datawp/CEK/$filename");
			$log  = date('Y-m-d H:i:s').' - '.$filename." data parsing kurang".PHP_EOL;
			//Save string to log, use FILE_APPEND to append.
			file_put_contents('/var/log/parsing.log', $log, FILE_APPEND);
		}
		if($key+1 == $sum){
			rename($file, "/home/datawp/SENT/$filename");
			$log  = date('Y-m-d H:i:s').' - '.$filename." parsing sukses".PHP_EOL;
			//Save string to log, use FILE_APPEND to append.
			file_put_contents('/var/log/parsing.log', $log, FILE_APPEND);
		}
		unset($file);
    }

	$old_path = getcwd();
	chdir('/usr/sbin/');
	$output = shell_exec('./data-sender');		
	chdir($old_path);
	
	$open = fopen('/root/parsingstatus', 'w+');
	fwrite($open,'0');
	fclose($open);
	
    // curl_close($curl);
    


?>
