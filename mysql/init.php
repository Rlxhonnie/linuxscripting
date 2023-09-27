<?php
$db = new SQLite3('getdata.db');

$tables = "CREATE TABLE IF NOT EXISTS getstatus ( 
            _id INTEGER NOT NULL,
            status_id INTEGER NOT NULL,
            tanggal DATETIME DEFAULT '0000-00-00',
            keterangan TEXT NOT NULL,
            updated TEXT NOT NULL,
            PRIMARY KEY (_id))";
    $db->exec($tables);
?>
