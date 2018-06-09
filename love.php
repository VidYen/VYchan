<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'inc/functions.php';

$query = prepare("SELECT `file_location`, `ad_link` FROM advertisement WHERE `ends_at` > :curr_time and `file_location` != '' and `ad_link` != ''");
$query->execute(array(':curr_time' => time()));
$rows = $query->fetchAll();
$rows[] = [0 => 'ad.png', 1 => '/buy.php?a'];
$row = $rows[array_rand($rows)];
session_start();
$_SESSION['ad'] = $row[1];

if(isset(explode('/', $row[0])[1])){
    $name = explode('/', $row[0])[1];
} else {
    $name = 'ad.png';
}
// snags the extension
$ext = pathinfo($name, PATHINFO_EXTENSION);

// send the right headers
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies
header("Content-type: image/" . $ext);
header("Content-Disposition: inline; filename=" . $name);
// readfile displays the image, passthru seems to spits stream.
readfile($row[0]);
exit;