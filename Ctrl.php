<?php
require( __DIR__ . '/lib/ElephantIO/Client.php');
use ElephantIO\Client as ElephantIOClient;
//程式名稱與用途:本地端199網頁事件觸發cgi
//使用方法:
//http://kyts.tw-roc.org/os_data/Ctrl.php?key=AE00&jpg=20130710103148
//#/usr/local/bin/php Ctrl.php AE00 20130710103148
//事件觸發訊號 send //- debug -  Sent 5:::{"name":"send","args":[{"key":"AE00","act":"changebg","jpg":"20130710103148"}]}
//回送訊號到顯示頁 get_response //websocket writing 5:::{"name":"get_response","args":[{"key":"AE00","act":"changebg","jpg":"20130710103148"}]}

if(isset($_SERVER["argv"])){ $mykey=$_SERVER["argv"][1]; $myjpg=$_SERVER["argv"][2];}
else { $mykey=$_GET['key'];$myjpg=$_GET['jpg'];}
$ps=rand(1234,9876); $passwd=str_pad($ps,4,'0',STR_PAD_LEFT);
$cno="key_".date("Ymd")."_".strftime("%H%M%S").$passwd;	
$key="key_".date("Ymd")."_".strftime("%H");
//if(isset($_SERVER["argv"])){
	//socket.io php實做
	//連線
	$elephant = new ElephantIOClient('http://192.168.1.199:8081', 'socket.io', 1, false, true, true);
	$elephant->init();
	//$elephant->send( ElephantIOClient::TYPE_EVENT, null, null, '{"name":"send","args":[{"key":"AE00","act":"changebg","jpg":"20130710103148"}]}' );
	$elephant->send( ElephantIOClient::TYPE_EVENT, null, null, '{"name":"send","args":[{"key":"'.$mykey.'","act":"changebg","jpg":"'.$myjpg.'"}]}' );
	$elephant->close();
/*
//} else { 
if($mykey!='' && $myjpg!=''){
	echo '
<!DOCTYPE html>
<html>
<head>
    <title>Nodejs - 觸發網頁</title>
    <script src="http://192.168.1.199:8081/socket.io/socket.io.js" type="text/javascript"></script>
    <script type="text/javascript">
        var socket = io.connect("http://192.168.1.199:8081");
        socket.emit("send", { key: "'.$mykey.'", act: "changebg", jpg: "'.$myjpg.'"});
    </script>
</head>
</html>';
	}
}
*/
?>
