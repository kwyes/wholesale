<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "login_check.php";
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$sDeleteYN = $_SESSION['staffproductYN'];

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$wsCode = ($_GET['wsCode']) ? $_GET['wsCode'] : $_POST['wsCode'];
	$ProdOwnCode = ($_GET['ProdOwnCode']) ? $_GET['ProdOwnCode'] : $_POST['ProdOwnCode'];
	$searchday = ($_GET['searchday']) ? $_GET['searchday'] : $_POST['searchday'];
	$currentTab = ($_GET['currentTab']) ? $_GET['currentTab'] : $_POST['currentTab'];
	$new = ($_GET['new']) ? $_GET['new'] : $_POST['new'];

	$today = date("Y-m-d");
	if(!$searchday)		$searchday = $today;

	$stype = 0;

	if($new == "") {

		$Onewhere = "WHERE wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' AND CID = '".$cId."' ";
		$Query = "SELECT wsCode,ProdOwnCode,SuppCode,prodId,prodName,prodKname,IsNULL(prodType,0) AS prodType,IsNULL(prodType2,0) AS prodType2,".
					"prodSupp,prodIUprice,prodOUprice,prodBalance,prodsize,prodTax,prodUnit,prodDeposit,prodcontenteach,useYN,".
					"prodNetWeight,prodWeight,prodCBM,prodDuty,prodShelfLife,prodBoxSize,".
					"CustomList1,CustomList2,CustomList3,CustomField1,CustomField2,CustomField3,prodCurrency,Description ".
				 "FROM Inventory_Item ".$Onewhere;
		$rst2 = mssql_query($Query);
		$row2 = mssql_fetch_array($rst2);
//echo $Query;
		if($row2['wsCode'] != "") {
			$wsCode = $row2['wsCode'];
			$ProdOwnCode = $row2['ProdOwnCode'];
			$SuppCode = $row2['SuppCode'];
			$prodId = $row2['prodId'];
			$prodName = $row2['prodName'];
//			$prodName = str_replace("\'", "'", $prodName);
			$prodKname = Br_iconv($row2['prodKname']);
			$prodSupp = $row2['prodSupp'];
			
			if($row2['prodType']>'0')	{	$stype = $row2['prodType'];	}
			else						{ 	$stype = 0;	}
			if($row2['prodType2']>'0')	{	$stype2 = $row2['prodType2']; }
			else						{	$stype2 = 0; }

			$prodDuty = $row2['prodDuty'];
			$prodIUprice = $row2['prodIUprice'];
			$prodOUprice = number_format($row2['prodOUprice'],2);
			$prodBalance = $row2['prodBalance'];
			$prodsize = Br_iconv($row2['prodsize']);
			$prodTax = $row2['prodTax'];
			$prodUnit = $row2['prodUnit'];
			$prodNetWeight = number_format($row2['prodNetWeight'],2);
			$prodWeight = number_format($row2['prodWeight'],2);
			$prodCBM = number_format($row2['prodCBM'],4);
			$prodShelfLife = $row2['prodShelfLife'];
			$prodBoxSize = $row2['prodBoxSize'];
			$prodcontenteach = $row2['prodcontenteach'];
			$prodDeposit = $row2['prodDeposit'];

			$CustomList1 = $row2['CustomList1'];
			$CustomList2 = $row2['CustomList2'];
			$CustomList3 = $row2['CustomList3'];
			$CustomField1 = $row2['CustomField1'];
			$CustomField2 = $row2['CustomField3'];
			$CustomField3 = $row2['CustomField2'];
			$Currency = $row2['Currency'];
			$Description = $row2['Description'];
			
			$LastModPerson = $row2['LastModPerson'];
			$LastModDate = $row2['LastModDate'];
			$LastModTime = $row2['LastModTime'];
			$useYN = $row2['useYN'];
		}
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="css/tab_style.css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script language="JavaScript" src="js/date_picker.js"></script>
<style>
#css_tabs {
    font-family:'nanumgothic', '나눔고딕', 'malgun gothic', '맑은 고딕', 'dotum', '돋움', sans-serif
}
/* 탭 선택 시 표시할 요소(div) 정의(1번 탭 선택 시 첫 번째 div 요소 표시) */
#css_tabs > input:nth-of-type(1), #css_tabs > input:nth-of-type(1) ~ div:nth-of-type(2), #css_tabs > input:nth-of-type(2), #css_tabs > input:nth-of-type(2) ~ div:nth-of-type(3), #css_tabs > input:nth-of-type(3), #css_tabs > input:nth-of-type(3) ~ div:nth-of-type(4) {
    display:none
}
#css_tabs > input:nth-of-type(1):checked ~ div:nth-of-type(2), #css_tabs > input:nth-of-type(2):checked ~ div:nth-of-type(3), #css_tabs > input:nth-of-type(3):checked ~ div:nth-of-type(4) {
    display:block
}
/* 라벨 기본 스타일 지정 */
#css_tabs > label {
    display:inline-block;
    font-variant:small-caps;
    font-size:.9em;
    padding:5px;
    text-align:center;
    width:100px;
    line-height:1.6em;
    font-weight:700;
    border-radius:3px 3px 0 0;
    background:#eee;
    color:#777;
    border:1px solid #ccc;
    border-width:1px 1px 0
}
#css_tabs > label:hover {
    cursor:pointer
}

/* 선택된 라벨, 커서를 올린 라벨 스타일 지정 */
#css_tabs > input:nth-of-type(1):checked ~ label:nth-of-type(1), #css_tabs > input:nth-of-type(2):checked ~ label:nth-of-type(2), #css_tabs > input:nth-of-type(3):checked ~ label:nth-of-type(3), #css_tabs > label:hover {
    background:#0066cc;
    color:#fff
}

#css_tabs > label:hover {
    background:#77ddff;
    color:#fff
}

/* 실제 내용이 담긴 div 요소 스타일 지정 */
#css_tabs .tab1_content, #css_tabs .tab2_content, #css_tabs .tab3_content {
    border:1px solid #ddd;
}
</style>
<script>

function item_cls()	{
	document.getElementById('wsCode').value = '';
	document.getElementById('ProdOwnCode').value = '';
	document.getElementById('useYN').value = '';
	document.getElementById('prodKname').value = '';
	document.getElementById('prodName').value = '';

	document.getElementById('SuppCode').value = '';
	document.getElementById('prodId').value = '';
	document.getElementById('prodsize').value = '';
	document.getElementById('prodUnit').value = '';
	document.getElementById('stype').value = '';
	document.getElementById('stype2').value = '';
	document.getElementById('prodTax').value = '';
	document.getElementById('prodcontenteach').value = '';
	document.getElementById('prodDeposit').value = '';
	document.getElementById('prodSupp').value = '';

	document.getElementById('prodNetWeight').value = '';
	document.getElementById('prodCBM').value = '';
	document.getElementById('prodBoxSize').value = '';
	document.getElementById('prodDuty').value = '';
	document.getElementById('prodShelfLife').value = '';
	document.getElementById('prodCurrency').value = '';
	document.getElementById('CustomList1').value = '';
	document.getElementById('CustomList2').value = '';
	document.getElementById('CustomList3').value = '';
	document.getElementById('CustomField1').value = '';
	document.getElementById('CustomField2').value = '';
	document.getElementById('CustomField3').value = '';
	document.getElementById('Description').value = '';
	document.getElementById('prodCurrency').value = '';

	document.location.href='inventoryitem.php?new=yes';
}

function product_append() {
	wcode = document.getElementById("wsCode").value;
	prodId = document.getElementById("prodId").value;
	if(wcode == "") {
		alert("Wholesale CODE를 입력하세요.");
		document.frm.wsCode.focus();
		return false;
	}
	if(prodId == "") {
		alert("Barcode를 입력하세요.");
		document.frm.prodId.focus();
		return false;
	}

	var target = document.forms.frm;
	var answer = confirm("아이템을 추가 하시겠습니까?");
	if(answer) {
		target.mode.value = "append";
		target.submit();
	}
}

function product_update() {
	wcode = document.getElementById("wsCode").value;
	prodId = document.getElementById("prodId").value;
	if(wcode == "") {
		alert("Wholesale CODE를 입력하세요.");
		document.frm.wsCode.focus();
		return false;
	}
//	if(prodId == "") {
//		alert("Barcode를 입력 하세요.");
//		document.frm.prodId.focus();
//		return false;
//	}

	var target = document.forms.frm;
	var answer = confirm("아이템을 수정 하시겠습니까?");
	if(answer) {
		target.mode.value = "update";
		target.submit();
	}
}

function product_delete(deleteYN) {
	wcode = document.getElementById("wsCode").value;
	prodId = document.getElementById("prodId").value;
	if(wcode == "") {
		alert("Wholesale CODE를 입력하세요.");
		document.frm.wsCode.focus();
		return false;
	}
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
			target.submit();
		}
	} else {
		alert("삭제할 권한이 없습니다.");
	}
}

function search_code(cId) {
	var search_key = document.getElementById("wsCode").value;
	if(search_key) {
		document.getElementById("code_iframe").src = "inventoryitem_search.php?mode=code&cId="+cId+"&key="+search_key;
		var pos = document.getElementById("wsCode").getBoundingClientRect();
		document.getElementById("search_code_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_code_display").style.top = pos.top + 20 + "px";
	} else {
		document.getElementById("wsCode").value = "";
		alert("검색할 Item Number 를 입력하세요.");
	}
}

function select_code(wsCode,ProdOwnCode)
{
	var div = document.getElementById("search_code_display");
	document.getElementById("wsCode").value = wsCode;
	document.getElementById("ProdOwnCode").value = ProdOwnCode;

	div.style.display = "none";
	document.location.href='inventoryitem.php?wsCode='+wsCode+'&ProdOwnCode='+ProdOwnCode;
}

function showhide_code()	{
	var div = document.getElementById("search_code_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}

function search_name(cId) {
	var search_key = document.getElementById("prodKname").value;
	if(search_key) {
		document.getElementById("name_iframe").src = "inventoryitem_search.php?mode=name&cId="+cId+"&key="+search_key;
		var pos = document.getElementById("prodKname").getBoundingClientRect();
		document.getElementById("search_name_display").style.left = pos.left - 00 + "px";
		document.getElementById("search_name_display").style.top = pos.top + 20 + "px";
	} else {
		document.getElementById("prodKname").value = "";
		alert("검색할 Card Name을 입력하세요.");
	}
}

function select_name(wsCode,ProdOwnCode)
{
	var div = document.getElementById("search_name_display");
	document.getElementById("wsCode").value = wsCode;
	document.getElementById("ProdOwnCode").value = ProdOwnCode;

	div.style.display = "none";
	document.location.href='inventoryitem.php?wsCode='+wsCode+'&ProdOwnCode='+ProdOwnCode;
}

function showhide_name()	{
	var div = document.getElementById("search_name_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}
function ecoFee(){
	newWindow=window.open("prodEco.php", "prodDeposit", "");	//자식창 OPEN
}
function change(cId){
	obj = document.getElementById("stype").value; 
	if (obj > 0) {
		url = "product_type2.php?cid="+cId+"&stype="+obj;
		document.getElementById("main_frame").src = "product_type2.php?cid="+cId+"&stype="+obj;	//iFrame
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
function reload_year(searchday,selG){
	var wsCode;
	var ProdOwnCode;
	var sYY;
	var tab = "";
	wsCode = document.getElementById("wsCode").value;
	ProdOwnCode = document.getElementById("ProdOwnCode").value;

	if (document.getElementById('tab3').checked)
	{
		tab = "tab3";
	}

	if (selG == 'B')	{
		sYY = searchday.substring(0,4);
		sYY = parseInt(sYY) - 1;
		searchday = sYY.toString() + searchday.substring(4,10);
	}	else	{
		sYY = searchday.substring(0,4);
		sYY = parseInt(sYY) + 1;
		searchday = sYY.toString() + searchday.substring(4,10);
	}

	document.location.href='inventoryitem.php?wsCode='+wsCode+'&ProdOwnCode='+ProdOwnCode+'&searchday='+searchday+'&currentTab='+tab;

}

function changeImage(imgNo)
{
	var target = document.forms.frm;
	$("#currentImage").val(imgNo);
	target.mode.value = "change_image";
	target.submit();
}

function deleteImage(imgNo)
{
	if(confirm("이미지를 삭제하시겠습니까?"))
	{
		var target = document.forms.frm;
		$("#currentImage").val(imgNo);
		target.mode.value = "delete_image";
		target.submit();
	}
}

function uploadImage(imgNo)
{
	$("#currentImage").val(imgNo);
	$("#uploadDoc").hide(); 
	$("#uploadImg").show(); 
}

function uploadImageFile() 
{
	var target = document.forms.frm;
	if($("#imgfile").val() == "") {
		alert("파일을 먼저 선택해 주세요.");
		return;
	}
	target.mode.value = "upload";
	target.submit();
	return;
}

function downDocFile()
{
	if(confirm("문서를 다운로드 하시겠습니까?"))
	{
		var target = document.forms.frm;
		target.mode.value = "downdoc";
		target.submit();
	}
}

function deleteDocFile()
{
	if(confirm("문서를 삭제하시겠습니까?"))
	{
		var target = document.forms.frm;
		target.mode.value = "deletedoc";
		target.submit();
	}
}

function uploadDocBtn1()
{
	$("#uploadImg").hide(); 
	$("#uploadDoc").show(); 
}

function uploadDocFile() 
{
	var target = document.forms.frm;
	if($("#docfile").val() == "") {
		alert("파일을 먼저 선택해 주세요.");
		return;
	}
	target.mode.value = "uploaddoc";
	target.submit();
	return;
}

function docSelected(e)
{
	var target = document.forms.frm;
//alert(e.value);
	$("#currentDoc").val(e.value);
	$("#deleteDoc").prop("disabled",false);
	$("#downDoc").prop("disabled",false);
}

</script>
</head>
<body style="background-color:#bfdfff;">
<form name="frm" method="post" action="inventoryitem_update.php" style="margin-bottom:0;" enctype="multipart/form-data">
<input type="hidden" name="cId">
<input type="hidden" name="mode">
<div id="item_list_div" style="position:absolute; left:0px; top:0px; border:0px solid #3366cc; width:800px; height:600px; background-color:#ffffff;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<div id="css_tabs">
				<input id="tab1" type="radio" name="tab" value="Profile"/>
				<input id="tab2" type="radio" name="tab" value="ItemDetails"/>
				<input id="tab3" type="radio" name="tab" value="History"/>
				<label for="tab1">Profile</label>
				<label for="tab2">Item Details</label>
				<label for="tab3">History</label>
				<div>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#c0c0c0;">
						<tr height="30">
							<td width="270px" class="doc_field_r"></td>
							<td width="260px" class="doc_field_c"></td>
							<td width="130px" class="doc_field_l"></td>
							<td width="140px" class="doc_field_l"></td>
						</tr>
					</table>
				</div>
				<div class="tab1_content">
					<div id="Profile_display" style="height:80%; overflow-y:none; overflow-x:hidden"></div>
					<div>
					<p>
						<table height="300">
							<tr>
								<td width="106px" class="doc_field_r"><b>Item Number:</b></td>
								<td width="420px" colspan="2"><input class="doc_field_200" id="wsCode" name="wsCode" type="text" value="<?=$wsCode?>" onkeypress="if (event.keyCode==13){ search_code('<?=$cId?>'); event.returnValue=false; }"/>&nbsp;<input style="background-color: #e2e2e2; width:60px" id="ProdOwnCode" name="ProdOwnCode" type="text" value="<?=$ProdOwnCode?>" readonly/></td>
								<td width="120px"></td>
								<td width="100px" class="doc_field"><input id="useYN" name="useYN" type="checkbox" value="N" <?if($useYN == "N") { echo 'checked'; }?>/>Inactive Item</td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Name:</b></td>
								<td colspan="4"><input class="doc_field_270" id="prodKname" name="prodKname" type="text" value="<?=$prodKname?>" onkeypress="if (event.keyCode==13){ search_name('<?=$cId?>'); event.returnValue=false; }"/></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>English Name:</b></td>
								<td colspan="2"><input class="doc_field_270" id="prodName" name="prodName" type="text" value="<?=$prodName?>"/></td>
								<td width="120px" class="doc_field_r">Quantity On Hand:</td>
								<td width="100px" class="doc_field_r"><span class="doc_field_r" id="prodBalance"></span><?=$prodBalance?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Supplier Code:</b></td>
								<td colspan="2"><input class="doc_field_100" id="SuppCode" name="SuppCode" type="text" value="<?=$SuppCode?>"/></td>
								<td width="120px" class="doc_field_r">Current Value:</td>
								<td width="100px" class="doc_field_r"><?=$CurrentValue?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Barcode:</b></td>
								<td colspan="2"><input class="doc_field_100" id="prodId" name="prodId" type="text" value="<?=$prodId?>"/></td>
								<td width="120px" class="doc_field_r">Average Cost:</td>
								<td width="100px" class="doc_field_r"><?=$AverageCost?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Size:</b></td>
								<td colspan="2"><input class="doc_field_100" id="prodsize" name="prodsize" type="text" value="<?=$prodsize?>"/></td>
								<td width="120px" class="doc_field_r">Committed:</td>
								<td width="100px" class="doc_field_r"><?=$Committed?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Unit:</b></td>
								<td colspan="2" class="doc_field_100">
									<select id="prodUnit" name="prodUnit" style="width:84px;">
									  <option value="BOX" <? if($prodUnit == 'BOX')	{ echo 'Selected'; } ?>>BOX</option> 
									  <option value="CTN" <? if($prodUnit == 'CTN') { echo 'Selected'; } ?>>CTN</option> 
									  <option value="CASE" <? if($prodUnit == 'CASE') { echo 'Selected'; } ?>>CASE</option>
									  <option value="EA" <? if($prodUnit == 'EA') { echo 'Selected'; } ?>>EA</option>
									  <option value="PK" <? if($prodUnit == 'PK') { echo 'Selected'; } ?>>PK</option>
									  <option value="LB" <? if($prodUnit == 'LB') { echo 'Selected'; } ?>>LB</option>
									  <option value="KG" <? if($prodUnit == 'KG') { echo 'Selected'; } ?>>KG</option>
									  <option value="OZ" <? if($prodUnit == 'OZ') { echo 'Selected'; } ?>>OZ</option>
									</select>		
								</td>
								<td width="120px" class="doc_field_r">On Order:</td>
								<td width="100px" class="doc_field_r"><?=$OnOrder?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Type I:</b></td>
								<td colspan="2">
						<?			$Query_type = "SELECT * FROM mfProd_type WHERE CID='".$cId."' ";
									$rst3 = mssql_query($Query_type);
						?>
									<select id="stype" name="stype" style="width:115px;" onclick="change('<?=$cId?>')">
									  <option value="">선택</option>
						<?			while($row3 = mssql_fetch_array($rst3))	{?>
									  <option value="<?=$row3['c_code']?>" <? if($stype == $row3['c_code']) { echo "selected"; }?>><?=Br_iconv($row3['kname'])?></option>
						<?			}?>
									</select>		
								</td>
								<td width="120px" class="doc_field_r">Available:</td>
								<td width="100px" class="doc_field"><?=$Available?></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Type II:</b></td>
								<td class="doc_field_80">
						<?
									$Query_type = "SELECT * FROM mfProd_type2 WHERE CID='".$cId."' AND c_code=".$stype;
									$rst4 = mssql_query($Query_type);
						?>
									<select id="stype2" name="stype2" style="width:115px;">
									  <option value="">선택</option>
						<?			while($row4 = mssql_fetch_array($rst4)) { ?>
									  <option value="<?=$row4['s_code']?>" <? if($stype2 == $row4['s_code']) { echo "selected"; }?>><?=Br_iconv($row4['kname'])?></option> 
						<?			}	?>
									</select>		
								</td>
								<td width="120px" class="doc_field_r"></td>
								<td width="100px" class="doc_field"></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Tax:</b></td>
								<td>
									<select id="prodTax" name="prodTax">
									  <option value="N" <? if($prodTax == 'N') { echo 'Selected'; } ?>>NONE</option> 
									  <option value="G" <? if($prodTax == 'G') { echo 'Selected'; } ?>>GST</option> 
									  <option value="B" <? if($prodTax == 'B') { echo 'Selected'; } ?>>GST/PST</option>
									</select>		
								</td>
								<td width="120px" class="doc_field_r"></td>
								<td width="100px" class="doc_field"></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Item Content:</b></td>
								<td colspan="2"><input class="doc_field_100" id="prodcontenteach" name="prodcontenteach" type="text" value="<?=$prodcontenteach?>"/></td>
								<td width="120px" class="doc_field_r"></td>
								<td width="100px" class="doc_field"></td>
							</tr>
							<tr>
								<td align="right"><input class="doc_field_r" type="button" value="DEPOSIT" onClick="ecoFee()"></td>
								<td><input class="doc_field_100" id="prodDeposit" name="prodDeposit" type="text" value="<?=$prodDeposit?>" /></td>


								<td width="120px" class="doc_field_r"></td>
								<td width="100px" class="doc_field"></td>
							</tr>
							<tr>
								<td class="doc_field_r"><b>Product Supplier:</b></td>
								<td colspan="2"><input class="doc_field_270" id="prodSupp" name="prodSupp" type="text" value="<?=$prodSupp?>"/></td>
								<td width="120px" class="doc_field_r"></td>
								<td width="100px" class="doc_field"></td>
							</tr>
							<tr height="30">
								<td></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="tab2_content">
					<div id="ItemDetails_display" style="height:80%; overflow-y:none; overflow-x:hidden"></div>
					<div>
						<table height="300">
							<tr>
								<td>
									<table>
										<tr>
											<td width="150px" class="doc_field_r"><b>Net Weight:</b></td>
											<td><input class="doc_field_100" id="prodNetWeight" name="prodNetWeight" type="text" value="<?=$prodNetWeight?>"/></td>
											<td width="120px" class="doc_field_r" style="padding-left:15px; padding-top:3px; vertical-align:top;"><b>Memo: </b></td>
											<td width="300px" rowspan="3">
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td><textarea style="background-color:#e2e2e2; height:63px; width:257px; overflow:auto;" id="Description" name="Description" type="text"><?=$Description?></textarea></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>Gross Weight:</b></td>
											<td colspan="3"><input class="doc_field_100" id="prodWeight" name="prodWeight" type="text" value="<?=$prodWeight?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>Item CBM:</b></td>
											<td colspan="3"><input class="doc_field_100" id="prodCBM" name="prodCBM" type="text" value="<?=$prodCBM?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>Box Size:</b></td>
											<td colspan="3"><input class="doc_field_100" id="prodBoxSize" name="prodBoxSize" type="text" value="<?=$prodBoxSize?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>Duty:</b></td>
											<td colspan="3"><input class="doc_field_100" id="prodDuty" name="prodDuty" type="text" value="<?=$prodDuty?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>Shelf Life:</b></td>
											<td><input class="doc_field_100" id="prodShelfLife" name="prodShelfLife" type="text" value="<?=$prodShelfLife?>"/></td>
											<td class="doc_field_r"><b>Currency:</b></td>
											<td>
												<select id="prodCurrency" name="prodCurrency">
												  <option value="CAD" <? if($prodTax == 'CAD') { echo 'Selected'; } ?>>CAD</option> 
												  <option value="USD" <? if($prodTax == 'USD') { echo 'Selected'; } ?>>USD</option>
												</select>		
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td class="doc_field_r" width="150px"><b>CustomList #1:</b></td>
											<td><input class="doc_field_s" id="CustomList1" name="CustomList1" type="text" value="<?=$CustomList1?>"/></td>
											<td width="90px"></td>
											<td class="doc_field_r"><b>CustomField #1: </b></td>
											<td><input class="doc_field_s" id="CustomField1" name="CustomField1" type="text" value="<?=$CustomField1?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>CustomList #2:</b></td>
											<td><input class="doc_field_s" id="CustomList2" name="CustomList2" type="text" value="<?=$CustomList2?>"/></td>
											<td></td>
											<td class="doc_field_r"><b>CustomField #2: </b></td>
											<td><input class="doc_field_s" id="CustomField2" name="CustomField2" type="text" value="<?=$CustomField2?>"/></td>
										</tr>
										<tr>
											<td class="doc_field_r"><b>CustomList #3:</b></td>
											<td><input class="doc_field_s" id="CustomList3" name="CustomList3" type="text" value="<?=$CustomList3?>"/></td>
											<td></td>
											<td class="doc_field_r"><b>CustomField #3: </b></td>
											<td><input class="doc_field_s" id="CustomField3" name="CustomField3" type="text" value="<?=$CustomField3?>"/></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center">

									<!-- imange and file upload -->
									<table>
									<tr>
										<td>
<?
	// 이미지를 가져옴.
	$Query_type = "SELECT image1,image2,image3 FROM Inventory_Item WHERE CID='".$cId."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
	$rst = mssql_query($Query_type);
	$row = mssql_fetch_array($rst);
	$image1Src = "";
	if(trim($row['image1']) != "") $image1Src = "upload/ItemImages/".$row['image1'];
	$image2Src = "";
	if(trim($row['image2']) != "") $image2Src = "upload/ItemImages/".$row['image2'];
	$image3Src = "";
	if(trim($row['image3']) != "") $image3Src = "upload/ItemImages/".$row['image3'];
?>
											<!-- first imange -->
											<input type="hidden" id="currentImage" name="currentImage" value="">
											<table>
											<tr>
												<td class="doc_field_c"> 
<? 	
	if($image1Src != ""){
		$size = getimagesize($image1Src);
		if($size[0] > $size[1])
		{
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image1Src?>"><img src="<?=$image1Src?>" width="150"></a></div> 
<?
		} else {
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image1Src?>"><img src="<?=$image1Src?>" height="150"></a></div> 
<?
		}
?>
<? } else { ?> 
													<img src="<?=$image1Src?>" height="150" width="150" style="border:1px solid blue"> 
<? } ?> 
												</td>
											</tr>
											<tr>
												<td class="doc_field_c"> 
<? 	if($image1Src != ""){?>
													<input type="button" value="Delete" onClick="javascript:deleteImage(1)">
<? } else { ?> 
													<input type="button" value="Image Upload" onClick="javascript:uploadImage(1)">
<? } ?> 
												</td>
											</tr>
											</table>
										</td>
										<td>
											<!-- second imange -->
											<table>
											<tr>
											<td align="cneter"> 
<? 	
	if($image2Src != ""){
		$size = getimagesize($image2Src);
		if($size[0] > $size[1])
		{
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image2Src?>"><img src="<?=$image2Src?>" width="150"></a></div> 
<?
		} else {
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image2Src?>"><img src="<?=$image2Src?>" height="150"></a></div> 
<?
		}
?>
<? } else { ?> 
													<img src="<?=$image2Src?>" height="150" width="150" style="border:1px solid blue"> 
<? } ?>
											</td>
											</tr>
											<tr>
												<td class="doc_field_c"> 
<? 	if($image2Src != ""){?>
													<input type="button" value="Delete" onClick="javascript:deleteImage(2)">
<? } else { ?> 
													<input type="button" value="Image Upload" onClick="javascript:uploadImage(2)">
<? } ?> 
												</td>
											</tr>
											</table>
										</td>
										<td>
											<!-- third imange -->
											<table>
											<tr>
												<td align="center"> 
<? 	
	if($image3Src != ""){
		$size = getimagesize($image3Src);
		if($size[0] > $size[1])
		{
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image3Src?>"><img src="<?=$image3Src?>" width="150"></a></div> 
<?
		} else {
?>
													<div style="border:1px solid blue; width:150px; height:150px"><a href="<?=$image3Src?>"><img src="<?=$image3Src?>" height="150"></a></div> 
<?
		}
?>
<? } else { ?> 
													<img src="<?=$image3Src?>" height="150" width="150" style="border:1px solid blue"> 
<? } ?> 
												</td>
											</tr>
											<tr>
												<td class="doc_field_c"> 
<? 	if($image3Src != ""){?>
													<input type="button" value="Delete" onClick="javascript:deleteImage(3)">
<? } else { ?> 
													<input type="button" value="Image Upload" onClick="javascript:uploadImage(3)">
<? } ?> 
												</td>
											</tr>
											</table>
										</td>
										<td>
											<!-- docs -->
											<table>
											<tr>
												<td align="center">
													<input type="hidden" id="currentDoc" name="currentDoc" value="">
													<select id="selectdoc" name="selectdoc" size="10" style="width:200px; height:150px" onchange="docSelected(this)">
														<optgroup label="Uploaded Docs">
<?
	$len = strlen($wsCode."_".$ProdOwnCode."_Doc_");

	foreach(glob("./upload/ItemDocs/*.*") as $filename)
	{
		if(strpos($filename,$wsCode."_".$ProdOwnCode."_Doc_"))
		{
			$filename = substr($filename, strlen("./upload/ItemDocs/"));
			$ofname = substr($filename,$len);
			if(strpos($filename,$wsCode."_".$ProdOwnCode."_Doc_") >= 0)
				echo "<option value='$filename'>$ofname</option>";
		}
	}
?>

														</optgroup>
													</select>
												</td>
											</tr>
											<tr>
												<td align="center">
													<input type="button" id="uploadDocBtn" value="Upload" onClick="javascript:uploadDocBtn1()">
													<input type="button" id="deleteDoc" value="Delete" onClick="javascript:deleteDocFile()" disabled>
													<input type="button" id="downDoc" value="Download" onClick="javascript:downDocFile()" disabled>
												</td>
											</tr>
											</table>

										</td>
									</tr>
									<tr>
										<td class="doc_field_c" align="right" colspan="4">
										<div id="uploadImg" style="position:relative; left:130px; display:none"> 
											<table>
											<tr>
												<td class="doc_field_r"> Select Image : </td>
												<td class="doc_field_l"><input type="file" id="imgfile" name="uploadimg" accept="image/png, image/jpeg, image/gif" /> <input type="button" value="Upload" onClick="javascript:uploadImageFile()" />
												</td>
											</tr>
											</table>
										</div> 
										<div id="uploadDoc" style="position:relative; left:140px; display:none;">
											<table>
											<tr>
												<td class="doc_field_r"> Select File : </td>
												<td class="doc_field_l"><input type="file" id="docfile" name="uploaddocfile" /> <input type="button" value="Upload" onClick="javascript:uploadDocFile()" /></td>
											</tr>
											</table>
										</div>
										</td>
									</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="tab3_content">
					<div id="History_display" style="height:80%; overflow-y:none; overflow-x:hidden"></div>
					<div>
						<table height="400" width="100%">
							<tr align="center">
<?
	if($_SESSION['ActiveIP'] == 'N')
	{
?>
								<td height="400" width="100%" align="center" valign="middle">
									승인되지 않은 접근입니다.
								</td>
<?
	} else { //if($_SESSION['ActiveIP'] == 'N')
?>
								<td>
									<input style="font-size:9pt;height:25" type="button" value=" << 이전 " class="btn_style" onClick="reload_year('<?=$searchday?>','B');"/>
									<input style="font-size:9pt;height:25" type="button" value=" 이후 >> " class="btn_style" onClick="reload_year('<?=$searchday?>','A');"/>								
								</td>
							</tr>
							<tr align="center">
								<td>
									<table style="border-collapse:collapse; font-family:verdana; font-size:13px;">
										<tr align="center" style="background-color:#0066cc">
											<td width='60' style="border:1px solid #BBBBBB; border-right:0; color:#ffffff"><?=substr($searchday, 0 ,4)?></td>
											<td width='60' style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">Bought</td>
											<td width='80' style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">Purchases</td>
											<td width='70' style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">Sold</td>
											<td width='80' style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">Sales</td>
											<td width='100' style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">Cost of Sales</td>
										</tr>
<?
										for($i=1; $i<= 12; $i++) {
											if($i < 10) $MM = '0'.$i;
											else			$MM = $i;

											$strYYMM = "'".$YY."-".$MM."'";
											
											if($wsCode)	{
												$query = "SELECT sum(qty) as qty, sum(prodPriceCAD) as purchases , sum(prodPrice) as price ".
															 "FROM purchase_detail ".$Onewhere." ".
															 "AND substring(convert(varchar(10),pd_date,120),1,7) =".$strYYMM;
												$pur_result = mssql_query($query);
												$pur_row = mssql_fetch_array($pur_result);

												$strQty = $pur_row['qty'];
												$strPrice = $pur_row['price'];

												$query = "SELECT sum(tqty) as qty, sum(tOUprice) as price ".
															 "FROM trSales ".$Onewhere." ".
															 "AND substring(convert(varchar(10),tdate,120),1,7) =".$strYYMM;

												$sale_result = mssql_query($query);
												$sale_row = mssql_fetch_array($sale_result);

												$saleQty = $sale_row['qty'];
												$salePrice = $sale_row['price'];
											}

											if(!$strQty) $strQty = 0;
											if(!$strPrice) $strPrice = 0;
											if(!$saleQty) $saleQty = 0;
											if(!$salePrice) $salePrice = 0;

											$totalQty = $totalQty + $strQty;
											$totalPrice = $totalPrice + $strPrice;
											$totalsaleQty = $totalsaleQty + $saleQty;
											$totalsalePrice = $totalsalePrice + $salePrice;

											if ($i % 2 == 0)		$fieldName = "doc_field_purchases_bg";
											else						$fieldName = "doc_field_purchases";

?>
										<tr height="20" class="<?=$fieldName?>">
											<td align="center" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=$MM?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=$strQty?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=$strPrice?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=$saleQty?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=$salePrice?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:1; padding-right:5px;"></td>
										</tr>
<?
											$strQty =  $strPrice = $saleQty =  $salePrice = 0;
										}
?>
										<tr height="20" style="background-color:#0066cc">
											<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff; padding-right:5px;">TOTAL</td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff; padding-right:5px;"><?=$totalQty?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff; padding-right:5px;"><?=$totalPrice?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff; padding-right:5px;"><?=$totalsaleQty?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff; padding-right:5px;"><?=$totalsalePrice?></td>
											<td align="right" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff; padding-right:5px;"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center">
									<table>
										<tr>
											<td width="150px" class="doc_field_r"><b>Item Regular Price:</b></td>
											<td><input class="doc_field_100" id="prodOUprice" name="prodOUprice" type="text" value="<?=$prodOUprice?>"/></td>
										</tr>
									</table>
								</td>
<?
	} //if($_SESSION['ActiveIP'] == 'N')
?>
							</tr>
						</table>
					</div>
				</div>
				<div>
					<table width="100%" height="70px">
						<tr>
							<td align="center">
<?
	if($_SESSION['ActiveIP'] == 'Y')
	{
?>
								<input style="font-size:9pt;height:25" type="button" value=" Clear " class="btn_style" onClick="return item_cls();"/>
								<input style="font-size:9pt;height:25" type="button" value=" Add " class="btn_style" onClick="product_append()"/>
								<input style="font-size:9pt;height:25" type="button" value=" Save " class="btn_style" onClick="product_update()"/>
								<input style="font-size:9pt;height:25" type="button" value=" Delete " class="btn_style" onClick="product_delete('<?=$sDeleteYN?>')"/>
<?
	}
?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
	</tr>
</table>
</div>
</form>
		
<div id="search_code_display" style="border:1px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:525px; left:0px; top:0px;">
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
<script>
<? 	if($currentTab == "tab3") { ?>
			document.getElementById('tab3').checked=true;
<? 	} else if($currentTab == "tab2") { ?>
			document.getElementById('tab2').checked=true;
<?	}	else {	?>
			document.getElementById('tab1').checked=true;
<?	}	?>
</script>
</body>
</html>
