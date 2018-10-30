<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<?php
	session_start();
	include_once "login_check.php";
	include "includes/db_configms.php";
	include "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$staffId = $_SESSION['staffID'];
	$sDeleteYN = $_SESSION['staffproductYN'];

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$page_no = intval(($_GET['page_no']) ? $_GET['page_no'] : $_POST['page_no']);

	$vendor_cd = ($_GET['vendor_cd']) ? $_GET['vendor_cd'] : $_POST['vendor_cd'];
	$vendorname = ($_GET['vendorname']) ? $_GET['vendorname'] : $_POST['vendorname'];
	$target_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];
	$target_date2 = ($_GET['target_date2']) ? $_GET['target_date2'] : $_POST['target_date2'];
	$seq = ($_GET['seq']) ? $_GET['seq'] : $_POST['seq'];

	$toDay = date("Y-m-d");
	if($target_date == "" && $target_date2 == "") 	{
		$target_date = substr($toDay,0,8)."01";
		$target_date2 = $toDay;
	}

	if($vendorname != "") $vendorname = Br_dconv(urldecode($key));
	if($vendor_cd !="" && $vendorname=="") $vendorname = Br_iconv(readCard($cId,$vendor_cd,2));

	$stype = 0;
	$where = "";

	if($target_date != "") 	$where_date = "pm_date BETWEEN '".$target_date."' AND '".$target_date2."' ";
	if($target_date != "") 	$where = "WHERE ".$where_date;

	if($where != "")	$where = $where."AND CID='".$cId."'";
	else				$where = "WHERE CID='".$cId."'";

	if($vendor_cd != "") $where = $where." AND pm_vendor_cd=".$vendor_cd;

	function getVendorName($key)
	{
		$query = "SELECT ";
	}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script language="JavaScript" src="js/date_picker.js">
// ㅇㄴㅇㄴㅁ
</script>
<style type="text/css">
html, 
body {
	height: 90%;
}
</style>
<script>

function search_vendor(cid) {
	var search_key = document.getElementById("vendorname").value;
	if(search_key) {
		document.getElementById("vendor_iframe").src = "search_purchase.php?mode=vendor&cId="+cid+"&key="+search_key;
		var pos = document.getElementById("vendorname").getBoundingClientRect();
		document.getElementById("search_vendor_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_vendor_display").style.top = pos.top + 20 + "px";
	} else {
		document.getElementById("vendor_cd").value = "";
		alert("검색할 Vendor의 전화번호/이름을 입력하세요.");
	}
}

function select_vendor(cardid,name) {
	var div = document.getElementById("search_vendor_display");
	document.getElementById("vendor_cd").value = cardid;
	document.getElementById("vendorname").value = name;
	div.style.display = "none";
}

function select_inquiry() {
	Vcode = document.getElementById('vendor_cd').value;
	Vname = document.getElementById('vendorname').value;
	Idate = document.getElementById('target_date').value;
	Idate2 = document.getElementById('target_date2').value;
	//window.alert(Vcode+" "+encodeURIComponent(Vname)+" "+Idate+" "+Idate2);
	document.location.href='purchase_list.php?vendor_cd='+Vcode+'&vendorname='+encodeURIComponent(Vname)+'&target_date='+Idate+'&target_date2='+Idate2;
}

function purchase_new(cId,sId)
{
	var Vname = document.getElementById("vendorname").value; 
	var Vcode = document.getElementById("vendor_cd").value;

//get_vendor_list2('da');
	if(Vname == ""){
		alert("Vendor 를 먼저 선택해 주세요!!");
		return;
	}

	var pono = window.prompt("Please enter PO# :");
	if(pono == null || pono == "undefined"){
		window.alert("PO#이 입력되지 않았습니다.");
		return;
	}

	if(window.confirm("PO# '"+pono+"' 로 새로운 주문 테이블을 생성합니다.\n 맞으면 확인 버튼을 눌러 주세요.","PO# 입력"))
	{
		var xmlhttp = new XMLHttpRequest();
		var param = "pono=" + pono + "&vcode=" + Vcode + "&cId=" + cId + "&sId=" + sId;

		//window.alert(param);

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if(xmlhttp.responseText[0] == '0')
					newWindow=window.open("purchase_new.php?cId="+cId+"&sId="+sId+"&vendorname="+encodeURIComponent(Vname)+"&vendorcd="+Vcode+"&pono="+pono, "purchase", "");	//자식창 OPEN
				else if(xmlhttp.responseText[0] == '2')
					alert("PO# '"+pono+"' 로 작성중인 테이블이 이미 존재 합니다.");
				else if(xmlhttp.responseText[0] == '3')
					alert("PO# '"+pono+"' 는 이미 등록되어 있습니다.");
				else if(xmlhttp.responseText[0] == '4')
					document.location.replace('login.php');
				else
					alert("실행에 문제가 있습니다. 관리자에게 문의 바랍니다.\n"+xmlhttp.responseText);
			}
		}
		//xmlhttp.open("GET","order_search.php?mode=" + mode + "&key=" + str, true);
		xmlhttp.open("GET","table_exist.php?" + param, true);
		xmlhttp.send();
		
	}
}

function purchase_inquiry(cId,vendor,pono,cDate)
{
	newWindow=window.open("purchase_vendor.php?cId="+cId+"&vendorCode="+vendor+"&pono="+pono+"&target_date="+cDate, "purchase", "");	//자식창 OPEN
}

function change_vid_vname()
{
	alert(document.getElementById('vendorname').value);
	alert(document.getElementById('first_vname').value);
	document.getElementById('vendorname').value=document.getElementById('first_vname').value; 
	document.getElementById('vendor_cd').value=document.getElementById('first_vid').value; 
	
	return false;
}

function get_vendor_list(str) {

	if (str.length == 0) { 
		document.getElementById("search_vendor_display").innerHTML = "";
		return;
	} else {
		document.getElementById("search_vendor_display").style.display = "block";
		if(document.getElementById("vendor_cd").value != "")
			document.getElementById("vendor_cd").value = "";

		var xmlhttp = new XMLHttpRequest();
		var param = "key=" + encodeURIComponent(str);

		//window.alert(param);

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("search_vendor_display").innerHTML = xmlhttp.responseText;
			}
		}
		//xmlhttp.open("GET","order_search.php?mode=" + mode + "&key=" + str, true);
		xmlhttp.open("GET","vendor_search.php?" + param, true);
		xmlhttp.send();
	}    
}

function get_vendor_list2(str) {
	var out_head = "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\"/>" + 
				   "<table width=\"100%\" style=\"border-collapse:collapse;\">";
	var out_body = "";
	var out_bottom = "</table>";
	//&lt;script&gt;	parent.document.getElementById(\"search_vendor_display\").style.display = \"\"&lt;/script&gt";
	var j = 1;

	if (str.length == 0) { 
		document.getElementById("search_vendor_display").innerHTML = "";
		return;
	} else {
		document.getElementById("search_vendor_display").style.display = "block";
		for(var i = 0; i < vendorList.length; i++)
		{
			if(vendorList[i].search(new RegExp(str,"i")) >= 0)
			{
				var tmp = vendorList[i].split(';');

				if (j % 2 == 0)	doc_field_name = "doc_field_bg";
				else			doc_field_name = "doc_field";

				out_body += "<tr class=\""+doc_field_name+"\">";
				out_body +=	"<td align=\"left\">";
				out_body +=	"<a href=\"javascript:parent.select_vendor('"+tmp[0]+"','"+tmp[1]+"');\" style=\"text-decoration:none; font-size:11px\">";
				out_body +=	"("+tmp[0]+") "+tmp[1]+"</a>";
				out_body +=	"<input type=\"hidden\" id=\"vname"+j+"\" value=\""+tmp[1]+"\" />";
				out_body +=	"<input type=\"hidden\" id=\"vid"+j+"\" value=\""+tmp[0]+"\" /></td></tr>";
				
				//if((j % 10) == 0) alert(out_body); 
				
				j++;
			}
		}

		document.getElementById("search_vendor_display").innerHTML = out_head + out_body + out_bottom;
		document.getElementById("search_vendor_display").style.display = "";
	}    
}

function load_vendor_list() {
//	alert("load");

	var xmlhttp = new XMLHttpRequest();

	//document.getElementById("search_vendor_display").style.display = "block";

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			vendorList = xmlhttp.responseText.split('/');
		}
	}
	//xmlhttp.open("GET","order_search.php?mode=" + mode + "&key=" + str, true);
	xmlhttp.open("GET","vendor_search2.php", true);
	xmlhttp.send();
}

function open_under_writing()
{
	var venderId = document.getElementById('po_vendor').value; 
	var orderCreator = document.getElementById('po_creator').value; 
	var e = document.getElementById("under_writing_order");
	var pono = e.options[e.selectedIndex].value;

	if(e.selectedIndex == 0) return;

	newWindow=window.open("purchase_under_writing.php?vendorCode="+venderId+"&pono="+pono+"&creator="+orderCreator, "purchase", "");	//자식창 OPEN

	e.selectedIndex = 0;
}
</script>
</head>
<body onLoad="load_vendor_list();">
<?
include_once "includes/header.html";
include_once "includes/menu.html";

if($staffId == "")
{
	echo "<script> window.alert('로그인 후 다시 이용해 주세요.');</script>";
	return;
}
?>

<table>
	<tr>
		<td class="doc_title"><b>■ Purchases &gt Purchase (구매)</b></td>
	</tr>
</table>
<div id="container" style="width:1024px">
<b class="rtop">
<b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b>
</b>
<div class="box">
<form name="frm" method="post" action="purchase_list.php" style="margin-bottom:0;">
<input type="hidden" name="mode" value="<?=$mode?>">
<table style="background-color:#bfdfff">
	<tr>
		<td width="50px" class="doc_field_l"><b>Vendor:</b></td>
		<td width="200px">
			<input style="background-color: #e2e2e2;" class="doc_field_200" id="vendorname" name="vendorname" type="text" size="20" value="<?=$vendorname?>" onKeyUp="if (event.keyCode!=13) get_vendor_list2(this.value); else return false;" onKeyPress=" if (event.keyCode==13){ return change_vid_vname(); }"/>
			<input type="hidden" id="first_vid" />
			<input type="hidden" id="first_vname" />
		<!-- <td><input style="background-color: #e2e2e2;" class="doc_field_200" id="vendorname" name="vendorname" type="text" size="20" value="<?=$vendorname?>" onKeyUp="get_vendor_list(this.value)" onKeyPress="if (event.keyCode==13){ search_vendor('<?=$cId?>'); event.returnValue=false}"/>
		<td><input style="background-color: #e2e2e2;" class="doc_field_200" id="vendorname" name="vendorname" type="text" size="20" value="<?=$vendorname?>" onFocus="window.alert('onFocus');" onblur="window.alert('onblur');" onKeyPress="if (event.keyCode==13){ search_vendor('<?=$cId?>'); event.returnValue=false}"/>>
			<div id="search_vendor_result" style="position:absolute; left:97px; top:128px; border:1px solid #666666; width:206px; height:72px; display:none; background-color:#F6CECE;"></div> -->

		</td>
		<td width="80px" ><input class="doc_field_80" id="vendor_cd" name="vendor_cd" type="text" value="<?=$vendor_cd?>" readonly/></td>
		<td width="50px" class="doc_field_r"><b>&nbsp;&nbsp;Date</b></td>
		<td width="130px">From: <input style="width:80px" type="text" id="target_date" name="target_date" value="<?=$target_date?>" onClick="datePicker(event,'target_date')"></td>
		<td width="120px">To: <input style="width:80px" type="text" id="target_date2" name="target_date2" value="<?=$target_date2?>" onClick="datePicker(event,'target_date2')"></td>
		<td width="70px" align="center">
			<input type="button" value="Inquiry" class="btn_style" onClick="select_inquiry()"/>
		</td>
		<td width="100px" align="left" width="265px">
			<!-- <input type="button" value="New Order" class="btn_style" onClick="purchase_append('<?=$cId?>')"/> -->
			<input type="button" value="New Order" class="btn_style" onClick="purchase_new('<?=$cId?>','<?=$staffId?>')"/>
		</td>
		<td width="164px" align="left" width="265px">
<?
	$Query = "SELECT table_name = convert(varchar(80), min(o.name)) ".
			 "FROM sysindexes i ".
				"INNER JOIN sysobjects o ".
				"ON (o.id = i.id) ".
			 "WHERE i.indid IN (0, 1, 255) AND  o.xtype = 'U' GROUP BY i.id";

	$rst = mssql_query($Query);
	$first = true;
	while($row = mssql_fetch_array($rst)) {
		
		$table_name = $row['table_name'];
		$words = explode( '_', $table_name );
		if($words[2] == $staffId)
		{
			if($first)
			{
				$first = false;
?>
		<input type="hidden" id="po_vendor" value="<?=$words[1]?>" >
		<input type="hidden" id="po_creator" value="<?=$words[2]?>" >
		<select id="under_writing_order" onchange="open_under_writing()">
			<option value="">작성중인 Order 불러오기</option>
<?
			}
			if($words[2] != "")
			{
?>
			  <option value="<?=$words[3]?>"><?=$words[3]?></option>
<?			}
		}
	}
?>
			</select>
		</td>
	</tr>
</table>
</div>
<b class="rbottom">
<b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b>
</b>
</div>
<?
	$TotalAmt = SumStockTotalAmt($target_date,$venCode,$InvoiceNo,$cId);
	if($TotalAmt > 0){	?>
		<table>
			<tr>
				<td width="5px"></td>
				<td  style="background-color:#c0c0c0" class="doc_field_l">&nbsp;&nbsp;<b>TOTAL AMOUNT: <font color=red><?=number_format($TotalAmt,2)?></font></b></td>
			</tr>
		</table>
<?	} else {	?>
		<p>
<?	}	?>
<table style="border-collapse:collapse; width:1024px">
	<tr class="doc_field_purchases_bg" align="center">
		<td width="80px"><font color="#0000cc">DATE</font></td>
		<td width="110px"><font color="#0000cc">PO #</font></td>
		<td width="110px"><font color="#0000cc">VENDOR INV #</font></td>
		<td><font color="#0000cc">VENDOR NAME</font></td>
		<td width="110px"><font color="#0000cc">AMOUNT</font></td>
		<td width="110px"><font color="#0000cc">AMOUNT DUE</font></td>
		<td width="90px"><font color="#0000cc">STATUS</font></td>
	</tr>
<?
	$Qry = "SELECT count(*) as row FROM purchase_master ".$where;
//echo $Qry;
	$dbraw = mssql_query($Qry);
	$prow = mssql_fetch_array($dbraw);

	$scale = 20;
	$last_page = GetLastPage($prow['row'], $scale);
	if($page_no < 1) $page_no = 1;
	if($page_no > $last_page) $page_no = $last_page;
//	if(DEBUG) echo "row: ".$prow['row']." / "."Scale: ".$scale." / "."Last page: ".$last_page."<br />";
//	if(DEBUG) echo $query."<br />";

	$i=0;
	$preWhere = $where;
	if ($where != "") {
		$where = $where." AND RowNum BETWEEN ".(($page_no-1)*$scale+1)." AND ".((($page_no-1)*$scale)+$scale);
	} else {
		$where = "WHERE RowNum BETWEEN ".(($page_no-1)*$scale+1)." AND ".((($page_no-1)*$scale)+$scale);
	}

	$Query = "SELECT CONVERT(char(10),pm_date,126) AS pm_date,pm_po_no,pm_invoice_no,pm_vendor_cd,TotalAmount,AmountDue,status ".
			 "FROM (SELECT ROW_NUMBER() OVER (ORDER BY pm_date ASC, pm_vendor_cd ASC, pm_po_no ASC) AS RowNum, * ".
			 "FROM purchase_master ".$preWhere.") as K ".$where." ORDER BY pm_date ASC, pm_vendor_cd ASC, pm_po_no ASC ";
	$rst = mssql_query($Query);
	while($row = mssql_fetch_array($rst)) {
		$i++;
		if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
		else				$doc_field_name = "doc_field_purchases";

		$pm_date = $row['pm_date'];
		$pm_po_no = $row['pm_po_no'];
		$pm_vendor_cd = $row['pm_vendor_cd'];
		$pm_vendor_name = getCardName($cId, $row['pm_vendor_cd'], 2)." (".$row['pm_vendor_cd'].")";
		$pm_invoice_no = $row['pm_invoice_no'];
		$TotalAmount = $row['TotalAmount'];
		$AmountDue = $row['AmountDue'];
		$status = $row['status'];
?>
	<tr class="<?=$doc_field_name?>">
		<td align="center"><a href="javascript:purchase_inquiry('<?=$cId?>','<?=$pm_vendor_cd?>','<?=$pm_po_no?>','<?=$pm_date?>')" style="color: #000000; text-decoration:none"><?=$pm_date?></a></td>
		<td align="center"><a href="javascript:purchase_inquiry('<?=$cId?>','<?=$pm_vendor_cd?>','<?=$pm_po_no?>','<?=$pm_date?>')" style="color: #000000; text-decoration:none"><?=$pm_po_no?></a></td>
		<td align="center"><?=$pm_invoice_no?></td>
		<td style="padding:0 0 0 5px" align="left"><a href="javascript:purchase_inquiry('<?=$cId?>','<?=$pm_vendor_cd?>','<?=$pm_po_no?>','<?=$pm_date?>')" style="color: #000000; text-decoration:none"><?=$pm_vendor_name?></a></td>
		<td style="padding:0 5px 0 0" align="right"><?=$TotalAmount?></td>
		<td style="padding:0 5px 0 0" align="right"><?=$AmountDue?></td>
		<td><?=$status?></td>
	</tr>

<?	}
	mssql_close();
?>
</table>
<table style="width:1024px">
	<tr>
		<td align="center">
			<div class="navigation_bar">
			<form method="get" action="?mode=l">
			<? if($page_no > 1) { ?>
			<a href="?mode=<?=$mode?>&vendor_cd=<?=$pd_vendor_cd?>&target_date=<?=$target_date?>&target_date2=<?=$target_date2?>&page_no=<? echo ($page_no - 1);?>" class="arrow">&lt</a>
			<? } ?>
				<label for="page_no"><font size="2"><b>Move page</b></font></label>
				<input type="hidden" id="mode" name="mode" value="l" />
				<input type="hidden" id="vendor_cd" name="vendor_cd" value="<?=$venCode?>" />
				<input type="hidden" id="target_date" name="target_date" value="<?=$target_date?>" />
				<input type="hidden" id="target_date2" name="target_date2" value="<?=$target_date2?>" />
				<input type="text" id="page_no" name="page_no" size="5" value="<? echo ($page_no);?>" /><font size="2"><b>&nbsp;Total: <?=$last_page?>&nbsp;</b></font>
				<input type="submit" class="arrow movebtn" value="Move" />
			<? if($page_no < $last_page) { ?>
			<a href="?mode=<?=$mode?>&vendor_cd=<?=$venCode?>&target_date=<?=$target_date?>&target_date2=<?=$target_date2?>&page_no=<? echo ($page_no + 1);?>" class="arrow">&gt</a>
			<? } ?>
			</form>
		</td>
	</tr>
</table>

<div id="search_vendor_display" style="border:1px #666666 solid; background-color:#F6CECE; position:absolute; z-index:10; display:none; width:240px; left:97px; top:129px;">
</div>

</body>
</html>