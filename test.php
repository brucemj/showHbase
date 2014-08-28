<?php  
header("Content-Type: text/html; charset=utf-8");
ini_set('display_errors', E_ALL); 
require_once('GrabData.class.php');

$grabD = new GrabData();

$pageTitles = $grabD -> GetPageTitleArray();

foreach($pageTitles as $hashId => $titleAndUlrs ){
	echo "web页面索引的HASH值是: ".$hashId."<br>";
	echo "web页面标题是: ".$titleAndUlrs["title"]."<br>";
	echo "web页面原始url是: ".$titleAndUlrs["url"]."<br>";
	echo "web页面逆序url是: ".$titleAndUlrs["hbase"]."<br>";
	echo "<hr>";
}
