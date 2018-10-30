<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "login_check.php";
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$sDeleteYN = $_SESSION['staffproductYN'];

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$page_no = intval(($_GET['page_no']) ? $_GET['page_no'] : "1");
	$CardType = ($_GET['CardType']) ? $_GET['CardType'] : $_POST['CardType'];
	$cardid = ($_GET['cardid']) ? $_GET['cardid'] : $_POST['cardid'];
	$name = ($_GET['name']) ?  $_GET['name'] :  $_POST['name'];
	$phoneno = ($_GET['phoneno']) ? $_GET['phoneno'] : $_POST['phoneno'];
	$chk_info = ($_GET['chk_info']) ? $_GET['chk_info'] : $_POST['chk_info'];
	
	if($chk_info == "") { $chk_info = 1; }
	$CardType = $chk_info;

//	if($chk_info == "Customer")			$strType = 1;
//	else if($chk_info == "Vendor")		$strType = 2;
//	else if($chk_info == "Employee")	$strType = 3;
//	else								$strType = 0;

	$today = date("Y-m-d");

	if ($CardType != "" && $where == "")	$where = "WHERE CardType=".$CardType;
	else if($CardType != "")				$where = $where." AND CardType=".$CardType;

	if ($cardid != "" && $where == "")		$where = "WHERE cardid=".$cardid;
	else if($cardid != "")					$where = $where." AND cardid=".$cardid;

	if ($name != "" && $where == "")		$where = "WHERE name like '%".Br_dconv($name)."%' ";
	else if($name != "")					$where = $where." AND name='".Br_dconv($name)."' ";

	if (trim($phoneno) != "" && $where == "") $where = "WHERE phone='".$phoneno."' ";
	else if(trim($phoneno) != "")			  $where = $where." AND phone='".$phoneno."' ";

	if ($where != "")	$where = $where." AND CID = '".$cId."' ";
	else				$where = "WHERE CID = '".$cId."' ";

	if ($chk_info != "" && $CardType == "")	$where = $where." AND CardType = ".$chk_info." ";

	$Query = "SELECT * FROM Card ".$where;
//echo $Query;
	if($mode == "1") {
		$rst2 = mssql_query($Query);
		$row2 = mssql_fetch_array($rst2);

		if($row2['CardID'] != "") {
			$cardid = $row2['CardID'];
			$name = Br_iconv($row2['Name']);
			$phoneno = $row2['Phone'];
			$stype = $row2['cType'];
			$sstatus = $row2['Status'];
			$cterm = $row2['cTerm'];
			$climit = $row2['cLimit'];
			$CustomList1 = Br_iconv($row2['CustomList1']);
			$CustomList2 = Br_iconv($row2['CustomList2']);
			$CustomList3 = Br_iconv($row2['CustomList3']);
			$CustomField1 = Br_iconv($row2['CustomField1']);
			$CustomField2 = Br_iconv($row2['CustomField2']);
			$CustomField3 = Br_iconv($row2['CustomField3']);
		}
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script language="JavaScript" src="js/date_picker.js"></script>
<script>
function moveTag(id) 
{ 
    var tag = document.getElementById(id); 
    var pos_y = 0; 
    var obj = tag; 
    
    // 좌표계산 
    while(obj.offsetParent) 
    { 
        pos_y = pos_y + parseInt(obj.offsetTop); 
        obj = obj.offsetParent; 
    } 
    pos_y = pos_y + parseInt(obj.offsetTop); 
    pos_y = pos_y - parseInt(tag.offsetHeight); 
    
    document.body.scrollTop = pos_y; 
} 

var db = (document.body) ? 1 : 0
var scroll = (window.scrollTo) ? 1 : 0

function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "="
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else {
    begin += 2
  }
  var end = document.cookie.indexOf(";", begin);
  if (end == -1) end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function saveScroll() {
  if (!scroll) return
  var now = new Date();
  now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000);
  var x = (db) ? document.body.scrollLeft : pageXOffset;
  var y = (db) ? document.body.scrollTop : pageYOffset;
  setCookie("xy", x + "_" + y, now);
}

function loadScroll() {
  if (!scroll) return
  var xy = getCookie("xy");
  if (!xy) return
  var ar = xy.split("_");
  if (ar.length == 2) scrollTo(parseInt(ar[0]), parseInt(ar[1]));
}

function card_cls()	{

	document.getElementById('cardid').value = '';
	document.getElementById('name').value = '';
	document.getElementById('phoneno').value = '';
	document.getElementById('stype').value = '';
	document.getElementById('sstatus').value = '';
	document.getElementById('CustomList1').value = '';
	document.getElementById('CustomList2').value = '';
	document.getElementById('CustomList3').value = '';
	document.getElementById('CustomField1').value = '';
	document.getElementById('CustomField2').value = '';
	document.getElementById('CustomField3').value = '';

	document.location.href='card.php';
}

function search(preChkInfo) {
	cardid = document.getElementById('cardid').value;
	name = document.getElementById('name').value;
	phoneno = document.getElementById('phoneno').value;

	var obj = document.getElementsByName('chk_info');
	var checked_index = -1;
	var checked_value = '';
	for( i=0; i<obj.length; i++) {
		if(obj[i].checked) {
			checked_index = i;
			checked_value = obj[i].value;
		}
	}
	if(checked_value != preChkInfo) {
		document.location.href='card.php?chk_info='+checked_value;
	} else {
		document.location.href='card.php?cardid='+cardid+'&name='+name+'&phoneno='+phoneno+'&chk_info='+checked_value;
	}
}

function search_id(e)	{
	if (e.keyCode == 13) {
//		chk_info = document.getElementById('chk_info').value;
		cardid = document.getElementById('cardid').value;

		var obj = document.getElementsByName('chk_info');
		var checked_index = -1;
		var checked_value = '';
		for( i=0; i<obj.length; i++) {
			if(obj[i].checked) {
				checked_index = i;
				checked_value = obj[i].value;
			}
		}

		if(checked_value < 1) {
			alert("구분을 선택 하세요.");
			document.frm.chk_info.focus();
		}

		//document.frm.submit();
		document.location.href='card.php?mode=1&cardid='+cardid+'&chk_info='+checked_value;
	}
}
function search_phone(e)	{
	if (e.keyCode == 13) {
		phoneno = document.getElementById('phoneno').value;
		document.location.href='card.php?phoneno='+phoneno;
	}
}
function card(cId)	{
	cardid = document.getElementById('cardid').value;
	name = document.getElementById('name').value;

	if(name == "") {
		alert("Card NAME을 입력하세요.");
		document.frm.name.focus();
		return false;
	}

	phoneno = document.getElementById('phoneno').value;
	stype = document.getElementById('stype').value;
	sstatus = document.getElementById('sstatus').value;
	terms = document.getElementById('terms').value;
	limit = document.getElementById('limit').value;

	CustomList1 = document.getElementById('CustomList1').value;
	CustomList2 = document.getElementById('CustomList2').value;
	CustomList3 = document.getElementById('CustomList3').value;
	CustomField1 = document.getElementById('CustomField1').value;
	CustomField2 = document.getElementById('CustomField2').value;
	CustomField3 = document.getElementById('CustomField3').value;

	var target = document.forms.frm;
	var answer = confirm("입력/수정 하시겠습니까?");
	if(answer) {

//		target.mode.value = "update";
//		target.submit();
		document.location.href='card_update.php?mode=update&cId='+cId+'&cardid='+cardid+'&name='+name+'&phoneno='+phoneno+'&stype='+stype+'&sstatus='+sstatus+'&terms='+terms+'&limit='+limit+'&CustomList1='+CustomList1+'&CustomList2='+CustomList2+'&CustomList3='+CustomList3+'&CustomField1='+CustomField1+'&CustomField2='+CustomField2+'&CustomField3='+CustomField3;
	}
}

function card_del(cId,deleteYN)	{
	stype = document.getElementById('stype').value;
	cardid = document.getElementById('cardid').value;

	if(cardid == "") {
		alert("Card ID를 입력하세요.");
		document.frm.cardid.focus();
		return false;
	}

	if(deleteYN == 1) {
		var target = document.forms.frm;
		var answer = confirm("정말로 삭제 하시겠습니까?");
		if(answer) {
			document.location.href='card_update.php?mode=delete&cId='+cId+'&cardid='+cardid+'&stype='+stype;
//			target.mode.value = "delete";
//			target.submit();
		}
	} else {
		alert("삭제할 권한이 없습니다.");
	}

}
function search_code(cid,chkinfo) {
	var search_key = document.getElementById("cardid").value;
	if(search_key) {
		document.getElementById("code_iframe").src = "search_card.php?mode=code&cId="+cid+"&chkinfo="+chkinfo+"&key="+search_key;
		var pos = document.getElementById("cardid").getBoundingClientRect();
		document.getElementById("search_code_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_code_display").style.top = pos.top + 20 + "px";
	} else {
		document.getElementById("cardid").value = "";
		alert("검색할 Card ID를 입력하세요.");
	}
}
function select_code(cardid,name,phone,ctype,sstatus,cterm,climit,cust1,cust2,cust3,field1,field2,field3) {
	var div = document.getElementById("search_code_display");
	document.getElementById("cardid").value = cardid;
	document.getElementById("name").value = name;
	document.getElementById("phoneno").value = phone;
	document.getElementById("stype").value = ctype;
	document.getElementById("sstatus").value = sstatus;
	document.getElementById("CustomList1").value = cust1;
	document.getElementById("CustomList2").value = cust2;
	document.getElementById("CustomList3").value = cust3;
	document.getElementById("CustomField1").value = field1;
	document.getElementById("CustomField2").value = field2;
	document.getElementById("CustomField3").value = field3;
	div.style.display = "none";
}
function showhide_code()	{
	var div = document.getElementById("search_code_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}
function search_name(cid,chkinfo) {
	var search_key = document.getElementById("name").value;
	if(search_key) {
		document.getElementById("name_iframe").src = "search_card.php?mode=name&cId="+cid+"&chkinfo="+chkinfo+"&key="+search_key;
		var pos = document.getElementById("name").getBoundingClientRect();
		document.getElementById("search_name_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_name_display").style.top = pos.top + 20 + "px";
	} else {
		document.getElementById("name").value = "";
		alert("검색할 Card Name을 입력하세요.");
	}
}
function select_name(cardid,name,phone,ctype,sstatus,cterm,climit,cust1,cust2,cust3,field1,field2,field3) {
	var div = document.getElementById("search_name_display");
	document.getElementById("cardid").value = cardid;
	document.getElementById("name").value = name;
	document.getElementById("phoneno").value = phone;
	document.getElementById("stype").value = ctype;
	document.getElementById("sstatus").value = sstatus;
	document.getElementById("CustomList1").value = cust1;
	document.getElementById("CustomList2").value = cust2;
	document.getElementById("CustomList3").value = cust3;
	document.getElementById("CustomField1").value = field1;
	document.getElementById("CustomField2").value = field2;
	document.getElementById("CustomField3").value = field3;
	div.style.display = "none";
}
function showhide()	{
	var div = document.getElementById("search_name_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}
</script>

</head>
<body <? /*onLoad="loadScroll()" onUnload="saveScroll()" */?>>
<?
include_once "includes/header.html";
include_once "includes/menu.html";
?>
<form name="frm" method="post" action="card_update.php" style="margin-bottom:0;">
<table>
	<tr>
		<td width="101px" class="doc_title"><b>■ CARD File</b></td>
		<td class="doc_title">
			<input type="radio" name="chk_info" value="1" <?if($chk_info=='1') { echo 'checked'; }?>>Customer
			<input type="radio" name="chk_info" value="2" <?if($chk_info=='2') { echo 'checked'; }?>>Vendor
		</td>
		<td align="right" width="66px"><input style="font-size:9pt;height:25" type="button" value="Inquiry" class="btn_style" onClick="return search(<?=$chk_info?>);"/></td>
	</tr>
</table>
<div id="container" style="width:1024px">
<b class="rtop">
<b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b>
</b>
<div class="box">
<table style="background-color:#bfdfff; width:1000px"">
	<tr>
		<td class="doc_field_l"><b>Card ID:</b></td>
		<td><input style="background-color: #e2e2e2;" class="doc_field_s" id="cardid" name="cardid" type="text" <?/*readonly*/?> value="<?=$cardid?>" onkeypress="if (event.keyCode==13){ search_code('<?=$cId?>','<?=$chk_info?>'); event.returnValue=false}"/></td>
		<td class="doc_field_r"><b>NAME:</b></td>
		<td colspan="3"><input style="background-color:#e2e2e2;" class="doc_field_sd" id="name" name="name" type="text" value="<?=$name?>" onKeyPress="if (event.keyCode==13){ search_name('<?=$cId?>','<?=$chk_info?>'); event.returnValue=false}"/></td>
		<td colspan="2" align="left"><input style="font-size:9pt;height:25" type="button" value=" Clear " class="btn_style" onClick="return card_cls();"/></td>
	</tr>
	<tr>
		<td class="doc_field_l"><b>PHONE No:</b></td>
		<td><input style="background-color: #e2e2e2;" class="doc_field_s" id="phoneno" name="phoneno" type="text" size="20" value="<?=$phoneno?>" onkeypress="return search_phone(event)"/></td>
		<td class="doc_field_r"><b>Type:</b></td>
		<td>
			<select id="stype" name="stype">
			  <option value=""></option> 
			  <option value="Customer" <? if($stype == 'Customer') { echo 'Selected'; } ?>>Customer</option> 
			  <option value="Vendor" <? if($stype == 'Vendor') { echo 'Selected'; } ?>>Vendor</option>
			</select>		
		</td>
		<td class="doc_field_r"><b>Status:</b></td>
		<td>
			<select id="sstatus" name="sstatus">
			  <option value=""  <? if($sstatus == "")  { echo 'Selected'; } ?>></option> 
			  <option value="1" <? if($sstatus == "1") { echo 'Selected'; } ?>>Active</option> 
			  <option value="2" <? if($sstatus == "0") { echo 'Selected'; } ?>>InActive</option>
			</select>		
		</td>
		<td class="doc_field_r"><b>TERMS:</b></td>
		<td><input class="doc_field_80" id="terms" name="terms" type="text" value="<?=$cterm?>"/> day</td>
	</tr>
	<tr>
		<td class="doc_field_l"><b>CustomList&nbsp; &nbsp;#1:</b></td>
		<td><input class="doc_field_s" id="CustomList1" name="CustomList1" type="text" value="<?=$CustomList1?>"/></td>
		<td class="doc_field_r"><b>CustomList&nbsp; &nbsp;#2:</b></td>
		<td><input class="doc_field_s" id="CustomList2" name="CustomList2" type="text" value="<?=$CustomList2?>"/></td>
		<td class="doc_field_r"><b>CustomList&nbsp; &nbsp;#3:</b></td>
		<td><input class="doc_field_s" id="CustomList3" name="CustomList3" type="text" value="<?=$CustomList3?>"/></td>
		<td class="doc_field_r"><b>LIMIT:</b></td>
		<td><input class="doc_field_80" id="limit" name="limit" type="text" value="<?=$climit?>"/> $</td>
	</tr>
	<tr>
		<td class="doc_field_l"><b>CustomField #1: </b></td>
		<td><input class="doc_field_s" id="CustomField1" name="CustomField1" type="text" value="<?=$CustomField1?>"/></td>
		<td class="doc_field_r"><b>CustomField #2: </b></td>
		<td><input class="doc_field_s" id="CustomField2" name="CustomField2" type="text" value="<?=$CustomField2?>"/></td>
		<td class="doc_field_r"><b>CustomField #3: </b></td>
		<td><input class="doc_field_s" id="CustomField3" name="CustomField3" type="text" value="<?=$CustomField3?>"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td colspan="2" align="left" width="185px">
			<input style="font-size:9pt;height:25" type="button" value=" Save " class="btn_style" onClick="card('<?=$cId?>')"/>
			<input style="font-size:9pt;height:25" type="button" value=" Delete " class="btn_style" onClick="card_del('<?=$cId?>','<?=$sDeleteYN?>')"/></td>
	</tr>
</table>
</form>
</div>
<b class="rbottom">
<b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b>
</b>
</div>
<p>
<table style="border-collapse:collapse; width:1024px">
	<tr class="doc_field_purchases_bg" align="center">
		<td width="50px"><font color="#0000cc">CD</font></td>
		<td><font color="#0000cc">NAME</font></td>
		<td width="90px"><font color="#0000cc">PHONE</font></td>
		<td width="75px"><font color="#0000cc">TYPE</font></td>
		<td width="75px"><font color="#0000cc">TERMS</font></td>
		<td width="75px"><font color="#0000cc">LIMIT</font></td>
		<td width="75px"><font color="#0000cc">STATUS</font></td>
		<td width="80px"><font color="#0000cc">LIST#1</font></td>
		<td width="80px"><font color="#0000cc">LIST#2</font></td>
		<td width="80px"><font color="#0000cc">LIST#3</font></td>
<!--
		<td width="80px"><font color="#0000cc">FILED#1</font></td>
		<td width="80px"><font color="#0000cc">FILED#2</font></td>
		<td width="80px"><font color="#0000cc">FILED#3</font></td>
-->
	</tr>
<?

	if($mode==1) {
		if($CardType == "") $CardType = $chk_info;
		$Qry = "SELECT count(*) as row FROM Card WHERE CID='".$cId."' AND CardType=".$CardType;
	} else {
		$Qry = "SELECT count(*) as row FROM Card ".$where;
	}
	$dbraw = mssql_query($Qry);
	$prow = mssql_fetch_array($dbraw);

	$scale = 20;
	$last_page = GetLastPage($prow['row'], $scale);
//	if(DEBUG) echo "row: ".$prow['row']." / "."Scale: ".$scale." / "."Last page: ".$last_page."<br />";
//	if(DEBUG) echo $query."<br />";

	$i=0;
	if($mode==1) {
		if($CardType == "") {
			$OneWhere = "CID='$cId' ";
		} else {
			$OneWhere = "CID='$cId' AND CardType = $CardType ";
		}

		$Query = "SELECT TOP $scale ";
		$Query .= "* ";
		$Query .= "FROM Card ";
		$Query .= "WHERE (CardID NOT IN ";
		$Query .= "(SELECT TOP ".($page_no-1)*$scale." CardID FROM Card WHERE ".$OneWhere." ORDER BY CardID)) ";
		$Query .= "AND ".$OneWhere;
		$Query .= "ORDER BY CardID";
	} else {
		if($where != "") {
			$flwhere = str_replace('WHERE','',$where);
			$flwhere = " AND ".$flwhere;
		}
		if($CardType == "") {
			$OneWhere = "CID='$cId' ";
		} else {
			$OneWhere = "CID='$cId' AND CardType = $CardType ";
		}

		$Query = "SELECT TOP $scale ";
		$Query .= "* ";
		$Query .= "FROM Card ";
		$Query .= "WHERE (CardID NOT IN ";
//		$Query .= "(SELECT TOP ".($page_no-1)*$scale." CardID FROM Card ".$where." ORDER BY CardID,CardType) ".$flwhere.") ";
		$Query .= "(SELECT TOP ".($page_no-1)*$scale." CardID FROM Card WHERE ".$OneWhere." ORDER BY CardID,CardType) AND ".$OneWhere.") ";
		$Query .= "ORDER BY CardID,CardType";
	}
//echo "<br>";
//echo $Query;
	$rst = mssql_query($Query);
	while($row = mssql_fetch_array($rst)) {
		$i++;
		if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
		else				$doc_field_name = "doc_field_purchases";

		if($row['Status'] == "1")	$strStatus = "Active";
		else						$strStatus = "InActive";
?>
	<tr class="<?=$doc_field_name?>">
		<td align="center"><a href="card.php?mode=1&cardid=<?=$row['CardID']?>&CardType=<?=$row['CardType']?>&page_no=<?=$page_no?>&chk_info=<?=$chk_info?>" style="color:#000000; text-decoration:none"><?=$row['CardID']?></td>
		<td><a href="card.php?mode=1&cardid=<?=$row['CardID']?>&CardType=<?=$row['CardType']?>&page_no=<?=$page_no?>&chk_info=<?=$chk_info?>" style="color:#000000; text-decoration:none"><?=Br_iconv($row['Name'])?></td>
		<td align="center"><?=$row['Phone']?></td>
		<td align="center"><?=$row['cType']?></td>
		<td align="center"><?=$row['cTerm']?></td>
		<td align="center"><?=number_format($row['cLimit'])?></td>
		<td align="center"><?=$strStatus?></td>
		<td><?=Br_iconv($row['CustomList1'])?></td>
		<td><?=Br_iconv($row['CustomList2'])?></td>
		<td><?=Br_iconv($row['CustomList3'])?></td>
<!--		<td><?=Br_iconv($row['CustomField1'])?></td>
		<td><?=Br_iconv($row['CustomField2'])?></td>
		<td><?=Br_iconv($row['CustomField3'])?></td>
-->
	</tr>

<?	}
	mssql_close();
?>
</table>
<table style="width:1024px">
	<tr>
		<td align="center">
			<div class="navigation_bar">
			<form method="get" action="?mode=list&chk_info=<?=$chk_info?>">
			<? if($page_no > 1) { ?>
			<a href="?page_no=<? echo ($page_no - 1);?>&chk_info=<?=$chk_info?>" class="arrow">&lt</a>
			<? } ?>
				<label for="page_no"><font size="2"><b>Move page</b></font></label>
				<input type="hidden" id="chk_info" name="chk_info" value="<?=$chk_info?>" />
				<input type="hidden" id="mode" name="mode" value="list" />
				<input type="text" id="page_no" name="page_no" size="5" value="<? echo ($page_no);?>" /><font size="2"><b>&nbsp;Total: <?=$last_page?>&nbsp;</b>
				<input type="submit" class="arrow movebtn"  value="Move" />
			<? if($page_no < $last_page) { ?>
			<a href="?page_no=<? echo ($page_no + 1);?>&chk_info=<?=$chk_info?>" class="arrow">&gt</a>
			<? } ?>
			</form>
		</td>
	</tr>
</table>		
<div id="search_name_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:482px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0px 0 0 20px; background-color:#ffffff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="20">
						<td style="letter-spacing:-1px; font-weight:bold;">검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="showhide()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="name_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>
<div id="search_code_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:482px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0px 0 0 20px; background-color:#ffffff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="20">
						<td style="letter-spacing:-1px; font-weight:bold;">검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="showhide_code()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="code_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>
</body>
</html>