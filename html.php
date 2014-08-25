<?php
require_once('html-parser/src/Parser.php');
use HtmlParser\Parser;
$html = '<html>
  <head>
    <meta charset="utf-8">
    <title>test</title>
  </head>
  <body>
    <p class="test_class test_class1">p1</p>
    <p class="test_class test_class2">p2</p>
    <p class="test_class test_class3">p3</p>
    <div id="test1">测试1</div>
  </body>
</html>';
//现在支持直接输入html字符串了
$html_dom = new Parser($html);
//$html_dom->parseStr($html);
$p_array = $html_dom->find('p.test_class');
$p1 = $html_dom->find('p.test_class1',0);
$div = $html_dom->find('div#test1',0);
foreach ($p_array as $p){
    echo $p->getPlainText()."<br>";
}
echo $div->getPlainText()."<br>";
echo $p1->getPlainText()."<br>";
echo $p1->class."<br>";
?>
