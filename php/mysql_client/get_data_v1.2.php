<?php
# Welcome to get data version = 1.2
# made with coffee by ilham

#File Config
$dbconf = fopen("/var/www/html/config.json", "r+") or die("Unable to open file!");
$getconf = json_decode(fgets($dbconf), TRUE);


#Database Config
$servername = strval($getconf["host"]);
$username = strval($getconf["username"]);
$password = strval($getconf["password"]);
$dbname = strval($getconf["database"]);
$tables = strval($getconf["tables"]);

#Create connection
$db = new SQLite3('getdata.db');
if(!$db) {
  echo $db->lastErrorMsg();
} else {
  echo "Memulai get data ... \n";
  $log_1  = date('Y-m-d H:i:s').' - '."Program Starting ...".PHP_EOL;
  $log_2  = date('Y-m-d H:i:s').' - '."(INFO !) - Pengambilan data transaksi dimulai".PHP_EOL;
  $log_3  = date('Y-m-d H:i:s').' - '."(INFO !) - Menghubungkan ke database wajib pajak".PHP_EOL;
  file_put_contents('/var/www/html/get_data.log', $log_1, FILE_APPEND);
  file_put_contents('/var/www/html/get_data.log', $log_2, FILE_APPEND);
  file_put_contents('/var/www/html/get_data.log', $log_3, FILE_APPEND);
}
$conn = new mysqli($servername, $username, $password , $dbname);

$date = (!empty($argv[1])) ? $argv[1] : date('Y-m-d', strtotime("-1 days"));
// $date = '2022-08-12';

if ($conn->connect_error) {
  $log0  =date('Y-m-d H:i:s').' - '."(ERROR) - Tidak dapat terhubung ke database wajib pajak ".PHP_EOL;
  $log01  = date('Y-m-d H:i:s').' - '."Program Ended ...".PHP_EOL;
  file_put_contents('/var/www/html/get_data.log', $log0, FILE_APPEND);
  file_put_contents('/var/www/html/get_data.log', $log01, FILE_APPEND);
  $sql = "INSERT INTO getstatus (status_id, tanggal, keterangan, updated) VALUES(0,'$date','gagal',0)";
  $db->exec($sql);
  echo "gagal terhubung ke datbase wajib pajak";

  die("Connection failed: " . $conn->connect_error);
}
if($conn){
    echo "Berhasil terhubung ke database wajib pajak";
    $log_4  = date('Y-m-d H:i:s').' - '."(OK !) - Berhasil terhubung ke database wajib pajak".PHP_EOL;
    $log_5  = date('Y-m-d H:i:s').' - '."(INFO !) - Pengecekan data transaksi yang gagal diambil ...".PHP_EOL;
	  file_put_contents('/var/www/html/get_data.log', $log_4, FILE_APPEND);
	  file_put_contents('/var/www/html/get_data.log', $log_5, FILE_APPEND);
}


$querry = "SELECT * FROM getstatus WHERE status_id='0' ";
$result = $db->query($querry);
while($rows = $result->fetchArray()){
$rowid = $rows['_id'];
$dates = $rows['tanggal'];



$log_6  =date('Y-m-d H:i:s').' - '."(INFO !) - Ditemukan data transaksi yang gagal diambil pada tanggal ".$dates.PHP_EOL;
file_put_contents('/var/www/html/get_data.log', $log_6, FILE_APPEND);

$query = mysqli_query($conn, "SELECT invoice_id, initanggal, subtotal, tax1Amount FROM $tables WHERE initanggal LIKE '%$dates%' AND tax1Amount !='0'  ");
$strings = '';

if(!empty($query)){
  while ($row = $query->fetch_assoc()) {
    $strings .= $row['invoice_id'].'|'.$row['initanggal'].'|'.$row['subtotal'].'|'.$row['tax1Amount']."\n";
  }
}


if(!empty($strings)){
  $update = "UPDATE getstatus set status_id='1' , keterangan='berhasil', updated='$date' WHERE _id='$rowid' ";
  $db->exec($update);
  echo "data berhasil diupdate ...";
  // $log_6  =date('Y-m-d H:i:s').' - '."(INFO !) - Ditemukan data transaksi yang gagal diambil pada tanggal ".$dates.PHP_EOL;
  $log_7  =date('Y-m-d H:i:s').' - '."(INFO !) - Pengambilan ulang data transaksi untuk tanggal ".$dates.PHP_EOL;
  $log_8  =date('Y-m-d H:i:s').' - '."(Success !) - Data transaksi pada tanggal "." $dates "." berhasil diupdate".PHP_EOL;
  // file_put_contents('/var/www/html/get_data.log', $log_6, FILE_APPEND);
  file_put_contents('/var/www/html/get_data.log', $log_7, FILE_APPEND);
  file_put_contents('/var/www/html/get_data.log', $log_8, FILE_APPEND);
}else{
  $logx1  =date('Y-m-d H:i:s').' - '."(WARNING !) - Gagal mengambil ulang data transaksi untuk tanggal ".$dates." Karena tidak ada data transaksi untuk tanggal tesebut !".PHP_EOL;
  file_put_contents('/var/www/html/get_data.log', $logx1, FILE_APPEND);
}

$fu = fopen("/home/datawp/NEW/".$dates.".txt","wb");
fwrite($fu, $strings);
}



#check if Exist!
$qrry = "SELECT * FROM getstatus WHERE status_id='1' AND tanggal='$date' ";
$rslt = $db->query($qrry);
while($row_ = $rslt->fetchArray()){
  if($row_['keterangan'] == 'berhasil'){
    echo "Data transaksi pada tanggal ".$date." sudah ada !";
    $log_11  = date('Y-m-d H:i:s').' - '."(INFO !) - Mencari data transaksi untuk tanggal ".$date.PHP_EOL;
    $log_s1  = date('Y-m-d H:i:s').' - '."(INFO !) - Data transaksi pada tanggal ".$date." sudah ada !".PHP_EOL;
    $log_s2  = date('Y-m-d H:i:s').' - '."Program Ended ".PHP_EOL;
    file_put_contents('/var/www/html/get_data.log', $log_11, FILE_APPEND);
    file_put_contents('/var/www/html/get_data.log', $log_s1, FILE_APPEND);
    file_put_contents('/var/www/html/get_data.log', $log_s2, FILE_APPEND);
    exit();
  }
}

#Perform queries
$query = mysqli_query($conn, "SELECT invoice_id, initanggal, subtotal, tax1Amount FROM $tables WHERE initanggal like '%$date%' AND tax1Amount !='0'  ");
$string = '';


if(!empty($query)){
  while ($row = $query->fetch_assoc()) {
    $string .= $row['invoice_id'].'|'.$row['initanggal'].'|'.$row['subtotal'].'|'.$row['tax1Amount']."\n";
  }
}else{
$sql = "INSERT INTO getstatus (status_id, tanggal, keterangan, updated) VALUES(0,'$date','gagal',0)";
$db->exec($sql);
echo "tidak ada data yang dapat diambil ...";
$logs1  = date('Y-m-d H:i:s').' - '."(WARNING !) - Berhasil terhubung ke db namun tidak ada data yang dapat diambil untuk tanggal ".$date.PHP_EOL;
$logs2  = date('Y-m-d H:i:s').' - '."Program Ended ".PHP_EOL;
file_put_contents('/var/www/html/get_data.log', $logs1, FILE_APPEND);
file_put_contents('/var/www/html/get_data.log', $logs2, FILE_APPEND);
}

if(!empty($string)){
$sql = "INSERT INTO getstatus (status_id, tanggal, keterangan, updated) VALUES(1,'$date','berhasil',0)";
$db->exec($sql);
echo "Data berhasil diambil ...";
$log11  = date('Y-m-d H:i:s').' - '."(INFO !) - Mencari data transaksi untuk tanggal ".$date.PHP_EOL;
$log22  = date('Y-m-d H:i:s').' - '."(Success!) - Data transaksi pada tanggal ".$date." berhasil diambil ".PHP_EOL;
$log33  = date('Y-m-d H:i:s').' - '."(Success!) - Data transaksi berhasil disimpan ! ".PHP_EOL;
$log44  = date('Y-m-d H:i:s').' - '."Program Ended ".PHP_EOL;
file_put_contents('/var/www/html/get_data.log', $log11, FILE_APPEND);
file_put_contents('/var/www/html/get_data.log', $log22, FILE_APPEND);
file_put_contents('/var/www/html/get_data.log', $log33, FILE_APPEND);
file_put_contents('/var/www/html/get_data.log', $log44, FILE_APPEND);
}



$fp = fopen("/home/datawp/NEW/".$date.".txt","wb");
fwrite($fp, $string);
?>
