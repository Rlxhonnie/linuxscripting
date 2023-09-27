-+
<?php
    $folder = glob('/home/datawp/NEW/*.txt', GLOB_BRACE);

    #VERSI BARU 
	$data = array("username" => "superadmin",
                "password" => "@bcd12345",
               "device" => "bjicparkbinjaimall"
         );                                                                    
        $data_string = json_encode($data);  
        $curl_new = curl_init("http://103.145.175.5:20000/api/v3/login/device");
        curl_setopt($curl_new, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_new, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
					"Site-Destination: binjai"
					));
					
        curl_setopt($curl_new, CURLOPT_POST, true);
        curl_setopt($curl_new, CURLOPT_POSTFIELDS, $data_string);
		$response = curl_exec($curl_new);
		
        
        #convert json to array
		$response = json_decode($response);
		curl_close($curl_new);	
		$token = $response->token;
        $id_merchant = $response->device->merchant->_id;
	

	foreach($folder as $file){
		@$filename = end(explode('/', $file));
        $data = file($file);
        $sum = count($data);
        $tax_type = 'TipePajak';
        //print_r($folder);exit();
        foreach ($data as $key => $value){
            $nama_device = 'devnamepars';
            $explode_row = explode('|', $value);
		

			$tax_type = 'TipePajak';
			if ($trxid) {	
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
				'deviceId' => 'devnamepars'
			);

			# KE WEB VERSI 3
			$dataTransaksi = json_encode($dataTransaksi, JSON_PRETTY_PRINT);
			$curl_new = curl_init("http://103.145.175.5:20000/api/v3/transactions");
			curl_setopt($curl_new, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_new, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Site-Destination: binjai",
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

		}
		
    }

	 $old_path = getcwd();
	 chdir('/usr/sbin/');
	 $output = shell_exec('./data-sender_v1.5sh');		
	 chdir($old_path);

?>
