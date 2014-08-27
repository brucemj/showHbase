<?php  
//header("Content-Type: text/html; charset=gb2312");
//header("Content-Type: text/html; charset=utf-8");
//ini_set('display_errors', E_ALL);  
$GLOBALS['THRIFT_ROOT'] = './libs';  

require_once( $GLOBALS['THRIFT_ROOT'] . '/Thrift.php' );
require_once( $GLOBALS['THRIFT_ROOT'] . '/transport/TSocket.php' );
require_once( $GLOBALS['THRIFT_ROOT'] . '/transport/TBufferedTransport.php' );
require_once( $GLOBALS['THRIFT_ROOT'] . '/protocol/TBinaryProtocol.php' );
require_once( $GLOBALS['THRIFT_ROOT'] . '/packages/Hbase/Hbase.php' );
require_once('Apache/Solr/Service.php');
require_once('html-parser/src/Parser.php');

class GrabData {
	public static $ThriftServer = '172.21.12.58';
	public static $ThriftPort = 9090;
	public static $ThriftSocket = false;
	public static $ThriftClient = false;
	
	public static $HbaseRawId = array();
	public static $HbaseTableName = "data57_webpage";  
	public static $HbaseData = false;

	public static $SolrClient = false;
	public static $SolrResults = false;

	public static $Query = 'title:专业';   // 搜索关键字
	public static $Limit = 20;             // 搜索结果的最大条数
	public static $debugTs = false;        // 时间调试开关
	public static $PageFcntUtf8 = array();        // 时间调试开关
	
	public static $UlrQuery =  false;
	
	function __construct(){
                self::ThriftOpen();
                self::SolrSearchQ();
		self::$UlrQuery = isset($_REQUEST['ts']) ? $_REQUEST['ts'] : false;
        }

        public static function ThriftOpen(){
		if( ! self::$ThriftSocket )
                        self::$ThriftSocket = new TSocket(self::$ThriftServer, self::$ThriftPort);
                if( ! self::$SolrClient )
                        self::$SolrClient = new Apache_Solr_Service('172.21.12.58', 8983, '/solr/');
                if( ! self::$ThriftClient ){
                        self::$ThriftSocket->setSendTimeout(10000);
                        self::$ThriftSocket->setRecvTimeout(20000);
                        $transport = new TBufferedTransport(self::$ThriftSocket);
                        $protocol = new TBinaryProtocol($transport);
                        self::$ThriftClient = new HbaseClient($protocol);
                        $transport->open();
                }
        }

        public static function SolrSearchQ(){
                try
                {
                        self::$SolrResults = self::$SolrClient->search(self::$Query, 0, self::$Limit);
                }
                catch (Exception $e)
                {
                        die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
                }
        }

        public function SolrSummary(){
                // display results summary
                if (self::$SolrResults)
                {
                        $total = (int) self::$SolrResults -> response -> numFound;
                        $start = min(1, $total);
                        $end = min(self::$Limit, $total);
                        echo "<div>Results  $start - $end  of $total </div>";
                }
        }

	public function HbaseSetRawId(){
                if (self::$SolrResults){
                        foreach (self::$SolrResults->response->docs as $docs ){
                                foreach ($docs as $field => $value){
                                        if ( $field == "id"){
                                                self::$HbaseRawId[] = $value;
                                        }
                                }
                        }
                }
        }

        public function HbaseGetPageList(){
		$this -> HbaseSetRawId();
		$this -> SolrSummary();
                foreach (self::$HbaseRawId as $rawId){
                        if(self::$debugTs) echo "<br> start search hbase DB for title.--------------".self::microtime_float()."<br>";
                        self::$HbaseData = self::$ThriftClient->getRow(self::$HbaseTableName,  $rawId);
                        $originUrl = self::$HbaseData[0]->columns['f:bas']->value;
                        $tsHash = substr(md5($rawId), 23);

                        $PageFcnt = self::$HbaseData[0]->columns['f:cnt']->value;
                        self::$PageFcntUtf8[$tsHash]=iconv("GB2312", "UTF-8", $PageFcnt);

                        $PageTitle = self::$HbaseData[0]->columns['p:t']->value;
			$aass = $_SERVER['PHP_SELF'];
                        echo "<div class='container'><section class='link-braces'>";
			if( self::$Query )
				echo "<a name=$tsHash href=$aass?ts=$tsHash#$tsHash>".$PageTitle."</a> <br>&nbsp&nbsp&nbsp&nbsp&nbsp -- "."<a href=$originUrl target='_blank'>".$originUrl."</a><br>";
			else
				echo "<a name=$tsHash href=$aass?ts=$tsHash#$tsHash>".$PageTitle."</a> <br>&nbsp&nbsp&nbsp&nbsp&nbsp -- "."<a href=$originUrl target='_blank'>".$originUrl."</a><br>";
                        echo "</section></div>";
                        if(self::$debugTs) echo " end search hbase DB for title.----------------".self::microtime_float()."<br>";
			
			$this -> PageDatashow($rawId);
                }
        }

	public function PageDatashow($rawId){
			$tsHash = substr(md5($rawId), 23);
			if ( $tsHash == self::$UlrQuery ){
				echo "&nbsp&nbsp&nbsp&nbsp<a href=index.php>返回</a><br>";
				$html_dom = new HtmlParser\Parser( self::$PageFcntUtf8[$tsHash] );

				$pNum = 0;
				echo "<a name='tips'><table border='1' bgcolor='PaleGreen'><tr><td>";
				//eregi
				while( $p_mcontent = $html_dom -> find('div#mcontent',$pNum) ){
					//echo var_dump($p_mcontent)."<br>";
					echo "<p>".$p_mcontent->getPlainText()."</p>";
					$pNum++;
				}
				echo '</td></tr></table></a>';
			}
	}



        public static function microtime_float(){
                list($usec, $sec) = explode(" ", microtime());
                return ((float)$usec + (float)$sec);
        }

// end class
}
?>
