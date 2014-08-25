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

//echo var_dump($solrResults);
//echo var_dump($solrResults->response->docs[0]);
$rawKey=array();
foreach ($solrResults->response->docs as $doc ){
	foreach ($doc as $field => $value){
		//echo var_dump($field);
		//echo "--------------------------<br>";
		//echo var_dump($value);
		//echo "--------------------------<br>";
		if ( $field == "id"){
			$rawKey[] = $value;
		}
	}
}

/*
foreach ( $rawKey as $idUrl){
	echo $idUrl."    ---<br>";
}
*/


//exit(0);
//获取表列表  
/*
$tables = $client->getTableNames();  
sort($tables);  
echo var_dump($tables)."<br>";
foreach ($tables as $name) {  
  
    echo( "  found: {$name} <br>" );  
}  
   
//创建新表student  
$columns = array(  
    new ColumnDescriptor(array(  
        'name' => 'id:',  
        'maxVersions' => 10  
    )),  
    new ColumnDescriptor(array(  
        'name' => 'name:'  
    )),  
    new ColumnDescriptor(array(  
        'name' => 'score:'  
    )),  
);  
*/ 
$tableName = "student";  
$tableName = "data57_webpage";  
/*
try {  
    $client->createTable($tableName, $columns);  
} catch (AlreadyExists $ae) {  
    echo( "WARN: {$ae->message} <br>" );  
} 
*/ 
//获取表的描述  
//	echo var_dump( get_class_methods("HbaseClient") );
//echo var_dump($solrResults);
$colu=array("f:cnt");
$colu2=array("p:c");
//$limitt=array("LIMIT => 10");
//$rawstart='cn.eol.zaizhi:http/bschool_xlfd_10029/20140820/t20140820_1165942.shtml';
//$rawstart='cn.eol.zaizhi:http/bschool_xlfd_10029/20130808/t20130808_998647.shtml';
//$rawaa='cn.eol.zaizhi:http/bschool_xlfd_10029/20140811/t20140811_1162357.shtml';
	//$testa = $client->scannerOpenWithStop($tableName,$rawstart,$rawstart,$colu2);  
	#$attss = $client->scannerGet($testa,3);
	#$attss2 = $client->scannerGet($testa);
	//a$testa = $client->getRow($tableName,$);  
//$scan = new TScan();
//$scan->caching=200;
//$scan->startRow='cn.eol.zaizhi:http/bschool_xlfd_10029/';
//$scan->stopRow='cn.eol.zaizhi:http/bschool_xlfd_10029/20130903/t20130903_1010842.shtml';
//echo var_dump($scan)."<br>";
//$testa = $client->scannerOpenWithScan($tableName,$scan);
//$attss = $client->scannerGetList($testa,10);
//echo var_dump($rawKey);

//	$testa = $client->getRow($tableName,$rawKey[3]);
//	echo var_dump($testa[0]->columns);
	//echo var_dump($testa[0]->columns['p:sig']->value);
//exit(0);
if($debugTs) echo microtime_float()."<br>";
foreach ($rawKey as $rawk){
	//echo "<br>-------------<br>";
if($debugTs) echo "1: start search hbase DB.--------------".microtime_float()."<br>";
	$testa = $client->getRow($tableName,$rawk);
	//$rawkTs = $testa[0]->columns['f:cnt']->timestamp;
	$urlKey = $testa[0]->columns['f:bas']->value;
	$rawkTs = substr(md5($urlKey), 23);
	$testFcnt = $testa[0]->columns['f:cnt']->value;
	$testb=iconv("GB2312", "UTF-8", $testFcnt);

if($debugTs) echo "3: start find title in HtmlParser------".microtime_float()."<br>";
	$p_title = $testa[0]->columns['p:t']->value;
//echo "<a href=index.php?ts=$rawkTs>".iconv("UTF-8", "GB2312", $p_title->getPlainText())."</a><br>";
echo "<a href=index.php?ts=$rawkTs>".$p_title."</a> -- "."<a href=$urlKey target='_blank'>".$urlKey."</a><br>";

//echo iconv("UTF-8", "GB2312", $p_mcontent->getPlainText());
//echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";

if ( "$tsUrl" == "$rawkTs" ){
	if($debugTs) echo "2: start HtmlParser object.------------".microtime_float()."<br>";
	$html_dom = new HtmlParser\Parser( $testb );
	if($debugTs) echo "4: start find mcontert in HtmlParser---".microtime_float()."<br>";
	$pNum = 0;
	echo "<a name='tips'><table border='1' bgcolor='PaleGreen'><tr><td>";
	while( $p_mcontent = $html_dom -> find('div#mcontent',$pNum) ){
		//echo var_dump($p_mcontent->getPlainText());
		//echo "<p>".iconv("UTF-8", "GB2312", $p_mcontent->getPlainText())."</p>";
		echo "<p>".$p_mcontent->getPlainText()."</p>";
		$pNum++;
	}
	echo '</td></tr></table></a>';
}
}

if($debugTs) echo microtime_float()."<br>";
exit(0);




















foreach ( $rawKey as $idU){
	$testa = $client->getRow($tableName,$idU);  
	//echo "<br>". $testa[0] ->columns["f:cnt"] ->value ;
	$html_dom = new HtmlParser\Parser( $testa[0] ->columns["f:cnt"] ->value );
	$p_array = $html_dom -> find('div#mcontent',0);
	echo iconv("UTF-8","GB2312//IGNORE", $p_array->getPlainText());
	//foreach ($p_array as $p){
    		//echo $p->getPlainText();
		//echo var_dump($p);
	//}
	//echo "<br>".var_dump($p_array)."<br>";
	echo "<br>------ttss end =================-------<br>";
}
//$trpc=$testa[0] ->columns["f:cnt"] ->value;
//$conres=iconv("UTF-8","GB2312//IGNORE",$trpc);
//echo var_dump($testa)."<br>";
//echo var_dump($attss)."<br>";
//echo var_dump($attss2)."<br>";
	echo "-------------<br>";
	echo "-------------<br>";
$transport->close();
exit(0);



$descriptors = $client->getColumnDescriptors($tableName);  
asort($descriptors);  
$row_name = 0;
foreach ($descriptors as $col) {  
    	echo var_dump($col)."<br>";
	echo( "  column: {$col->name}, maxVer: {$col->maxVersions} <br>" );  
	echo "-------------<br>";
	$arr = $client->get($tableName, $row_name, $col->name);  
	echo var_dump($arr)."<br>";
	echo "-------------<br>";
	$row_name++;
}  

	echo "-------------<br>";
	echo "-------------<br>";
//	$arr = $client->get("data2_webpage", 'cn.eol.zaizhi:http/bschool_xlfd_10029/20140820/t20140820_1165942.shtml', 'p:c');  
	echo var_dump($arr)."<br>";
	echo "-------------<br>";
$row_name = '2';  
$fam_col_name = 'score';  
#$arr = $client->get($tableName, $row_name, $fam_col_name);  
//$arr = $client->get($tableName, $row_name, $fam_col_name);  
//echo "=============================== <br>";
//echo var_dump($arr)."<br>";

$transport->close();
exit(0);
//修改表列的数据  
$row = '2';  
$valid = "foobar-世界杯";  
$mutations = array(  
    new Mutation(array(  
        'column' => 'score',  
        'value' => $valid  
    )),  
);  
//$client->mutateRow($tableName, $row, $mutations);  
  
  
//获取表列的数据  
$row_name = '0';  
$fam_col_name = 'f';  
$arr = $client->get($tableName, $row_name, $fam_col_name);  
// $arr = array  
foreach ($arr as $k => $v) {  
// $k = TCell  
    echo ("value = {$v->value} , <br>  ");  
    echo ("timestamp = {$v->timestamp}  <br>");  
}  
  
$arr = $client->getRow($tableName, $row_name);  
// $client->getRow return a array  
foreach ($arr as $k => $TRowResult) {  
// $k = 0 ; non-use  
// $TRowResult = TRowResult  
    var_dump($TRowResult);  
}  
  
$transport->close();  
?>
