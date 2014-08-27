<?php  
//header("Content-Type: text/html; charset=gb2312");
header("Content-Type: text/html; charset=utf-8");
ini_set('display_errors', E_ALL); 

require_once('GrabData.class.php');

$grabD = new GrabData();
$grabD->HbaseGetPageList();

