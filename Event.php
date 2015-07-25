<!document html>
<html>
    <head>
        <meta charset="utf-8">
        <title>上下班酒測警報(桃園/高雄)</title>
<link href="/images/econ.ico" rel="SHORTCUT ICON">
<link href="reset.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="../themes/metro-gray/easyui.css" rel="stylesheet" type="text/css" >
<link href="/css/jquery.cluetip.css" rel="stylesheet" type="text/css" />
<link href="/css/portal.css" rel="stylesheet" type="text/css" >
<link href="/css/demo.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/js/jquery.portal.js"></script>
<script type="text/javascript" src="/js/jquery.hoverIntent.js"></script>
<script type="text/javascript" src="/js/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="/js/jquery.cluetip.js"></script>
<script type="text/javascript" src="/js/theclock.js"></script>
<script type="text/javascript" src="/js/demo.js"></script>
<?php
####################################################
#程式名稱:Event.php
#功能說明:上下班酒測警報(當地)
#修改日期:2014.10.14
#參數使用:
#修改人員:游清池
#####################################################
if( $_SERVER["REMOTE_ADDR"]=='203.74.125.135' ) { 
	echo '
<script type="text/javascript" src="http://tpts.tw-roc.org:8081/socket.io/socket.io.js"></script>';
} else {
	echo '
<script type="text/javascript" src="http://192.168.1.199:8081/socket.io/socket.io.js"></script>';
}
require("../include/config.php");
require("../include/function.php");
$index=$localInfo['index'];
$action=$localInfo['action'];	
$wtdate = date("Ymd");
if(!isset($_GET['date'])) $date=$wtdate;
else $date = $_GET['date'];

$SQLAE05="SELECT `remask`,`logtime`,a.`workno` as pno,`num`,`logupdate`,b.name,`area` FROM `drv_red` a "
." LEFT JOIN `personal` b ON ( b.cardwebno=a.`workno`) "
." WHERE `logtime` LIKE '".$date."%' AND `mode`='AE05' AND `num`='0.000' GROUP BY logtime ORDER BY `logtime` DESC ";

$SQLAE06="SELECT `remask`,`logtime`,a.`workno` as pno,`num`,`logupdate`,b.name,`area` FROM `drv_red` a "
." LEFT JOIN `personal` b ON ( b.cardwebno=a.`workno`) "
." WHERE `logtime` LIKE '".$date."%' AND `mode`='AE06' AND `num`='0.000' GROUP BY logtime ORDER BY `logtime` DESC ";

$SQLAENoPass="SELECT `remask`,`logtime`,a.`workno` as pno,`num`,`logupdate`,b.name,`area` FROM `drv_red` a "
." LEFT JOIN `personal` b ON ( b.cardwebno=a.`workno`) "
." WHERE `logtime` LIKE '".$date."%' AND `num`!='0.000' GROUP BY logtime ORDER BY `logtime` DESC ";
function pnotr($pno,$row){
	$name='';
	switch ($pno) { 
		case '030100001':
			$name="董事長";
			break;
		case '020002001':	
			$name="協理";
			break;
		default:	
			$name=$row['name'];			
	}
	if($name=='') return '查無!!';
	else return $name;				
}

?>		
	
<script type="text/javascript">
$(document).ready(function(){
	$('#pp').portal({ border:false,	fit:true });
	setInterval( function() { 
		var seconds = new Date().getSeconds(); var minutes = new Date().getMinutes();var hours = new Date().getHours(); 
		if(seconds==1 && minutes==10 && hours==0){ 
			var newDate = new Date(); var yyyy = newDate.getFullYear();  var mm = newDate.getMonth()+1;   var dd = newDate.getDate(); 
			var yyyymmdd = yyyy; yyyymmdd += ((mm < 10) ? "0" : "")+mm; yyyymmdd += ((dd < 10) ? "0" : "")+dd; 
			var url = 'Event.php?date='+yyyymmdd; toUrl(url); 
		}  
	},1000);
<?php				
if( $_SERVER["REMOTE_ADDR"]=='203.74.125.135' ) echo ' var nodejs_server = "tpts.tw-roc.org:8081";';
else echo '	var nodejs_server = "192.168.1.199:8081";';  
//<img src="http://192.168.1.250/ksalcohojpg/'+jpg+'.jpg" />
?>  				
	var socket = io.connect("http://" + nodejs_server);
	socket.on("get_response", function (b) { var combine = b.key + "_" + b.act;
	var jpg = b.jpg;//20130710103148
	console.log(combine);
switch (combine) {
	case "AE00_changebg":
		$("#show_Main").html(''); 
		$("#EventPage").dialog("open"); //2014.10.14酒測器故障所以暫時不彈跳與警報
		show_Name2(jpg); show_AE00();
		setTimeout(function () { 
			show_AE00(); $("#Eventlog").html('酒測未過:'+jpg); 
			$("#main").html('<img src="http://192.168.1.250/ksalcohojpg/'+jpg+'.jpg" />');
			$("#callman").html('<iframe src="/swfplayer.php?url=/os_data/callman.mp3" width="10" height="30" marginwidth="0" #marginheight="0" scrolling="no" frameborder="0" align="center"></iframe>');
			$.messager.confirm('確認!','請手動解除警報?',function(r){ if (r){ $("#callman").html(''); } });
		}, 500);
		$("#callman").html('');
		break;		
	case "AE05_changebg":
		$("#show_Main").html(''); 
		$("#EventPage").dialog("open");
		show_Name2(jpg); show_AE05();
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); show_AE05(); $("#Eventlog").html('上班拒測:'+jpg);
			$("#main").html('<img src="http://192.168.1.250/ksalcohojpg/'+jpg+'.jpg" />');
			$("#callman").html('<iframe src="/swfplayer.php?url=/os_data/callman.mp3" width="10" height="30" marginwidth="0" #marginheight="0" scrolling="no" frameborder="0" align="center"></iframe>');
			$.messager.confirm('確認!','請手動解除警報?',function(r){ if (r){ $("#callman").html(''); } });				
		}, 500);
		break;
	case "AE06_changebg":
		$("#show_Main").html(''); 
		$("#EventPage").dialog("open"); show_Name2(jpg); show_AE06();	
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); show_AE06(); $("#Eventlog").html('下班拒測:'+jpg); 
			$("#main").html('<img src="http://192.168.1.250/ksalcohojpg/'+jpg+'.jpg" />');
			$("#callman").html('<iframe src="/swfplayer.php?url=/os_data/callman.mp3" width="10" height="30" marginwidth="0" #marginheight="0" scrolling="no" frameborder="0" align="center"></iframe>');
			$.messager.confirm('確認!','請手動解除警報?',function(r){ if (r){ $("#callman").html(''); } });				
		}, 500);
		break;	
	case "AE02_changebg":
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); $("#Eventlog").html('上班酒測:'+jpg); }, 500);
		break;
	case "AE04_changebg":
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); $("#Eventlog").html('下班酒測:'+jpg);  }, 500);
		break;
	case "AE01_changebg":
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); $("#Eventlog").html('上班刷卡:'+jpg); }, 500);
		break;
	case "AE03_changebg":
		setTimeout(function () { $("body").css("background-color", 'F2FBEF'); $("#Eventlog").html('下班刷卡:'+jpg); }, 500);
		break;												
	}
 });
	
});
	function show_Name(jpg){ $.get("<?php echo $action;?>",{ cmd : "show_Name", id : jpg } ,function(data){ $("#showName").html(data) }); }
	function show_Name2(jpg){ $.get("<?php echo $action;?>",{ cmd : "show_Name", id : jpg } ,function(data){ $("#showName2").html(data) }); }
	function toUrl(url){ document.location = url;}
	function show_Main(pno){ $.get("<?php echo $action;?>",{ cmd : "show_Main", id : pno } ,function(data){ $('#show_Main').html(data); $("#main").html(''); $("#EventPage").dialog("open");  }); }			
	function show_AE00(){  $.get("<?php echo $action;?>",{ cmd : "AE00", date : "<?php echo $wtdate;?>" } ,function(data){ $('#NoPass').html(data);  }); }			
	function show_AE05(){  $.get("<?php echo $action;?>",{ cmd : "AE05", date : "<?php echo $wtdate;?>" } ,function(data){ $('#AE05').html(data);  }); }	
	function show_AE06(){  $.get("<?php echo $action;?>",{ cmd : "AE06", date : "<?php echo $wtdate;?>" } ,function(data){ $('#AE06').html(data);  }); }		
</script>
    </head>
<body>

<div id="wui">
    <div id="conten">
	<h4>上下班酒測警報 (<span id="hours"> </span><span id="point">:</span><span id="min"> </span><span id="point">:</span><span id="sec"> </span>)</h4>
	最新資訊:<span id="Eventlog" style="color:#FFFFFF; background-color:#000000"></span><span id="showName" style="color:#FFFFFF; background-color:#000000"></span>
	<div id="pp" style="width:100%;">
		<div style="width:100%">		
<?php
echo '
        <div id="NoPass" title="酒測未過" collapsible="true" closable="true" style=" height:150px;">
        <ol>';
		//echo $SQLAENoPass;
	$rsch = sysDbSet($SQLAENoPass);
	while($row = mysql_fetch_array($rsch)){ 
		$logtime=$row['logtime']; $gobacktime=substr($logtime,8,2).":".substr($logtime,10,2).":".substr($logtime,12,2);
		$pno=$row['pno'];$remark=$row['remask']; $area=$row['area']; $pcode=substr($pno,0,2); $name=pnotr($pno,$row); 
		if($area=='' || $area=='01'){ $userIMG="tp"; $apno="台北測試:".$pno; }
		if($area=='02' && $pcode=='02'){ $userIMG="ky"; $apno="桃園運務:".$pno; }
		if($area=='03' && $pcode=='03'){ $userIMG="ks"; $apno="高雄運務:".$pno; }
		if($area=='02' && $pcode=='03'){ $userIMG="ksud"; $apno="高雄=>桃園運務:".$pno; }
		if($area=='03' && $pcode=='02'){ $userIMG="kyud"; $apno="桃園=>高雄運務:".$pno; }	
		echo '<li><table width="100%"><tr>';
		echo '<td><div><span class="wleft"><img src="/images/'.$userIMG.'.png" title="'.$remark.'"/></span><span class="wright">'.$name.'</span></div></td>';
		echo '<td><div><span class="idinfor"><img src="/images/id.png" title="'.$apno.'"></span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/full-time.png" /></span><span class="wright">'.$gobacktime.'</span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/winebowl.png" /></span><span class="wright">'.$row['num'].'</span></div></td>';
		echo '<td><a class="basic" onclick="show_Main('.$logtime.')"><img src="/images/photography.png"  /></a></td>';
		echo '	</tr></table></li>';
	}			
echo '
        </ol>
        </div>';	
echo '
        <div id="AE05" title="上班拒測" collapsible="true" closable="true" style=" height:150px;">
        <ol>';

	$rsch = sysDbSet($SQLAE05);
	while($row = mysql_fetch_array($rsch)){ 
		$logtime=$row['logtime']; $gobacktime=substr($logtime,8,2).":".substr($logtime,10,2).":".substr($logtime,12,2);
		$pno=$row['pno'];$remark=$row['remask']; $area=$row['area']; $pcode=substr($pno,0,2); $name=pnotr($pno,$row); 
		if($area=='' || $area=='01'){ $userIMG="tp"; $apno="台北測試:".$pno; }
		if($area=='02' && $pcode=='02'){ $userIMG="ky"; $apno="桃園運務:".$pno; }
		if($area=='03' && $pcode=='03'){ $userIMG="ks"; $apno="高雄運務:".$pno; }
		if($area=='02' && $pcode=='03'){ $userIMG="ksud"; $apno="高雄=>桃園運務:".$pno; }
		if($area=='03' && $pcode=='02'){ $userIMG="kyud"; $apno="桃園=>高雄運務:".$pno; }	
		echo '<li><table width="100%"><tr>';
		echo '<td><div><span class="wleft"><img src="/images/'.$userIMG.'.png" /></span><span class="wright">'.$name.'</span></div></td>';
		echo '<td><div><span class="idinfor"><img src="/images/id.png" title="'.$apno.'"></span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/full-time.png" /></span><span class="wright">'.$gobacktime.'</span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/winebowl.png" /></span><span class="wright">'.$row['num'].'</span></div></td>';
		echo '<td><a class="basic" onclick="show_Main('.$logtime.')"><img src="/images/photography.png"  /></a></td>';
		echo '	</tr></table></li>';
	}			
echo '
        </ol>
        </div>';	
echo '
        <div id="AE06" title="下班拒測" collapsible="true" closable="true" style=" height:150px;">
        <ol>';

	$rsch = sysDbSet($SQLAE06);
	while($row = mysql_fetch_array($rsch)){ 
		$logtime=$row['logtime']; $gobacktime=substr($logtime,8,2).":".substr($logtime,10,2).":".substr($logtime,12,2);
		$pno=$row['pno'];$remark=$row['remask']; $area=$row['area']; $pcode=substr($pno,0,2); $name=pnotr($pno,$row); 
		if($area=='' || $area=='01'){ $userIMG="tp"; $apno="台北測試:".$pno; }
		if($area=='02' && $pcode=='02'){ $userIMG="ky"; $apno="桃園運務:".$pno; }
		if($area=='03' && $pcode=='03'){ $userIMG="ks"; $apno="高雄運務:".$pno; }
		if($area=='02' && $pcode=='03'){ $userIMG="ksud"; $apno="高雄=>桃園運務:".$pno; }
		if($area=='03' && $pcode=='02'){ $userIMG="kyud"; $apno="桃園=>高雄運務:".$pno; }	
		echo '<li><table width="100%"><tr>';
		echo '<td><div><span class="wleft"><img src="/images/'.$userIMG.'.png" /></span><span class="wright">'.$name.'</span></div></td>';
		echo '<td><div><span class="idinfor"><img src="/images/id.png" title="'.$apno.'"></span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/full-time.png" /></span><span class="wright">'.$gobacktime.'</span></div></td>';
		echo '<td><div><span class="wleft"><img src="/images/winebowl.png" /></span><span class="wright">'.$row['num'].'</span></div></td>';
		echo '<td><a class="basic" onclick="show_Main('.$logtime.')"><img src="/images/photography.png"  /></a></td>';
		echo '	</tr></table></li>';
	}			
echo '
        </ol>
        </div>';	
echo '
      </div>
        </div>
  </div>
    </div>';		
	echo '<div id="EventPage" title="警報視窗" class="easyui-dialog" style="width:280px;height:255px;padding:0px;" closed="true" >';	
	echo '<span id="showName2" style="color:#FFFFFF; background-color:#000000"></span>';
    echo '	<div id="main"></div>';
	echo '	<div id="callman"></div>';
	echo '	<div id="show_Main"></div>';
	echo '</div>';
?>

</body>
</html>
