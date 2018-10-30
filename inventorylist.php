<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<?php
	session_start();
	include_once "login_check.php";
	include "includes/db_configms.php";
	include "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$sDeleteYN = $_SESSION['staffproductYN'];

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$page_no = intval(($_GET['page_no']) ? $_GET['page_no'] : "1");

	$wsCode = ($_GET['wsCode']) ? $_GET['wsCode'] : $_POST['wsCode'];
	$ProdOwnCode = ($_GET['ProdOwnCode']) ? $_GET['ProdOwnCode'] : $_POST['ProdOwnCode'];

	$searchCD = ($_GET['searchCD']) ? $_GET['searchCD'] : $_POST['searchCD'];
	$searchId = ($_GET['searchId']) ? $_GET['searchId'] : $_POST['searchId'];
	$searchName = ($_GET['searchName']) ?  $_GET['searchName'] :  $_POST['searchName'];

	$inactive = ($_GET['inactive']) ? $_GET['inactive'] : $_POST['inactive'];
	$checked = "";
	if($inactive == "yes") $checked = "checked";

	$today = date("Y-m-d");
	$chk_info = "1";

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<?php
	if($cId == '1')
	{
		$bgcolor = "#3366CC";
?>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<?
	} else {
		$bgcolor = "#E92142";
?>
<link rel="stylesheet" type="text/css" href="css/style2.css"/>
<?
	}
?>

<script language="JavaScript" src="js/date_picker.js">
// ㅇㄴㅇㄴㅁ
</script>
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
function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

function search_code(e)
{
	if (e.keyCode == 13) {
		wsCode = document.getElementById('wsCode').value;
		document.location.href='product.php?searchCD='+wsCode;
	}
}

function search_id(e)
{
	if (e.keyCode == 13) {
		prodId = document.getElementById('prodId').value;
		document.location.href='product.php?mode=2&searchId='+prodId;
	}
}
function search_name(e)
{
	if (e.keyCode == 13) {
		name = document.getElementById('prodKname').value;
		document.location.href='product.php?mode=2&searchName='+name;
	}
}
function change(cid){
	obj = document.getElementById("stype").value; 
	if (obj > 0) {
		url = "product_type2.php?cid="+cid+"&stype="+obj;
		document.getElementById("main_frame").src = "product_type2.php?cid="+cid+"&stype="+obj;	//iFrame
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
function crImeMode(obg) { 
	obj,value = obg.value.replace(/[\ㄱ-ㅎ가-힣]/g, ''); 
}
function showhide()	{
	var div = document.getElementById("search_supp_display");
	if (div.style.display !== "none") {
		div.style.display = "none";
	} else {
		div.style.display = "block";
	}
}

function select_supp(code) {
	var div = document.getElementById("search_supp_display");
	document.getElementById("prodSupp").value = code;
	div.style.display = "none";
}

// Global variables for inventory items
var items_all = [];				// for inventory items
var items_all_complete = false;
var items_all_count = 0;
var items_all_position = 0;		// for dynamic loading

var items_retail = [];				// for retail items
var items_retail_complete = false;
var items_retail_count = 0;
var items_retail_position = 0;

var items_mapping = [];		// for mapping items
var items_mapping_complete = false;
var items_mapping_count = 0;
var items_mapping_position = 0;

var items_search = [];			// for search items
var items_search_count = 0;
var items_search_position = 0;

var check_info = "";				// TB, SRY, BBY 선택 항목 저장

function make_item_table(type,count,position,items_array)
{
	var rtn = "";
	var display_div;

	switch(type)
	{
		case "2" : display_div = "retail_display"; break;
		case "3" : display_div = "mapping_display"; break;
		default : display_div = "inventory_display";
	}

	if(count == 0){
		var colspan = 0;
		if(type == "2") colspan = 11;
		else if(type == "3") colspan = 9;
		else colspan = 7;
		rtn += 	'<table width="100%">' +
						'<tr>' +
						'	<td align="center" colspan="' + colspan + '" style="color:#808080"><p><b>검색된 결과가 없습니다.</b></p></td>' +
						'</tr>' +
					'</table>';
		document.getElementById(display_div).innerHTML = rtn;
		return 0;
	}
	
	var tmp = "";
	var fieldName = "";
	var fields;
	var length = 0;

	if(position == -1) length = items_array.length;	// search ?
	else 
	{
		if(items_array.length > 50) length = 50;
		else length = items_array.length;
	}

	console.log(items_array.length);

	var j=0;
	if(type == "2")	// retail item
	{
		rtn = '<table id="table_retail" style="border-collapse:collapse; width:1000px; font-family:verdana; font-size:13px;"> ' +
				'<tr align="center" style="background-color:#0066cc"> ' +
					'<td width="90px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UPC</td> ' +
					'<td width="205px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">ITEM NAME</td> ' +
					'<td width="50px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">SIZE</td> ' +
					'<td width="30px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UNIT</td> ' +
//					'<td width="30px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">TYPE</td> ' +
					'<td width="70px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">IN PRICE</td> ' +
					'<td width="70px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">SALE PRICE</td> ' +
					'<td width="60px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">BALANCE</td> ' +
					'<td width="20px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">TAX</td> ' +
					'<td width="50px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">DEPOSIT</td> ' +
					'<td width="50px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">PROMOTION</td> ' +
					'<td width="40px" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">USE</td> ' +
				'</tr> ';
		//for(var i=0; i<items_array.length; i++){
		for(var i=0; i<length; i++){

			//if(type != "0" && items_array[i][1] != type) continue;
			j++;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else			fieldName = "doc_field_purchases";

			//fields = items_all[i].split(';');
			//$row['prodId'];$row['prodKname'];$row['prodsize'];$row['prodUnit'];$row['prodType'];$row['prodIUprice'];$row['prodOUprice'];$row['prodTotQty'];$row['prodTax'];$row['prodDeposit'];$strPromo;$row['useYN'];
			var balanceS = "";
			var price1S = "";
			var price2S = "";
			var balance = parseInt(items_array[i][7]);
			var price1 = items_array[i][5] == "" ? 0 : parseInt(items_array[i][5]);
			var price2 = items_array[i][6] == "" ? 0 : parseInt(items_array[i][6]);

			if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price1 != 0) price1S = price1.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price2 != 0) price2S = price2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			
			rtn +=  '<tr class="' + fieldName + '">' +
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">' + items_array[i][0]+'</td>' + 
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">' + items_array[i][1]+'</td>' + 
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">'+items_array[i][2]+'</td>' + 
					'<td align="center" style="border:1px solid #BBBBBB; border-right:0">'+items_array[i][3]+'</td>' + 
//					'<td align="right" style="border:1px solid #BBBBBB; border-right:0">'+items_array[i][4]+'</td>' + 
					'<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;">' +items_array[i][5]+'</td>' +
					'<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;">' +items_array[i][6]+'</td>' +
					'<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;">' +items_array[i][7]+'</td>' +
					'<td align="center" style="border:1px solid #BBBBBB; border-right:0">' +items_array[i][8]+'</td>' +
					'<td align="right" style="border:1px solid #BBBBBB; border-right:0">' +items_array[i][9]+'</td>' +
					'<td align="center" style="border:1px solid #BBBBBB; border-right:0">' +items_array[i][10]+'</td>' +
					'<td align="center" style="border:1px solid #BBBBBB; border-right:1">' +items_array[i][11]+'</td>' +
					'</tr>';
		}
	}
	else if(type == "3") // item mapping
	{
		rtn = '<table id="table_mapping" style="border-collapse:collapse; width:1000px"> ' +
				'<tr style="background-color:#0066cc" class="doc_field_purchases_bg" align="center"> ' + 
					'<td width="110px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UPC</font></td> ' +
					'<td width="40px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UNIT</font></td> ' +
					'<td width="245px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">ITEM NAME</font></td> ' +
					'<td width="80px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">SIZE</font></td> ' +
					'<td width="110px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UPPER UPC</font></td> ' +
					'<td width="40px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UNIT</font></td> ' +
					'<td width="245px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UPPER ITEM NAME</font></td> ' +
					'<td width="80px" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">SIZE</font></td> ' +
					'<td width="50px" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">CONTENT</font></td> ' +
				'</tr> ';
		//for(var i=0; i<items_array.length; i++){
		for(var i=0; i<length; i++){
			j++;
			//if(type != "0" && items_array[i][1] != type) continue;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else				fieldName = "doc_field_purchases";

			rtn +=  '<tr class="' + fieldName + '">' +
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">' + items_array[i][0] + '</td>' + 
						'<td align="center" style="border:1px solid #BBBBBB; border-right:0">' + items_array[i][1]+'</td>' + 
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">'+items_array[i][2]+'</td>' + 
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">'+items_array[i][3]+'</td>' + 
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">' + items_array[i][4] + '</td>' + 
						'<td align="center" style="border:1px solid #BBBBBB; border-right:0;">' +items_array[i][5]+'</td>' +
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">' +items_array[i][6]+'</td>' +
						'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px">' +items_array[i][7]+'</td>' +
						'<td align="center" style="border:1px solid #BBBBBB; border-right:1">' +items_array[i][8]+'</td>' +
					'</tr>';
		}
	}
	else // Inventory List
	{
		rtn = '<table id="table_inventory" width="865px" style="border-collapse:collapse; letter-spacing:-1px; font-family:verdana; font-size:13px;">' +
				'<tr style="background-color:#0066cc">' +
				'	<td width="110px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">ITEM CODE</td>' +
				'	<td width="35px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">O/C</td>' +
				'	<td width="110px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">UPC</td>' +
				'	<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">ITEM NAME</td>' +
				'	<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">SIZE</td>' +
				'	<td width="70px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">BALANCE</td>' +
				'	<td width="80px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">LAST COST</td>' +
				'	<td width="90px" align="center" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">SELL PRICE</td>' +
				'</tr>' ;
		//for(var i=0; i<items_array.length; i++){
		for(var i=0; i<length; i++){

			//if(type != "0" && items_array[i][1] != type) continue;
			j++;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else			fieldName = "doc_field_purchases";

			//fields = items_all[i].split(';');
			//$row['wsCode'].";".$row['ProdOwnCode'].";".$name.";".Br_iconv($row['prodsize']).";".$row['prodBalance'].";".$row['prodIUprice'].";".$row['prodIUprice'];
			var balanceS = "";
			var price1S = "";
			var price2S = "";
			var balance = parseInt(items_array[i][5]);
			var price1 = items_array[i][6] == "" ? 0 : parseInt(items_array[i][6]);
			var price2 = items_array[i][7] == "" ? 0 : parseInt(items_array[i][7]);
			var color = "black";
			var istatus = items_array[i][8];

			if(istatus == 'Y') color = "black";
			else color = "gray";

			if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price1 != 0) price1S = price1.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price2 != 0) price2S = price2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			rtn +=  '<tr class="' + fieldName + '">' +
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">' + 
						'<a href="inventoryitem.php?wsCode='+items_array[i][0]+'&ProdOwnCode='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][0]+'</a></td>' + 
					'<td align="center" style="border:1px solid #BBBBBB; border-right:0">' + 
						'<a href="inventoryitem.php?wsCode='+items_array[i][0]+'&ProdOwnCode='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][1]+'</a></td>' + 
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">'+
						'<a href="inventoryitem.php?wsCode='+items_array[i][0]+'&ProdOwnCode='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][2]+'</a></td>' + 				
					'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">'+
						'<a href="inventoryitem.php?wsCode='+items_array[i][0]+'&ProdOwnCode='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][3]+'</a></td>' + 				
					'<td align="left" style="color:'+color+'; border:1px solid #BBBBBB; border-right:0; padding-left:5px;">'+items_array[i][4]+'</td>' + 
					'<td align="right" style="color:'+color+'; border:1px solid #BBBBBB; border-right:0; padding-right:5px;">'+balanceS+'</td>' + 
					'<td align="right" style="color:'+color+'; border:1px solid #BBBBBB; border-right:0; padding:0 5px 0 0">' +price1S+'</td>' +
					'<td align="right" style="color:'+color+'; border:1px solid #BBBBBB; border-right:1; padding:0 5px 0 0">' +price2S+'</td>' +
					'</tr>';
		}
	}
	rtn += '</table>';

	document.getElementById(display_div).innerHTML = rtn;
	return length;
}

function add_item_table(element,count,position,items_array)
{

	var rtn = "";
	var tmp = "";
	var fieldName = "";
	var fields;
	var length = 0;

	if(items_array.length > position + 50) length = 50;
	else length = items_array.length - position;

	console.log(length+" element.id"+element.id);
	
	var j=0;
	if(element.id == "retail_display")	// retail item
	{
		var table = document.getElementById("table_retail");
		for(var i=position; i<length+position; i++)
		{
			j++;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else				fieldName = "doc_field_purchases";

			var row = table.insertRow(-1);
			row.className = fieldName;

			var cell0 = row.insertCell(0);
			var cell1 = row.insertCell(1);
			var cell2 = row.insertCell(2);
			var cell3 = row.insertCell(3);
			var cell4 = row.insertCell(4);
			var cell5 = row.insertCell(5);
			var cell6 = row.insertCell(6);
			var cell7 = row.insertCell(7);
			var cell8 = row.insertCell(8);
			var cell9 = row.insertCell(9);
			var cell10 = row.insertCell(10);

			var price1S = "";
			var price2S = "";

			var price1 = items_array[i][5] == "" ? 0 : parseInt(items_array[i][5]);
			var price2 = items_array[i][6] == "" ? 0 : parseInt(items_array[i][6]);

			if(price1 != 0) price1S = price1.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price2 != 0) price2S = price2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');

			cell0.style.textAlign = "left";
			cell0.style.border = "1px solid #BBBBBB";
			cell0.style.paddingLeft = "5px";
			cell0.style.borderRight = "0";
			cell0.innerHTML = items_array[i][0];

			cell1.style.textAlign = "left";
			cell1.style.border = "1px solid #BBBBBB";
			cell1.style.borderRight = "0";
			cell1.style.paddingLeft = "5px";
			cell1.innerHTML = items_array[i][1];

			cell2.style.textAlign = "left";
			cell2.style.border = "1px solid #BBBBBB";
			cell2.style.borderRight = "0";
			cell2.style.paddingLeft = "5px";
			cell2.innerHTML = items_array[i][2];

			cell3.style.textAlign = "center";
			cell3.style.border = "1px solid #BBBBBB";
			cell3.style.borderRight = "0";
			cell3.innerHTML = items_array[i][3];

			cell4.style.textAlign = "right";
			cell4.style.border = "1px solid #BBBBBB";
			cell4.style.borderRight = "0";
			cell4.innerHTML = price1S;

			cell5.style.textAlign = "right";
			cell5.style.border = "1px solid #BBBBBB";
			cell5.style.borderRight = "0";
			cell5.style.paddingRight = "5px";
			cell5.innerHTML = price2S;

			cell6.style.textAlign = "right";
			cell6.style.border = "1px solid #BBBBBB";
			cell6.style.borderRight = "0";
			cell6.style.paddingRight = "5px";
			cell6.innerHTML = items_array[i][7];

			cell7.style.textAlign = "center";
			cell7.style.border = "1px solid #BBBBBB";
			cell7.style.borderRight = "0";
			cell7.innerHTML = items_array[i][8];

			cell8.style.textAlign = "right";
			cell8.style.border = "1px solid #BBBBBB";
			cell8.style.borderRight = "0";
			cell8.innerHTML = items_array[i][9];

			cell9.style.textAlign = "center";
			cell9.style.border = "1px solid #BBBBBB";
			cell9.style.borderRight = "0";
			cell9.innerHTML = items_array[i][10];

			cell10.style.textAlign = "center";
			cell10.style.border = "1px solid #BBBBBB";
			cell10.style.borderRight = "1";
			cell10.innerHTML = items_array[i][11];
		}
	}
	else if(element.id == "mapping_display") // item mapping
	{
		var table = document.getElementById("table_mapping");
		for(var i=position; i<length+position; i++){

			//if(type != "0" && items_array[i][1] != type) continue;
			j++;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else				fieldName = "doc_field_purchases";

			var row = table.insertRow(-1);
			row.className = fieldName;

			var cell0 = row.insertCell(0);
			var cell1 = row.insertCell(1);
			var cell2 = row.insertCell(2);
			var cell3 = row.insertCell(3);
			var cell4 = row.insertCell(4);
			var cell5 = row.insertCell(5);
			var cell6 = row.insertCell(6);
			var cell7 = row.insertCell(7);
			var cell8 = row.insertCell(8);

			cell0.style.textAlign = "left";
			cell0.style.border = "1px solid #BBBBBB";
			cell0.style.borderRight = "0";
			cell0.style.paddingLeft = "5px";
			cell0.innerHTML = items_array[i][0];

			cell1.style.textAlign = "center";
			cell1.style.border = "1px solid #BBBBBB";
			cell1.style.borderRight = "0";
			cell1.innerHTML = items_array[i][1];

			cell2.style.textAlign = "left";
			cell2.style.border = "1px solid #BBBBBB";
			cell2.style.borderRight = "0";
			cell2.style.paddingLeft = "5px";
			cell2.innerHTML = items_array[i][2];

			cell3.style.textAlign = "left";
			cell3.style.border = "1px solid #BBBBBB";
			cell3.style.borderRight = "0";
			cell3.style.paddingLeft = "5px";
			cell3.innerHTML = items_array[i][3];

			cell4.style.textAlign = "left";
			cell4.style.border = "1px solid #BBBBBB";
			cell4.style.borderRight = "0";
			cell4.style.paddingLeft = "5px";
			cell4.innerHTML = items_array[i][4];

			cell5.style.textAlign = "center";
			cell5.style.border = "1px solid #BBBBBB";
			cell5.style.borderRight = "0";
			cell5.innerHTML = items_array[i][5];

			cell6.style.textAlign = "left";
			cell6.style.border = "1px solid #BBBBBB";
			cell6.style.borderRight = "0";
			cell6.style.paddingLeft = "5px";
			cell6.innerHTML = items_array[i][6];

			cell7.style.textAlign = "left";
			cell7.style.border = "1px solid #BBBBBB";
			cell7.style.borderRight = "0";
			cell7.style.paddingLeft = "5px";
			cell7.innerHTML = items_array[i][7];

			cell8.style.textAlign = "center";
			cell8.style.border = "1px solid #BBBBBB";
			cell8.style.borderRight = "1";
			cell8.innerHTML = items_array[i][8];
		}
	}
	else // Inventory List
	{
		var table = document.getElementById("table_inventory");
		for(var i=position; i<length+position; i++){

			j++;
			//if(type != "0" && items_array[i][1] != type) continue;
			if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
			else				fieldName = "doc_field_purchases";

			var istatus = items_array[i][8];
			if(istatus == 'Y') color = "black";
			else color = "gray";

			var row = table.insertRow(-1);
			row.className = fieldName;

			var cell0 = row.insertCell(0);
			var cell1 = row.insertCell(1);
			var cell2 = row.insertCell(2);
			var cell3 = row.insertCell(3);
			var cell4 = row.insertCell(4);
			var cell5 = row.insertCell(5);
			var cell6 = row.insertCell(6);
			var cell7 = row.insertCell(7);

			//fields = items_all[i].split(';');
			//$row['wsCode'].";".$row['ProdOwnCode'].";".$name.";".Br_iconv($row['prodsize']).";".$row['prodBalance'].";".$row['prodIUprice'].";".$row['prodIUprice'];
			var balanceS = "";
			var price1S = "";
			var price2S = "";
			var balance = parseInt(items_array[i][5]);
			var price1 = items_array[i][6] == "" ? 0 : parseInt(items_array[i][6]);
			var price2 = items_array[i][7] == "" ? 0 : parseInt(items_array[i][7]);

			if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price1 != 0) price1S = price1.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
			if(price2 != 0) price2S = price2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');

			cell0.style.textAlign = "left";
			cell0.style.border = "1px solid #BBBBBB";
			cell0.style.borderRight = "0";
			cell0.style.paddingLeft = "5px";
			cell0.innerHTML = '<a href="inventoryitem.php?wsCode='+items_array[i][0]+'&ProdOwnCode='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][0]+'</a></td>';

			cell1.style.textAlign = "center";
			cell1.style.border = "1px solid #BBBBBB";
			cell1.style.borderRight = "0";
			cell1.innerHTML = '<a href="inventoryitem.php?cardId='+items_array[i][0]+'&CardType='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][1]+'</a></td>';

			cell2.style.textAlign = "left";
			cell2.style.border = "1px solid #BBBBBB";
			cell2.style.borderRight = "0";
			cell2.style.paddingLeft = "5px";
			cell2.innerHTML = '<a href="inventoryitem.php?cardId='+items_array[i][0]+'&CardType='+items_array[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_array[i][2]+'</a></td>';

			cell3.style.textAlign = "left";
			cell3.style.border = "1px solid #BBBBBB";
			cell3.style.borderRight = "0";
			cell3.style.paddingLeft = "5px";
			cell3.style.color = color;
			cell3.innerHTML = items_array[i][3];

			cell4.style.textAlign = "left";
			cell4.style.border = "1px solid #BBBBBB";
			cell4.style.borderRight = "0";
			cell4.style.paddingLeft = "5px";
			cell4.style.color = color;
			cell4.innerHTML = items_array[i][4];

			cell5.style.textAlign = "right";
			cell5.style.border = "1px solid #BBBBBB";
			cell5.style.borderRight = "0";
			cell5.style.paddingRight = "5px";
			cell5.style.color = color;
			cell5.innerHTML = balanceS;

			cell6.style.textAlign = "right";
			cell6.style.border = "1px solid #BBBBBB";
			cell6.style.borderRight = "0";
			cell6.style.color = color;
			cell6.innerHTML = price1S;

			cell7.style.textAlign = "right";
			cell7.style.border = "1px solid #BBBBBB";
			cell7.style.borderRight = "1";
			cell7.style.color = color;
			cell7.innerHTML = price2S;
		}
	}
	return length;
} //

function get_inventory_item_list(cId) 
{
	var chkInfo = "0";
	var mode = document.querySelector('input[name = "tab"]:checked').value;
	var e = document.getElementById("inventory_display");

//alert("get_inventory_item_list");
	e.onscroll = function(){
		if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
			items_all_position += add_item_table(e,items_all_count,items_all_position,items_all);
		}
	};

	if(items_all.length == 0) {
		itemLength = 0;
	}
	else {
		itemLength = items_all.length - 1;
	}

	if(items_all_complete)
	{
		document.getElementById("found").innerHTML = comma(items_all_count);
		make_item_table("1",items_all_count,0,items_all);
		return;
	}

	var xmlhttp = new XMLHttpRequest();
	var param = 'mode=' + mode + "&CID=" + cId + "&rowNum=" + itemLength + "&$chk_info=" + chkInfo +"&inactive=<?=$inactive?>";
	var items_tmp = [];
	
	try{
		xmlhttp.onreadystatechange = function() {
			document.getElementById("loading_image").style.display = "none";
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				items_tmp = xmlhttp.responseText.split('::');
				items_all_count = parseInt(items_tmp[0]);
				for(var i = 1; i < items_tmp.length; i++)
				{
					items_all.push(items_tmp[i].split(';'));
				}

				document.getElementById("found").innerHTML = comma(items_all_count);

				items_all_position = make_item_table("1",items_all_count,0,items_all);
				items_all_complete = true;
			}
		}
		
		xmlhttp.open("GET","inventorylist_search.php?" + param, true);
		xmlhttp.send();
	} catch(e) {
		document.getElementById("loading_image").style.display = "none";
		alert("서버와 통신에 문제가 있습니다. 관리자에게 문의 바랍니다. "+e.message)
	}

	document.getElementById("loading_image").style.display = "block";
}

function get_retail_item_list(cId) 
{
	var mode = document.querySelector('input[name = "tab"]:checked').value;
	var e = document.getElementById("retail_display");

	e.onscroll = function(){
		if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
			items_retail_position += add_item_table(e,items_retail_count,items_retail_position,items_retail);
		}
	};

	var chkInfo = "";
	if(document.getElementById('bby').checked)
		chkInfo = "1";		// Burnaby 매장
	else if(document.getElementById('sry').checked)
		chkInfo = "2";		// Surry 매장
	else
		chkInfo = "0";		// 도매

	check_info = chkInfo;

	if(items_retail.length == 0) {
		itemLength = 0;
	}
	else {
		itemLength = items_retail.length - 1;
	}

	if(items_retail_complete)
	{
		document.getElementById("found").innerHTML = comma(items_retail_count);
		make_item_table("2",items_retail_count,0,items_retail);
		return;
	}

	var xmlhttp = new XMLHttpRequest();
	var param = 'mode=' + mode + "&CID=" + cId + "&rowNum=" + itemLength + "&chk_info=" + chkInfo;
	var items_tmp = [];

	//alert(param);

	try{
		xmlhttp.onreadystatechange = function() {
			document.getElementById("loading_image").style.display = "none";
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				items_tmp = xmlhttp.responseText.split('::');
				items_retail_count = parseInt(items_tmp[0]);
				for(var i = 1; i < items_tmp.length; i++)
				{
					items_retail.push(items_tmp[i].split(';'));
				}
				
				document.getElementById("found").innerHTML = comma(items_retail_count);

				items_retail_position = make_item_table("2",items_retail_count,0,items_retail);
				items_retail_complete = true;
			}
		}
		
		xmlhttp.open("GET","inventorylist_search.php?" + param, true);
		xmlhttp.send();

	} catch(e) {
		document.getElementById("loading_image").style.display = "none";
		alert("서버와 통신에 문제가 있습니다. 관리자에게 문의 바랍니다. "+e.message)
	}
	document.getElementById("loading_image").style.display = "block";
}

var barcode = [];
var upbarcode = [];

function get_mapping_item_name_barcord(chkInfo,barcode) 
{
	var xmlhttp = new XMLHttpRequest();
	var param = "chk_info=" + chkInfo;

	var serialBarcode = barcode.join(";");
alert(serialBarcode);
	var data = new FormData();
	data.append('data',serialBarcode);

	try{
		xmlhttp.onreadystatechange = function() {
			document.getElementById("loading_image").style.display = "none";
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
alert(xmlhttp.responseText);
/*
				items_tmp = xmlhttp.responseText.split('::');
				items_mapping_count = parseInt(items_tmp[0]);
				for(var i = 1; i < items_tmp.length; i++)
				{
					oneRow = items_tmp[i].split(';');
					barcode[i] = oneRow[0];
					upbarcode[i] = oneRow[4];
					items_mapping.push(oneRow);
				}


				items_mapping_position = make_item_table("3",items_mapping_count,0,items_mapping);
				items_mapping_complete = true;
*/
			}
		}
		
		xmlhttp.open("POST","inventorylist_getprodname.php?" + param, true);
		xmlhttp.send(data);

	} catch(e) {
		document.getElementById("loading_image").style.display = "none";
		alert("서버와 통신에 문제가 있습니다. 관리자에게 문의 바랍니다. "+e.message)
	}
	document.getElementById("loading_image").style.display = "block";
}

function get_mapping_item_list(cId) 
{
	var mode = document.querySelector('input[name = "tab"]:checked').value;
	var e = document.getElementById("mapping_display");

	e.onscroll = function(){
		if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
			items_mapping_position += add_item_table(e,items_mapping_count,items_mapping_position,items_mapping);
		}
	};

	var chkInfo = "";
	if(document.getElementById('bby').checked)
		chkInfo = "1";		// Burnaby 매장
	else if(document.getElementById('sry').checked)
		chkInfo = "2";		// Surry 매장
	else
		chkInfo = "0";		// 도매
//alert("check_info:" + check_info + " chkInfo:" + chkInfo);
	check_info = chkInfo;

	if(items_mapping.length == 0) {
		itemLength = 0;
	}
	else {
		itemLength = items_mapping.length - 1;
	}

	if(items_mapping_complete)
	{
		document.getElementById("found").innerHTML = comma(items_mapping_count);
		make_item_table("3",items_mapping_count,0,items_mapping);
		return;
	}

	var xmlhttp = new XMLHttpRequest();
	var param = 'mode=' + mode + "&CID=" + cId + "&rowNum=" + itemLength + "&chk_info=" + chkInfo;
	var items_tmp = [];
	var oneRow = [];

//alert(param);

	try{
		xmlhttp.onreadystatechange = function() {
			document.getElementById("loading_image").style.display = "none";
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//alert(xmlhttp.responseText);
				items_tmp = xmlhttp.responseText.split('::');
				items_mapping_count = parseInt(items_tmp[0]);
				for(var i = 1; i < items_tmp.length; i++)
				{
					oneRow = items_tmp[i].split(';');
					barcode[i-1] = oneRow[0];
					upbarcode[i-1] = oneRow[4];
					items_mapping.push(oneRow);
				}

				document.getElementById("found").innerHTML = comma(items_mapping_count);

				//get_mapping_item_name_barcord(check_info,barcode);

				items_mapping_position = make_item_table("3",items_mapping_count,0,items_mapping);
				items_mapping_complete = true;
			}
		}
		
		xmlhttp.open("GET","inventorylist_search.php?" + param, true);
		xmlhttp.send();

	} catch(e) {
		document.getElementById("loading_image").style.display = "none";
		alert("서버와 통신에 문제가 있습니다. 관리자에게 문의 바랍니다. "+e.message)
	}
	document.getElementById("loading_image").style.display = "block";
}

function get_item_list(str, cId) {
	if(str == "1")
	{ 
		document.getElementById("inventory_display").innerHTML = "";
		document.getElementById("checkInfo").style.display = "none";
		document.getElementById("inactiveD").style.display = "block";
		document.getElementById("btn_new").disabled = false;
		get_inventory_item_list(cId);
	}
	else if(str == "2")
	{
		document.getElementById("retail_display").innerHTML = "";
		document.getElementById("checkInfo").style.display = "block";
		document.getElementById("inactiveD").style.display = "none";
		document.getElementById("tb").disabled = true;
		document.getElementById("btn_new").disabled = true;
		get_retail_item_list(cId);
	}
	else if(str == "3")
	{
		document.getElementById("mapping_display").innerHTML = "";
		document.getElementById("checkInfo").style.display = "block";
		document.getElementById("inactiveD").style.display = "none";
		document.getElementById("tb").disabled = false;
		document.getElementById("btn_new").disabled = true;
		get_mapping_item_list(cId);
	}
}

function search_inventory()
{
	//var SearchName = document.getElementById("SearchName").value;
	var opt = document.getElementById("opt").value;
	var searchKey = document.getElementById("searchKey").value;
	var temp = [];
	var pos = 0;

	items = items_all;
	count = items_all_count;

	items_search = [];

	if(searchKey == "")
	{
		var e = document.getElementById("inventory_display");

		e.onscroll = function(){
			if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
				items_all_position += add_item_table(e,items_all_count,items_all_position,items_all);
			}
		};
		document.getElementById("found").innerHTML = comma(count);
		make_item_table("1",count,0,items);
		return;
	}

	document.getElementById("inventory_display").onscroll = null;

	for(var i = 0; i < items.length; i++)
	{
		if(items[i] == null) continue;

		var wscode = items[i][0];
		var itemName = items[i][3];

		pos = wscode.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
			continue;
		}
		pos = itemName.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			//alert(itemName);
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
		}
	}

	document.getElementById("found").innerHTML = comma(items_search.length);
	make_item_table("1",items_search.length,-1,items_search);
}

function search_retail()
{
	//var SearchName = document.getElementById("SearchName").value;
	var opt = document.getElementById("opt").value;
	var searchKey = document.getElementById("searchKey").value;
	var temp = [];
	var pos = 0;

	items = items_retail;
	count = items_retail_count;

	items_search = [];

	var chkInfo = "";
	if(document.getElementById('bby').checked)
		chkInfo = "1";		// Burnaby 매장
	else if(document.getElementById('sry').checked)
		chkInfo = "2";		// Surry 매장
	else
		chkInfo = "0";		// 도매

//alert("check_info:"+check_info+" chkInfo:"+chkInfo);
	if(check_info != chkInfo)
	{
		items_retail = [];
		items_retail_count = 0;
		items_retail_complete = false;
		check_info = chkInfo;

		document.getElementById("searchKey").value = "";
		document.getElementById("retail_display").innerHTML = "";

		get_retail_item_list('<?=$cId?>');
		return;
	}

	if(searchKey == "")
	{
		var e = document.getElementById("retail_display");

		e.onscroll = function(){
			if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
				items_retail_position += add_item_table(e,items_retail_count,items_retail_position,items_retail);
			}
		};

		document.getElementById("found").innerHTML = comma(count);
		e.innerHTML = "";
		make_item_table("2",count,0,items);
		return;
	}

	document.getElementById("retail_display").onscroll = null;

	for(var i = 0; i < items.length; i++)
	{
		if(items[i] == null) continue;

		var wscode = items[i][0];
		var itemName = items[i][1];

		pos = wscode.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
			continue;
		}
		pos = itemName.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			//alert(itemName);
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
		}
	}

	document.getElementById("found").innerHTML = comma(items_search.length);
	make_item_table("2",items_search.length,-1,items_search);
}

function search_mapping()
{
	//var SearchName = document.getElementById("SearchName").value;
	var opt = document.getElementById("opt").value;
	var searchKey = document.getElementById("searchKey").value;
	var temp = [];
	var pos = 0;

	items = items_mapping;
	count = items_mapping_count;

	items_search = [];

	var chkInfo = "";
	if(document.getElementById('bby').checked)
		chkInfo = "1";		// Burnaby 매장
	else if(document.getElementById('sry').checked)
		chkInfo = "2";		// Surry 매장
	else
		chkInfo = "0";		// 도매

//alert("check_info:"+check_info+" chkInfo:"+chkInfo);
	if(check_info != chkInfo)
	{
		items_mapping = [];
		items_mapping_count = 0;
		items_mapping_complete = false;
		check_info = chkInfo;

		document.getElementById("searchKey").value = "";
		document.getElementById("mapping_display").innerHTML = "";

		get_mapping_item_list('<?=$cId?>');
		return;
	}

	if(searchKey == "")
	{
		var e = document.getElementById("mapping_display");

		e.onscroll = function(){
			if(e.offsetHeight + e.scrollTop >= e.scrollHeight){
				items_mapping_position += add_item_table(e,items_mapping_count,items_mapping_position,items_mapping);
			}
		};

		document.getElementById("found").innerHTML = comma(count);
		e.innerHTML = "";
		make_item_table("3",count,0,items);
		return;
	}

	document.getElementById("mapping_display").onscroll = null;

	for(var i = 0; i < items.length; i++)
	{
		if(items[i] == null) continue;

		var barcode = items[i][0];
		var itemName = items[i][2];
		var ubarcode = items[i][4];
		var uitemName = items[i][6];

		pos = barcode.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
			continue;
		}
		pos = itemName.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			//alert(itemName);
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
		}

		pos = ubarcode.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
			continue;
		}
		pos = uitemName.toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			//alert(itemName);
			if(opt == "Starts With" && pos != 0) continue;	
			items_search.push(items[i]);
		}
	}

	document.getElementById("found").innerHTML = comma(items_search.length);
	make_item_table("3",items_search.length,-1,items_search);
}

function search()
{
	var mode = document.querySelector('input[name = "tab"]:checked').value;

	if(mode == "inventory") search_inventory();
	else if(mode == "retail") search_retail();
	else if(mode == "item") search_mapping(); // mapping
}

function all_item_list() {

	var mode = document.querySelector('input[name = "tab"]:checked').value;
	document.getElementById("searchKey").value="";
	document.getElementById("opt").value="Starts With";

	if(mode == "inventory") search_inventory();
	else if(mode == "retail") search_retail();
	else if(mode == "item") search_mapping(); // mapping
}

function update_db() {
	var inactive = "";

	if(document.getElementById("inactive").checked)
		inactive = "yes";

	window.location.href="?inactive="+inactive;
}

function append(str) {
	newWindow=window.open("inventoryitem.php", "inventoryitem", "");	//자식창 OPEN
}

function toggle_item_list() {
	var div = document.getElementById("item_list_div");
	if (div.style.display !== "none") {
		div.style.display = "none";
		location.href = "./index.php";
	} else {
		div.style.display = "block";
	}
}

function setInit()
{
	var disp_div = document.getElementById("item_list_div");
	var disp_inventory = document.getElementById("inventory_display");
	var disp_retail = document.getElementById("retail_display");
	var disp_mapping = document.getElementById("mapping_display");

	//console.log("setHeight1 ## disp_div.style.height:"+disp_div.style.height+" disp_inventory.style.height:"+disp_inventory.style.height);

	disp_div.style.height = (window.innerHeight - 80) + "px";
	disp_inventory.style.height = (window.innerHeight - 186) + "px";
	disp_retail.style.height = (window.innerHeight - 186) + "px";
	disp_mapping.style.height = (window.innerHeight - 186) + "px";

	//console.log("setHeight2 ## disp_div.style.height:"+disp_div.style.height+" disp_inventory.style.height:"+disp_inventory.style.height);
}

</script>
<style type="text/css">
html, 
body {
	height: 90%;
}
</style>

</head>
<body onLoad="setInit();get_item_list('1', <?=$cId?>);">
<!-- <body onLoad="get_item_list('1', <?=$cId?>); tmr = setInterval(get_item_list, 500, '1', <?=$cId?>);">  -->
<?
include_once "includes/header.html";
include_once "includes/menu.html";
?>

<input type="hidden" name="mode" id="mode" value="">
<div id="item_list_div" style="position:absolute; left:8px; top:67px; border:2px solid <?=$bgcolor?>; width:1020px; height:88%; background-color:#ffffff; overflow-y:hidden; overflow-x:hidden;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#cad8ff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="letter-spacing:-1px; font-weight:bold;">INVENTORY ITEM</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" alt="Close" height="19" width="19" onClick="toggle_item_list()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div id="css_tabs">
					<input id="tab1" type="radio" name="tab" value="inventory" checked="checked" onClick="get_item_list('1',<?=$cId?>)"/>
					<input id="tab2" type="radio" name="tab" value="retail" onClick="get_item_list('2',<?=$cId?>)"/>
					<input id="tab3" type="radio" name="tab" value="item" onClick="get_item_list('3',<?=$cId?>)"/>
					<label for="tab1">Inventory List</label>
					<label for="tab2">Retail Item</label>
					<label for="tab3">Item Mapping</label>
					<div>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#c0c0c0;">
							<tr height="30">
								<td width="118px" class="doc_field_r">Search by&nbsp;&nbsp;</td>
								<td width="100px">
									<select id="opt" name="opt">
									  <option value="Contains" Selected>Contains</option>
									  <option value="Starts With">Starts With</option> 
									</select>		
								</td>
								<td width="175px"><input style="width:175px;" class="doc_field_l" id="searchKey" name="searchKey" type="text" value="<?=$searchKey?>" onKeyPress="if (event.keyCode==13){ search(); event.returnValue=false}"/></td>
								<td align="left" width="50px">&nbsp;<img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="return all_item_list();"></td>
								<td align="left" width="75px"><input style="font-size:9pt;height:25" type="button" value="SEARCH" class="btn_style" onClick="return search();"/></td>
								<td align="left" width="150px"><input id="btn_new" style="font-size:9pt;height:25" type="button" value="NEW ITEM" class="btn_style" onClick="return append('yes');"/></td>
								<td align="left" width="357px" class="doc_field_l">
									<div id="inactiveD">
									<input type="checkbox" id="inactive" onClick="update_db()" <?=$checked?> /><span style="position: relative;bottom: 2px;">Show Inactive items</span>
									</div>
								</td>
							</tr>
							<tr>
								<td width="118px" class="doc_field_r"># Found:&nbsp;&nbsp;</td>
								<td width="100px" align="left"><span style="width:100px;" class="doc_field_l" id="found"></span></td>

								<td width="175px" align="center" class="doc_field_c">
									<div id="checkInfo">
									<input type="radio" id="tb" name="chk_info" value="0" <?if($chk_info=='0') { echo 'checked'; }?>><? if ($cId == "1") { echo "TB"; } else { echo "MN"; } ?>
									<input type="radio" id="bby" name="chk_info" value="1" <?if($chk_info=='1') { echo 'checked'; }?>>BBY
									<input type="radio" id="sry" name="chk_info" value="2" <?if($chk_info=='2') { echo 'checked'; }?>>SRY
									</div>
								</td>
								<td width="275px" colspan="3">&nbsp;</td>
							</tr>
						</table>
					</div>
					<div class="tab1_content">
						<div id="inventory_display" style="height:500px; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab2_content">
						<div id="retail_display" style="height:80%; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab3_content">
						<div id="mapping_display" style="height:80%; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div id="loading_image" style="background-color:#ffffff; position:absolute; left:450px; top:150px; display:none; widht:100px height:100px margin-top:10px; text-align:center;"><img src="css/img/ajax-loader.gif"></br>자료 처리 중입니다.</div>
				</div>
			</td>
		</tr>
	</table>
</div>


</body>
</html>
