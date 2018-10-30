<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<?php
	session_start();
	include_once "login_check.php";
	include "includes/common_class2.php";
	include "includes/common_class.php";

	$dbs_servername = "192.168.2.60";
	$dbs_userid = "wssale";
	$dbs_password = "w2015";
	$dbs_dbname = "wholesaleDB";
    define( "DB_SERVER", $dbs_servername );
    define( "DB_USERID", $dbs_userid );
    define( "DB_PASSWORD", $dbs_password );
    define( "DB_NAME", $dbs_dbname ); 

	$coId = $_SESSION['staffCID'];
	$sDeleteYN = substr($_SESSION['staffAuthority'],8,1);

	if($coId == "1") $strSID = "TB";
	else if($coId == "2") $strSID = "MN";
	else $strSID = "";

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$page_no = intval(($_GET['page_no']) ? $_GET['page_no'] : "1");
	$Barcode = ($_GET['Barcode']) ? $_GET['Barcode'] : $_POST['Barcode'];

	$searchId = ($_GET['searchId']) ? $_GET['searchId'] : $_POST['searchId'];
	$searchName = ($_GET['searchName']) ?  $_GET['searchName'] :  $_POST['searchName'];

	$conn = mssql_connect( DB_SERVER, DB_USERID, DB_PASSWORD) or die("Database failed to response.");  
	//mssql_select_db( DB_NAME, $conn );
	//mssql_select_db( DB_NAME0, $conn );

	$today = date("Y-m-d");

	$stype = 0;
	$where = "";
	
	if($searchId != "") {
		$where = "WHERE Barcode like '".$searchId."%' ";
	}

	if($mode == "1") {
			$Onewhere = "WHERE Barcode='".$Barcode."' AND a.CID='".$coId."' ";
			$Query = "SELECT Barcode,Unit,Up_wsCode,Up_ProdOwnCode,Up_Barcode,Up_Unit,Up_Inside_qty,".
								"wsCode,ProdOwnCode,SuppCode,prodId,prodName,prodKname,prodType,prodType2,".
								"prodOUprice,prodBalance,prodTax, prodUnit,prodSize,useYN ".
							"FROM tfBarcodeLink a LEFT JOIN Inventory_Item b ON a.CID=b.CID AND Barcode=prodId ".$Onewhere;
//echo $Query;
		$rst2 = mssql_query($Query,$conn);
		$row2 = mssql_fetch_array($rst2);

		if($row2['Barcode'] != "") {

			$array2 = getGalProdName($row2['Barcode']);
			$Barcode = $row2['Barcode'];
			$GalCode =  $array2[4];
			$ProdOwnCode =  $array2[5];
			$prodKname = $array2[0];
			$prodName = $array2[1];
			$Unit = $array2[3];

			$Up_Barcode = $row2['Up_Barcode'];
			$Up_Unit = $row2['Up_Unit'];
			$Up_Inside_qty = $row2['Up_Inside_qty'];

			$up_array = getwsProdName($row2['Up_wsCode'],$row2['Up_ProdOwnCode'],$coId);

			$Up_prodKname = $up_array[0];
			$Up_prodName = $up_array[1];
			$Up_GalCode = $up_array[4];
			$Up_ProdOwnCode = $up_array[5];
		}
	}
/*
	else {
			$Barcode = "";
			$Unit = "";
			$Up_Barcode = "";
			$Up_Unit = "";
			$Up_Inside_qty = "";
			$wsCode = "";
			$ProdOwnCode = "";
			$SuppCode = "";
			$prodKname = "";
			$prodName = "";
			$Up_prodKname = "";
			$Up_prodName = "";
			$Up_GalCode = "";
			$Up_ProdOwnCode = "";
	} */
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script language="JavaScript" src="js/date_picker.js">
// ㅇㄴㅇㄴㅁ
</script>
<script>
function search_wscode(coId) {
	var search_key = document.getElementById("Barcode").value;
	if(search_key) {
		document.getElementById("wscode_iframe").src = "search_galcode.php?mode=wscode&coId="+coId+"&key="+search_key;
		var pos = document.getElementById("Barcode").getBoundingClientRect();
		document.getElementById("search_wscode_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_wscode_display").style.top = pos.top + 20 + "px";
	} else {
		alert("검색할 Whole Code를 입력하세요.");
	}
}
function select_wscode(wscode,owncode,upc,kname,name,unit,mode) {
	var div  = document.getElementById("search_wscode_display");
	var div2 = document.getElementById("search_name_display");
	document.getElementById("GalCode").value = wscode;
	document.getElementById("ProdOwnCode").value = owncode;
	document.getElementById("Barcode").value = upc;
	document.getElementById("prodKname").value = kname;
	document.getElementById("prodName").value = name;
	document.getElementById("Unit").value = unit;
	if(mode == "wscode")
		div.style.display = "none";
	else if(mode == "name")
		div2.style.display = "none";
}
function showhide()	{
	var div = document.getElementById("search_wscode_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}
function search_name(coId) {
	var search_key = document.getElementById("prodKname").value;
	if(search_key) {
		document.getElementById("name_iframe").src = "search_galcode.php?mode=name&coId="+coId+"&key="+search_key;
		var pos = document.getElementById("prodKname").getBoundingClientRect();
		document.getElementById("search_name_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_name_display").style.top = pos.top + 20 + "px";
	} else {
		alert("검색할 Whole Code를 입력하세요.");
	}
}
function showhide_name()	{
	var div = document.getElementById("search_name_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}

function search_upcode(coId) {
	var search_key = document.getElementById("Up_GalCode").value;
	if(search_key) {
		document.getElementById("upcode_iframe").src = "search_galcode.php?mode=upcode&coId="+coId+"&key="+search_key;
		var pos = document.getElementById("Up_GalCode").getBoundingClientRect();
		document.getElementById("search_upcode_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_upcode_display").style.top = pos.top + 20 + "px";
	} else {
		alert("검색할 Whole Code를 입력하세요.");
	}
}
function select_upcode(wscode,owncode,upc,kname,name,unit,mode) {
	var div  = document.getElementById("search_upcode_display");
	var div2 = document.getElementById("search_upname_display");
	document.getElementById("Up_GalCode").value = wscode;
	document.getElementById("Up_ProdOwnCode").value = owncode;
	document.getElementById("Up_Barcode").value = upc;
	document.getElementById("Up_prodKname").value = kname;
	document.getElementById("Up_prodName").value = name;
	document.getElementById("Up_Unit").value = unit;
	if(mode == "upcode")
		div.style.display = "none";
	else if(mode == "upname")
		div2.style.display = "none";

}
function showhide_upcode()	{
	var div = document.getElementById("search_upcode_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}

function search_upname(coId) {
	var search_key = document.getElementById("Up_prodKname").value;
	if(search_key) {
		document.getElementById("upname_iframe").src = "search_galcode.php?mode=upname&coId="+coId+"&key="+search_key;
		var pos = document.getElementById("Up_prodKname").getBoundingClientRect();
		document.getElementById("search_upname_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_upname_display").style.top = pos.top + 20 + "px";
	} else {
		alert("검색할 Whole Code를 입력하세요.");
	}
}
function showhide_upname()	{
	var div = document.getElementById("search_upname_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}

function search()
{
	var obj = document.getElementsByName('chk_info');
	var checked_index = -1;
	var checked_value = '';
	for( i=0; i<obj.length; i++) {
		if(obj[i].checked) {
			checked_index = i;
			checked_value = obj[i].value;
		}
	}
	document.location.href='product_mapping.php?mode=all&chk_info='+checked_value;
}
function search_id(e)
{
	if (e.keyCode == 13) {
		Barcode = document.getElementById('Barcode').value;
		document.location.href='product_mapping.php?mode=2&searchId='+Barcode;
	}
}
function change(coId){
	obj = document.getElementById("stype").value; 
	if (obj > 0) {
		url = "product_Rtype2.php?stype="+obj;
		document.getElementById("main_frame").src = "product_Rtype2.php?stype="+obj;	//iFrame
	}
}
function ecoFee(){
	newWindow=window.open("prodEco.php", "prodDeposit", "");	//자식창 OPEN
}
function clearSelectBox(selectBox) {
	if (null == selectBox || null == selectBox.options)	{
		return;
	}
	var length = selectBox.options.length;
	for (var index=0;index<length ;index++)	{ 
		selectBox.options.remove(0);
	}
}
function check(){
    var f=document.frm;
    var checkBox = f.checkbox;
    var result = document.getElementById("prodPromo");    
    var str = "";
    for(var i=0; i<checkBox.length; i++) {
        if(checkBox[i].checked){
            str += checkBox[i].value + "<br>";
        } 
    }
    result.innerHTML = str;
}
function mapping_append() {
	wcode = document.getElementById("GalCode").value;
	prodId = document.getElementById("Barcode").value;

	upcode = document.getElementById("Up_GalCode").value;
	upprodId = document.getElementById("Up_Barcode").value;
	contenteach = document.getElementById("contenteach").value;

	if(wcode == "" || upcode == "") {
		alert("Wholesale CODE를 입력하세요.");
		document.frm.GalCode.focus();
		return false;
	}
	if(prodId == "" || upprodId == "") {
		alert("Barcode를 입력 하세요.");
		document.frm.prodId.focus();
		return false;
	}
	if(contenteach == "") {
		alert("Content를 입력 하세요.");
		document.frm.contenteach.focus();
		return false;
	}

	var target = document.forms.frm;
	var answer = confirm("아이템을 ADD 하시겠습니까?");
	if(answer) {
		target.mode.value = "append";
		target.submit();
	}
}
function mapping_update() {
	wcode = document.getElementById("GalCode").value;
	prodId = document.getElementById("Barcode").value;

	upcode = document.getElementById("Up_GalCode").value;
	upprodId = document.getElementById("Up_Barcode").value;
	contenteach = document.getElementById("contenteach").value;

	if(wcode == "" || upcode == "") {
		alert("Wholesale CODE를 입력하세요.");
		document.frm.GalCode.focus();
		return false;
	}
	if(prodId == "" || upprodId == "") {
		alert("Barcode를 입력 하세요.");
		document.frm.prodId.focus();
		return false;
	}
	if(contenteach == "") {
		alert("Content를 입력 하세요.");
		document.frm.contenteach.focus();
		return false;
	}

	var target = document.forms.frm;
	var answer = confirm("아이템을 수정 하시겠습니까?");
	if(answer) {
		target.mode.value = "update";
		target.submit();
	}
}
function mapping_delete(deleteYN, page_no) {
	wcode = document.getElementById("GalCode").value;
	prodId = document.getElementById("Barcode").value;

	upcode = document.getElementById("Up_GalCode").value;
	upprodId = document.getElementById("Up_Barcode").value;

//	if(wcode == "" || upcode == "") {
//		alert("Wholesale CODE를 입력하세요.");
//		document.frm.GalCode.focus();
//		return false;
//	}

	if(prodId == "") {
		alert("Barcode를 입력 하세요.");
		document.frm.prodId.focus();
		return false;
	}

	if(deleteYN == 1) {
		var target = document.forms.frm;
		var answer = confirm("정말로 삭제 하시겠습니까?");
		if(answer) {
			target.mode.value = "delete";
			target.page_no.value = page_no;
			target.submit();
		}
	} else {
		alert("삭제할 권한이 없습니다.");
	}
}
function crImeMode(obg) { 
	obg.value = obg.value.replace(/[\ㄱ-ㅎ가-힣]/g, ''); 
}
</script>
</head>
<body>
<?
include_once "includes/header.html";
include_once "includes/menu.html";
?>
<form name="frm" method="post" action="item_mapping_update.php" style="margin-bottom:0;">
<table>
	<tr>
		<td width="207px" class="doc_title"><b>■ Inventory &gt Item Mapping</b></td>
<!--		<td class="doc_title">
			<input type="radio" name="chk_info" value="0" <?if($chk_info=='0') { echo 'checked'; }?>><?= $strSID?>
			<input type="radio" name="chk_info" value="1" <?if($chk_info=='1') { echo 'checked'; }?>>BBY
			<input type="radio" name="chk_info" value="2" <?if($chk_info=='2') { echo 'checked'; }?>>SRY
		</td>
		<td align="right" width="66px">
			<input style="font-size:9pt;height:25" type="button" value="Inquiry" class="btn_style" onClick="search()"/>
		</td>	-->
	</tr>
</table>
<div id="container" style="width:1024px">
<b class="rtop">
<b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b>
</b>
<div class="box">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="page_no" value="<?=$page_no?>">
<table style="background-color:#bfdfff; width:1000px">
	<tr>
		<td width="90px" class="doc_field_l"><b>Item CODE:</b></td>
		<td width="155px"><input style="background-color: #e2e2e2;" class="doc_field_90" id="GalCode" name="GalCode" type="text" value="<?=$GalCode?>" />
		<input class="doc_field_40" id="ProdOwnCode" name="ProdOwnCode" type="text" size="20" value="<?=$ProdOwnCode?>"/></td>
		<td width="90px" class="doc_field_r"><b>Item NAME:</b></td>
		<td><input style="background-color: #e2e2e2;" class="doc_field_270" id="prodKname" name="prodKname" type="text" value="<?=$prodKname?>" onKeyPress="if (event.keyCode==13){ search_name('<?=$coId?>'); event.returnValue=false}"/></td>
		<td width="90px" class="doc_field_r"><b>English NAME:</b></td>
		<td><input class="doc_field_270" id="prodName" name="prodName" type="text" value="<?=$prodName?>"/></td>
	</tr>
	<tr>
		<td class="doc_field_l"><b>Barcode:</b></td>
		<td><input style="background-color: #e2e2e2;" class="doc_field_140" id="Barcode" name="Barcode" type="text" value="<?=$Barcode?>" onKeyPress="if (event.keyCode==13){ search_wscode('<?=$coId?>'); event.returnValue=false}"/></td>
		<td width="76px" class="doc_field_r"><b>UNIT:</b></td>
		<td colspan="3" class="doc_field_100">
			<select id="Unit" name="Unit" style="width:84px;">
			  <option value="BOX" <? if($Unit == 'BOX')	{ echo 'Selected'; } ?>>BOX</option> 
			  <option value="BAG" <? if($Unit == 'BAG')	{ echo 'Selected'; } ?>>BAG</option> 
			  <option value="CTN" <? if($Unit == 'CTN') { echo 'Selected'; } ?>>CTN</option> 
			  <option value="CASE" <? if($Unit == 'CASE') { echo 'Selected'; } ?>>CASE</option>
			  <option value="MLT" <? if($Unit == 'MLT') { echo 'Selected'; } ?>>MLT</option>
			  <option value="EA" <? if($Unit == 'EA') { echo 'Selected'; } ?>>EA</option>
			  <option value="PK" <? if($Unit == 'PK') { echo 'Selected'; } ?>>PK</option>
			  <option value="LB" <? if($Unit == 'LB') { echo 'Selected'; } ?>>LB</option>
			  <option value="KG" <? if($Unit == 'KG') { echo 'Selected'; } ?>>KG</option>
			  <option value="OZ" <? if($Unit == 'OZ') { echo 'Selected'; } ?>>OZ</option>
			</select>		
		</td>
	</tr>
</table>
<br>
<table style="background-color:#6699cc; width:1000px">
	<tr>
		<td width="90px" class="doc_field_l"><b>UPPER CODE:</b></td>
		<td width="155px"><input style="background-color: #e2e2e2;" class="doc_field_90" id="Up_GalCode" name="Up_GalCode" type="text" value="<?=$Up_GalCode?>" onKeyPress="if (event.keyCode==13){ search_upcode('<?=$coId?>'); event.returnValue=false}"/>
		<input class="doc_field_40" id="Up_ProdOwnCode" name="Up_ProdOwnCode" type="text" size="20" value="<?=$Up_ProdOwnCode?>"/></td>
		<td width="90px" class="doc_field_r"><b>Item NAME:</b></td>
		<td colspan="3"><input style="background-color: #e2e2e2;" class="doc_field_270" id="Up_prodKname" name="Up_prodKname" type="text" value="<?=$Up_prodKname?>" onKeyPress="if (event.keyCode==13){ search_upname('<?=$coId?>'); event.returnValue=false}"/></td>
		<td width="90px" class="doc_field_r"><b>English NAME:</b></td>
		<td colspan="2"><input class="doc_field_270" id="Up_prodName" name="Up_prodName" type="text" value="<?=$Up_prodName?>"/></td>
	</tr>
	<tr>
		<td class="doc_field_l"><b>Barcode:</b></td>
		<td><input style="background-color: #e2e2e2;" class="doc_field_140" id="Up_Barcode" name="Up_Barcode" type="text" value="<?=$Up_Barcode?>" onkeypress="return search_id(event)"/></td>
		<td width="76px" class="doc_field_r"><b>UNIT:</b></td>
		<td class="doc_field_100">
			<select id="Up_Unit" name="Up_Unit" style="width:84px;">
			  <option value="BOX" <? if($Up_Unit == 'BOX')	{ echo 'Selected'; } ?>>BOX</option> 
			  <option value="CTN" <? if($Up_Unit == 'CTN') { echo 'Selected'; } ?>>CTN</option> 
			  <option value="CASE" <? if($Up_Unit == 'CASE') { echo 'Selected'; } ?>>CASE</option>
			  <option value="EA" <? if($Up_Unit == 'EA') { echo 'Selected'; } ?>>EA</option>
			  <option value="PK" <? if($Up_Unit == 'PK') { echo 'Selected'; } ?>>PK</option>
			  <option value="LB" <? if($Up_Unit == 'LB') { echo 'Selected'; } ?>>LB</option>
			  <option value="KG" <? if($Up_Unit == 'KG') { echo 'Selected'; } ?>>KG</option>
			  <option value="OZ" <? if($Up_Unit == 'OZ') { echo 'Selected'; } ?>>OZ</option>
			</select>		
		</td>
		<td width="59px" class="doc_field_r"><b>Content:</b></td>
		<td><input class="doc_field_90" id="contenteach" name="contenteach" type="text" value="<?=$Up_Inside_qty?>"/>
		</td>
		<td></td>
		<td align="left"><input type="button" value="&nbsp; Add &nbsp;" class="btn_style" onClick="mapping_append()"/>&nbsp;&nbsp;
		<input type="button" value=" Save " class="btn_style" onClick="mapping_update()"/>&nbsp;&nbsp;
		<input type="button" value="Delete" class="btn_style" onClick="mapping_delete('<?=$sDeleteYN?>','<?=$page_no?>')"/></td>
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
		<td width="110px"><font color="#0000cc">UPC</font></td>
		<td width="40px"><font color="#0000cc">UNIT</font></td>
		<td><font color="#0000cc">ITEM NAME</font></td>
		<td width="80px"><font color="#0000cc">SIZE</font></td>
		<td width="110px"><font color="#0000cc">UPPER ITEM</font></td>
		<td width="40px"><font color="#0000cc">UNIT</font></td>
		<td><font color="#0000cc">ITEM NAME</font></td>
		<td width="80px"><font color="#0000cc">SIZE</font></td>
		<td width="50px"><font color="#0000cc">CONTENT</font></td>
	</tr>
<?
    $scale1 = 20; //page

	$Qry = "SELECT count(*) as row FROM tfBarcodeLink a LEFT JOIN Inventory_Item b ON a.CID=b.CID AND Barcode=prodId ";
	$dbraw = mssql_query($Qry,$conn);
	$prow = mssql_fetch_array($dbraw);

	$scale = 20;
	$last_page = GetLastPage($prow['row'], $scale);

	$IT_where = $where_keyword;
	$IT_field = "Barcode,Unit,Up_wsCode,Up_ProdOwnCode,Up_Barcode,Up_Unit,Up_Inside_qty,".
						"wsCode,ProdOwnCode,SuppCode,prodId,prodName,prodKname,".
						"prodType,prodType2,prodOUprice,prodBalance,prodTax, prodUnit,prodSize,useYN ";
	//상품 전체 수 구하는 쿼리
    $row_que = "select count(Barcode) as row from tfBarcodeLink a LEFT JOIN Inventory_Item b ON a.CID=b.CID AND Barcode=prodId ".
						"WHERE a.CID='".$coId."' ";
	$row_sel = mssql_query($row_que, $conn);
    $row_fat = mssql_fetch_array($row_sel); 
    $row = $row_fat['row'];
    //페이지 인덱스 구하기
    $cpage_que = $page_no * $scale1;
    if($cpage_que == $scale1)
    {
        $cpage_que = "0";
    }
    else
    {
        $cpage_que = $cpage_que - $scale1;
    }

    //마지막 장 갯수 구하기
    $IT_top = $row - $cpage_que;
    if($IT_top > $scale1)
    {
        $IT_top = $scale1;
    }
    else
    {
        $IT_top = $IT_top;
    }

	$i=0;

	$Query = "SELECT top $IT_top $IT_field FROM tfBarcodeLink a LEFT JOIN Inventory_Item b ON a.Barcode=b.prodId ".
					"WHERE a.Barcode not in (SELECT top $cpage_que c.Barcode ".
					"FROM tfBarcodeLink c LEFT JOIN Inventory_Item d ON c.CID=C.CID AND c.Barcode=d.prodId ) ";
//echo $Query;
	$rst = mssql_query($Query, $conn);
	while($row = mssql_fetch_array($rst)) {
		$i++;
		if ($i % 2 == 0)		$doc_field_name = "doc_field_purchases_bg";
		else						$doc_field_name = "doc_field_purchases";

		$array = getGalProdName($row['Barcode']);

//echo $array[0]."=".$array[1]."=".$array[2]."=".$array[3]."=".$array[4];

		if($array[1] != "")		$array_name = $array[0]." ".$array[1];
		else							$array_name = $array[0];

		if($row['prodName'])	$item_name = Br_iconv($row['prodKname'])." ".$row['prodName'];
		else								$item_name = Br_iconv($row['prodKname']);

		$arraylist = getwsProdName($row['Up_wsCode'],$row['Up_ProdOwnCode'],$coId);
		$sprodKname = $arraylist[0]." ".$arraylist[1];

?>
	<tr class="<?=$doc_field_name?>">
		<td align="left" style="padding:0 0 0 5px"><a href="?mode=1&Barcode=<?=$row['Barcode']?>&searchName=<?=$searchName?>&searchId=<?=$searchId?>&page_no=<?=$page_no?>" style="color: #000000; text-decoration:none"><?=$row['Barcode']?></td>
		<td align="center"><?=$array[3]?></td>
		<td align="left" style="padding:0 0 0 5px"><a href="?mode=1&Barcode=<?=$row['Barcode']?>&searchName=<?=$searchName?>&searchId=<?=$searchId?>&page_no=<?=$page_no?>" style="color: #000000; text-decoration:none"><?=$array_name?></td>
		<td align="center"><?=Br_iconv($array[2])?></td>
		<td align="left" style="padding:0 0 0 5px"><?
			if($row['Up_Barcode']) echo $row['Up_Barcode'];
			else echo $row['Up_wsCode'];
			?></td>
		<td align="center"><?=$arraylist[3]?></td>
		<td align="left" style="padding:0 0 0 5px"><?=$sprodKname;?></td>
		<td align="center"><?=$arraylist[2];?></td>
		<td align="center"><?=$row['Up_Inside_qty']?></td>
	</tr>
<?	}
	mssql_close();
?>
</table>
<table style="width:1024px">
	<tr>
		<td align="center">
			<div class="navigation_bar">
			<form method="get" action="?mode=list">
			<? if($page_no > 1) { ?>
			<a href="?mode=<?=$mode?>&searchName=<?=$searchName?>&searchId=<?=$searchId?>&page_no=<? echo ($page_no - 1);?>" class="arrow">&lt</a>
			<? } ?>
				<label for="page_no"><font size="2"><b>Move page</b></font></label>
				<input type="hidden" id="page" name="page" value="sales" />
				<input type="hidden" id="mode" name="mode" value="list" />
				<input type="text" id="page_no" name="page_no" size="5" value="<? echo ($page_no);?>" /><font size="2"><b>&nbsp;Total: <?=$last_page?>&nbsp;</b></font>
				<input type="submit" class="arrow movebtn"  value="Move" />
			<? if($page_no < $last_page) { ?>
			<a href="?mode=<?=$mode?>&searchName=<?=$searchName?>&searchId=<?=$searchId?>&page_no=<? echo ($page_no + 1);?>" class="arrow">&gt</a>
			<? } ?>
			</form>
		</td>
	</tr>
</table>		
<div>
<iframe src="" width="0" height="00" frameborder="0" border="0" scrolling="yes" bgcolor=#EEEEEE bordercolor="#FF000000" marginwidth="0" marginheight="0" name="main_frame" id="main_frame"></iframe>
</div>

<div id="search_wscode_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:525px; left:0px; top:0px;">
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
			<td><iframe id="wscode_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

<div id="search_name_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:525px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0px 0 0 20px; background-color:#ffffff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="20">
						<td style="letter-spacing:-1px; font-weight:bold;">검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="showhide_name()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="name_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

<div id="search_upcode_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:525px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0px 0 0 20px; background-color:#ffffff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="20">
						<td style="letter-spacing:-1px; font-weight:bold;">검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="showhide_upcode()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="upcode_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

<div id="search_upname_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:525px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0px 0 0 20px; background-color:#ffffff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="20">
						<td style="letter-spacing:-1px; font-weight:bold;">검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="showhide_upname()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="upname_iframe" width="100%" height="200" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

</body>
</html>
