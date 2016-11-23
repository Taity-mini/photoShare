<?php
//Database connection file

//Database credentials
$mysqlusername = "root";
$mysqlpassword = "";
$mysqldatabase = "photoshare";
$host = "localhost";
$domain = "http://photoshare.dev";


//Database connect and close functions using PDO

function dbConnect()
{
    global $host, $mysqldatabase, $mysqlusername, $mysqlpassword;
    try {
        $conn = new PDO("mysql:host=$host;dbname=$mysqldatabase", $mysqlusername, $mysqlpassword);
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $conn;
    } catch (PDOException $e) {
        echo 'Cannot connect to database';
        exit;
    }
}

function dbClose()
{
    $conn = null;
}

