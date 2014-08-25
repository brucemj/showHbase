<?php  
//header("Content-Type: text/html; charset=gb2312");
header("Content-Type: text/html; charset=utf-8");
$debugTs = false;
if($debugTs) echo microtime_float()."<br>";
ini_set('display_errors', E_ALL);  
$GLOBALS['THRIFT_ROOT'] = './libs';  
  
require_once( $GLOBALS['THRIFT_ROOT'] . '/Thrift.php' );  
require_once( $GLOBALS['THRIFT_ROOT'] . '/transport/TSocket.php' );  
require_once( $GLOBALS['THRIFT_ROOT'] . '/transport/TBufferedTransport.php' );  
require_once( $GLOBALS['THRIFT_ROOT'] . '/protocol/TBinaryProtocol.php' );  
require_once( $GLOBALS['THRIFT_ROOT'] . '/packages/Hbase/Hbase.php' );
require_once('Apache/Solr/Service.php');
require_once('html-parser/src/Parser.php');

$socket = new TSocket('172.21.12.58', '9090');  
  
$socket->setSendTimeout(10000); // Ten seconds (too long for production, but this is just a demo ;)  
$socket->setRecvTimeout(20000); // Twenty seconds  
$transport = new TBufferedTransport($socket);  
$protocol = new TBinaryProtocol($transport);  
$client = new HbaseClient($protocol);  
  
$transport->open();  

$limit = 20;
$queryKey = "title:专业";
$tsUrl = isset($_GET['ts']) ? $_GET['ts'] : false;  
$solr = new Apache_Solr_Service('172.21.12.58', 8983, '/solr/');  
try  
{  
   $solrResults = $solr->search($queryKey, 0, $limit);  
}  
catch (Exception $e)  
{  
   die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");  
}

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

$rawKey=array();
foreach ($solrResults->response->docs as $doc ){
	foreach ($doc as $field => $value){
		if ( $field == "id"){
			$rawKey[] = $value;
		}
	}
}

$tableName = "data57_webpage";  
$colu=array("f:cnt");
$colu2=array("p:c");

if($debugTs) echo microtime_float()."<br>";

foreach ($rawKey as $rawk){
	if($debugTs) echo "1: start search hbase DB.--------------".microtime_float()."<br>";
	$testa = $client->getRow($tableName,$rawk);
	//$rawkTs = $testa[0]->columns['f:cnt']->timestamp;
	$urlKey = $testa[0]->columns['f:bas']->value;
	$rawkTs = substr(md5($urlKey), 23);
	$testFcnt = $testa[0]->columns['f:cnt']->value;
	$testb=iconv("GB2312", "UTF-8", $testFcnt);

	if($debugTs) echo "3: start find title in HtmlParser------".microtime_float()."<br>";
	$p_title = $testa[0]->columns['p:t']->value;
	echo "<a href=index.php?ts=$rawkTs>".$p_title."</a> -- "."<a href=$urlKey target='_blank'>".$urlKey."</a><br>";


	if ( "$tsUrl" == "$rawkTs" ){
		if($debugTs) echo "2: start HtmlParser object.------------".microtime_float()."<br>";
		$html_dom = new HtmlParser\Parser( $testb );
		if($debugTs) echo "4: start find mcontert in HtmlParser---".microtime_float()."<br>";

		$pNum = 0;
		echo "<a name='tips'><table border='1' bgcolor='PaleGreen'><tr><td>";
		while( $p_mcontent = $html_dom -> find('div#mcontent',$pNum) ){
			echo "<p>".$p_mcontent->getPlainText()."</p>";
			$pNum++;
		}
		echo '</td></tr></table></a>';
	}
}

if($debugTs) echo microtime_float()."<br>";
$transport->close();  
exit(0);

?>
