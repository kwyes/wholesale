<?php
session_start();
$CID = $_SESSION['staffCID'];
/*
sales_packing.php?search_date=<?=$row['tDate']?>&target_date=<?=$row['tDate']?>&invoiceno=<?=$row['tInvNo']?>&customercode=<?=$row['tCust']
*/
$customercode = ($_REQUEST["customercode"]) ? $_REQUEST["customercode"] : $_GET['customercode'];
$customername = ($_REQUEST["customername"]) ? $_REQUEST["customername"] : $_GET['customername'];
$invoiceno = ($_GET['invoiceno']) ? $_GET['invoiceno'] : $_POST['invoiceno'];

//$invoiceno =  iconv('euc-kr', 'utf-8', $invoiceno);

$ID = ($_GET['ID']) ? $_GET['ID'] : $_POST['ID'];
$target_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];

include_once "includes/db_configms.php";
include_once "includes/common_class.php";
include_once "login_check.php";

$today = date("Y-m-d");

//if($orderType == "old") {
	$arrary = getSaleOrder($CID,$orderNo);
	$passName = $arrary[0];
	$shipto = $arrary[1];
	$orderMemo = $arrary[2];
	$customer_query = "SELECT TOP 1 CONVERT(char(10), a.tDate, 126) AS odrDate, a.CID, a.tPassWord, a.tCust, a.tInvNo, b.totalAmount, b.shipto, b.SalesMemo, c.shipto AS BillTo, d.salesman, b.colStatus, b.CustomerPO FROM trSales a ".
					  " LEFT JOIN trSalesMaster b ON a.tInvNo=b.colInvNo AND b.CID='$CID' AND b.ID='$ID' ".
					  " LEFT JOIN ShipTo c ON c.CardID=a.tCust AND seq=1 AND c.CID='$CID' ".
					  " LEFT JOIN trOrderMaster d ON d.tOrdNo = b.OrderNo AND d.CID='$CID' ".
					  "WHERE a.tInvNo = '$invoiceno' AND a.CID = '$CID' ";
//echo $customer_query;
	$customer_query_result = mssql_query($customer_query);
	$customer_row = mssql_fetch_array($customer_query_result);

	$totalAmount = $customer_row['totalAmount'];
	$order_date = $customer_row['odrDate'];
	$staffID = $customer_row['tPassWord'];
	$BillTo = $customer_row['BillTo'];
	$Salesperson = $customer_row['salesman'];
	$invoiceStatus = $customer_row['colStatus']; 
	$SalesMemo = Br_iconv($customer_row['SalesMemo']); 
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script language="JavaScript" src="js/date_picker.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WholeSale INVENTORY SYSTEM</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<style>
#css_tabs {
    font-family:'nanumgothic', '나눔고딕', 'malgun gothic', '맑은 고딕', 'dotum', '돋움', sans-serif
}
/* 탭 선택 시 표시할 요소(div) 정의(1번 탭 선택 시 첫 번째 div 요소 표시) */
#css_tabs > input:nth-of-type(1), #css_tabs > input:nth-of-type(1) ~ div:nth-of-type(1), #css_tabs > input:nth-of-type(2), #css_tabs > input:nth-of-type(2) ~ div:nth-of-type(2) {
    display:none
}
#css_tabs > input:nth-of-type(1):checked ~ div:nth-of-type(1), #css_tabs > input:nth-of-type(2):checked ~ div:nth-of-type(2) {
    display:block
}
/* 라벨 기본 스타일 지정 */
#css_tabs > label {
    display:inline-block;
    font-variant:small-caps;
    font-size:.9em;
    padding:5px;
    text-align:center;
    width:15%;
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
#css_tabs > input:nth-of-type(1):checked ~ label:nth-of-type(1), #css_tabs > input:nth-of-type(2):checked ~ label:nth-of-type(2), #css_tabs > label:hover {
    background:tomato;
    color:#fff
}

#css_tabs > label:hover {
    background:gold;
    color:#fff
}

/* 실제 내용이 담긴 div 요소 스타일 지정 */
#css_tabs .tab1_content, #css_tabs .tab2_content {
    border:1px solid #ddd;
}

// Item list table style
#order_table tr {
	letter-spacing : -1px;
	font-family : verdana;
	font-size : 12px;
}

#order_table tr:nth-child(odd) {
	background-color: #dddddd;
}

#order_table th {
	text-align : center;
	color : #ffffff;
	border-top : 1px solid #BBBBBB;
	border-left : 1px solid #BBBBBB;
}

#order_table td {
	border-top : 1px solid #BBBBBB;
	border-left : 1px solid #BBBBBB;
	letter-spacing: -1px;
	font-family:"verdana";
	font-size: 12px;
}

.order_table_head {
	color : #ffffff;
	letter-spacing: -1px;
	text-align:center;
	background-color: #ff6666;
	font-family:"verdana";
	font-size: 12px;
}

</style>
<script>
// customer
function search_customer() {
	hide_toggle_div('item');
	hide_toggle_div('priceQty');
	var search_key = document.getElementById("customername").value;
	if(search_key) {
		document.getElementById("customer_iframe").src = "sales_order_search.php?mode=customer&key=" + search_key;
		
		var pos = document.getElementById("customername").getBoundingClientRect();
		document.getElementById("search_customer_result").style.left = pos.left + "px";
		document.getElementById("search_customer_result").style.top = pos.top + 21 + "px";
	} else {
		alert("검색할 고객 전화번호/이름을 입력하세요.");
	}
}

function toggle_customer_list() {
	$(document).ready(function(){
		$("#search_customer_result").animate({
			height:'toggle'
		});
	});
}

function re_search() {
	hide_toggle_div("item");
	document.forms.order_sheet.customer.value = "";

	var table = document.getElementById("customer_table");
	//table.rows[1].cells[1].style.paddingLeft = "1px";
	//table.rows[2].cells[1].innerHTML = '<input size="12" type="text" id="search_customer_key" style="width:150px; background-color:#e2e2e2;" onKeyPress="if (event.keyCode==13){ search_customer(); event.returnValue=false}">';
	document.getElementById("customer_btn").setAttribute("onclick", "search_customer()")
	document.getElementById("customer_btn").value = "검색";
}

function select_customer(CardNo,CardName,cId) {
	get_item_list(CardNo,cId);
	//document.forms.order_sheet.customercode.value = CardNo;
	document.forms.order_sheet.customername.value = CardName;
	orderNo = document.forms.order_sheet.orderNo.value;

	location="sales_packing.php?customercode="+CardNo+"&customername="+CardName+"&orderNo="+orderNo;

	document.getElementById("search_customer_result").style.display = "none";
}
// customer

function select_old_customer(CardNo,CardName,cId) {
	get_item_list(CardNo,cId);

	document.getElementById("search_customer_result").style.display = "none";
}

function change_orderDate(cId) {
	// date_picker.js 에 caller 있음
	var key = document.getElementsByName("search_order_date")[0].value;
	document.getElementById("orderList_iframe").src = "sales_order_search.php?mode=order_list&key=" + key;
}

function set_deliveryDate(deliDate) {
	var table = document.getElementById("customer_table");
	table.rows[2].cells[3].innerHTML = deliDate + "<input type='hidden' name='delivery_date' value='" + deliDate + "'>";
}

function hide_toggle_div(div) {
	if(div == "customer")	$("#search_customer_result").hide();
	if(div == "item")			$("#item_list_div").hide();
	if(div == "priceQty")		$("#item_priceQty_div").hide();
}

function select_order(orderNo,CardId,CardName) {
	var answer = confirm("완료하지 않은 정보는 저장되지 않습니다.");
	if(answer) {
		if(orderNo) {
			var url = "sales_packing.php?orderNo=" + orderNo + "&customercode=" + CardId + "&customername=" + CardName;
			location.replace(url);
		} else {
			location.replace("sales_packing.php");  
		}
	}
}
// order list

// item list
function toggle_item_list() {
	hide_toggle_div('customer');
	hide_toggle_div('priceQty');
	if(document.forms.order_sheet.customercode.value == "") {
		alert("Customer를 먼저 선택해주세요");
		return;
	}

	$(document).ready(function(){
		$("#item_list_div").animate({
			width:'toggle'
		});
	});
}

function toggle_search_input(mode) {
	if(mode == "item") {
		document.getElementById("search_item_key").style.display = "";
	} else {
		document.getElementById("search_item_key").style.display = "none";
	}
}

function get_item_list(str, cId) {
	show_loading_image();

	CardId = document.forms.order_sheet.customercode.value;

	var mode = document.querySelector('input[name = "tab"]:checked').value;
	div_id = mode + "_result";

	if (str.length == 0) { 
		document.getElementById(div_id).innerHTML = "";
		hide_loading_image();
		return;
	} else {
		var xmlhttp = new XMLHttpRequest();
		var param = 'mode=' + mode + "&CID=" + <?=$CID?> + "&CardId=" + CardId + "&key=" + encodeURIComponent(str);

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById(div_id).innerHTML = xmlhttp.responseText;
				hide_loading_image();
			}
		}
		xmlhttp.open("GET","sales_order_search.php?" + param, true);
		xmlhttp.send();
	}    
}

function show_loading_image() {
	document.getElementById('loading_image').style.display = "";
}

function hide_loading_image() {
	document.getElementById('loading_image').style.display = "none";
}

function select_item2(wscode,prodowncode,tid,itemBarcode,itemName,prodsize,itemPrice,itemQty,itemGst,itemPst,itemAmt,itemMemo) {
	var item_num = parseInt(document.forms.order_sheet.item_num.value);
	var table = document.getElementById("order_table");
	var oldValue=[];
	var oldQty=[];
	var oldMemo=[];

	for(var i = 0; i < item_num; i++) {
		oldValue[i] = document.getElementsByName('order_price[]')[i].value;
		oldQty[i] = document.getElementsByName('order_qty[]')[i].value;
		oldMemo[i] = document.getElementsByName('order_memo[]')[i].value;
	}
//	var TableLen = table.rows.length;
	
	var new_item_num = 1;
	var row = table.insertRow(new_item_num);

	row.style.height = "25";

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
	var cell11 = row.insertCell(11);
	var cell12 = row.insertCell(12);

	var ii = item_num;
	for(var j = item_num + 1; j >= 1; j--,ii--) {
<?
$readOnly = "";
if($invoiceStatus != 'E' && $invoiceStatus != 'X')
{
?>
		table.rows[j].cells[7].innerHTML = "<input name='order_price[]' style='width:55px; text-align:right;' type='text' value='" + parseFloat(oldValue[ii-1]).toFixed(2) + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, " + j + ")' onfocus='get_item_priceQty(\"" + itemBarcode + "\", \"" + itemName + "\", " + j + ")' required/>";
		table.rows[j].cells[6].innerHTML = "<input name='order_qty[]' style='width:25px; text-align:right;' type='text' value='" + oldQty[ii-1] + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, " + j + ")' required/>";
		table.rows[j].cells[11].innerHTML = "<input name='order_memo[]' style='width:100px; text-align:left;' type='text' value='" + oldMemo[ii-1] + "' maxlength='50' />";
		table.rows[j].cells[12].innerHTML ="<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item(" + j + ")'>";
<?
} else {
	$readOnly = "readonly";
?>
		table.rows[j].cells[7].innerHTML = "<input name='order_price[]' style='width:55px; text-align:right;' type='text' value='" + parseFloat(oldValue[ii-1]).toFixed(2) + "' readonly/>";
		table.rows[j].cells[6].innerHTML = "<input name='order_qty[]' style='width:25px; text-align:right;' type='text' value='" + oldQty[ii-1] + "' readonly/>";
		table.rows[j].cells[11].innerHTML = "<input name='order_memo[]' style='width:100px; text-align:left;' type='text' value='" + oldMemo[ii-1] + "' maxlength='50' readonly/>";
		table.rows[j].cells[12].innerHTML = " ";
<?
}
?>
	}

	cell0.style.textAlign = "center";
	cell0.style.borderRight = "0";
	cell0.innerHTML = item_num + 1;

	cell1.style.textAlign = "left";
	cell1.style.borderRight = "0";
	cell1.style.paddingLeft = "5px";
	cell1.innerHTML = wscode+"<input name='wscode[]' type='hidden' value='"+wscode+"'>";

	cell2.style.textAlign = "center";
	cell2.style.borderRight = "0";
	cell2.innerHTML = prodowncode+"<input name='prodowncode[]' type='hidden' value='"+prodowncode+"'><input name='tid[]' type='hidden' value='"+tid+"'>";

	cell3.style.textAlign = "left";
	cell3.style.borderRight = "0";
	cell3.style.paddingLeft = "5px";
	cell3.innerHTML = itemBarcode+ + "<input type='hidden' name='order_item[]' value='" + itemBarcode + "'>"

	cell4.style.textAlign = "left";
	cell4.style.borderRight = "0";
	cell4.style.paddingLeft = "8px";
	cell4.innerHTML = itemName;

	cell5.style.textAlign = "left";
	cell5.style.borderRight = "0";
	cell5.style.paddingLeft = "8px";
	cell5.innerHTML = prodsize;

	cell6.style.borderRight = "0";
	cell6.style.textAlign = "right";
	cell6.style.paddingRight = "1px";
	if(itemQty == 0) {
		cell6.style.backgroundColor = "red";
	}
	cell6.innerHTML = "<input name='order_qty[]' style='width:25px; text-align:right;' type='text' value='" + itemQty + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, " + new_item_num + ")' <?=$readOnly?> required/>";
	
	if(itemPrice == 0) {
		cell7.style.backgroundColor = "red";
	}
	cell7.style.borderRight = "0";
	cell7.style.textAlign = "right";
	cell7.style.paddingLeft = "1px";
	cell7.innerHTML = "<input name='order_price[]' style='width:55px; text-align:right;' type='text' value='" + parseFloat(itemPrice).toFixed(2) + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, " + new_item_num + ")' onfocus='get_item_priceQty(\"" + itemBarcode + "\", \"" + itemName + "\", " + new_item_num + ")' <?=$readOnly?> required/>";

	cell8.style.textAlign = "right";
	cell8.style.borderRight = "0";
	cell8.style.paddingLeft = "5px";
	if(itemGst != 0)	cell8.innerHTML = itemGst;
	else cell8.innerHTML = "";
	
	cell9.style.textAlign = "right";
	if(itemPst != 0)	cell9.innerHTML = itemPst;
	else cell9.innerHTML = "";

	cell10.style.textAlign = "right";
	if(itemPrice || itemQty) {
		cell10.innerHTML = parseFloat(itemPrice * itemQty).toFixed(2) ;
	} else {
		cell10.innerHTML = 0.00;
	}
	
	cell11.style.textAlign = "center";
	cell11.style.paddingLeft = "2px";
	cell11.innerHTML = "<input name='order_memo[]' style='width:100px; text-align:left;' type='text' value='" + itemMemo +"' maxlength=50 <?=$readOnly?> />";

	cell12.style.textAlign = "center";

	if(new_item_num == 1) {
		document.getElementById('complete_btn').style.display = "";
	}
	document.forms.order_sheet.item_num.value = item_num + 1;
}

function onlyNumber(event) {
	event = event || window.event;
	var keyID = (event.which) ? event.which : event.keyCode;
	if ( (keyID >= 48 && keyID <= 57) || (keyID >= 96 && keyID <= 105) || keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39 || keyID == 189 || keyID == 190 || keyID == 110 || keyID == 9) {
		return;
	} else {
		return false;
	}
}

function check_value(str, cell, row_num) {
	var table = document.getElementById("order_table");
	if (str.length != 0 && str != 0) {
		table.rows[row_num].cells[cell].style.backgroundColor = "";
		if(cell == 7) {
			var price = parseFloat(document.getElementsByName('order_price[]')[row_num-1].value);
			document.getElementsByName('order_price[]')[row_num-1].value = price.toFixed(2);
		}
		if(cell == 6) {
			var qty = parseFloat(document.getElementsByName('order_qty[]')[row_num-1].value);
			document.getElementsByName('order_qty[]')[row_num-1].value = qty;
		}
	} else {
		if(cell == 7)	document.getElementsByName('order_price[]')[row_num-1].value = 0.00;
		if(cell == 6)	document.getElementsByName('order_qty[]')[row_num-1].value = 0;
		table.rows[row_num].cells[cell].style.backgroundColor = "red";
	}
	calculate_sum(row_num);
}

function calculate_sum(row_num) {
	var price = document.getElementsByName('order_price[]')[row_num-1].value;
	var qty = document.getElementsByName('order_qty[]')[row_num-1].value;

	if(price && qty) {
		var table = document.getElementById("order_table");
		var sum = parseFloat(price * qty);
		var prev_price = parseFloat(table.rows[row_num].cells[10].innerHTML);
		table.rows[row_num].cells[10].innerHTML = sum.toFixed(2);
		table = document.getElementById("order_table_wrap");
		table.rows[0].cells[1].innerHTML = (parseFloat(table.rows[0].cells[1].innerHTML) - prev_price + sum).toFixed(2);
	}
}

function calculate_sum2() {
	var price, qty, sum = 0;
	var i = 0;

	price = document.getElementsByName('order_price[]');
	qty = document.getElementsByName('order_qty[]');
	gst = document.getElementsByName('order_gst[]');
	pst = document.getElementsByName('order_pst[]');

	for(i = 0; i < price.length; i++)
	{
		if(price[i].value !=0 & qty[i].value !=0)
			sum = sum + parseFloat(price[i].value * qty[i].value) + parseFloat(gst[i].value) + parseFloat(pst[i].value);
	}
	if(sum) {
		table = document.getElementById("order_table_wrap");
		table.rows[0].cells[1].innerHTML = sum.toFixed(2);
	}
}

function delete_item(row) {

//alert(row);
	var item_num = parseInt($("input[name=item_num]").val());
	var table = document.getElementById("order_table");
	var del_price = parseFloat(table.rows[row].cells[10].innerHTML);

	$("input[name=item_num]").val(item_num - 1);
	show_button();

	if(row < item_num) {
		for(var i = row + 1; i < table.rows.length; i++) { // 삭제할 열 다음 열의 정보를 삭제 이후 가져야할 값으로 수정
			var itemBarcode = table.rows[i].cells[3].innerHTML;
			var itemName = table.rows[i].cells[4].innerHTML;
			var j = i-1;
			table.rows[i].cells[12].innerHTML = "<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item(" + j + ")'>";

			document.getElementsByName('order_price[]')[i-1].setAttribute("onfocus", "get_item_priceQty(" + itemBarcode + ", " + itemName + ", " + j + ")");
			var price = document.getElementsByName('order_price[]')[i-1].setAttribute("onblur", "check_value(this.value, 6, " + j + ")");
			var qty = document.getElementsByName('order_qty[]')[i-1].setAttribute("onblur", "check_value(this.value, 7, " + j + ")");
		}
		for(i = 1; i < row; i++) {
			table.rows[i].cells[0].innerHTML = parseInt(table.rows[i].cells[0].innerHTML) - 1;
		}
	}
	table.deleteRow(row);
	table = document.getElementById("order_table_wrap");
	table.rows[0].cells[1].innerHTML = (parseFloat(table.rows[0].cells[1].innerHTML) - del_price).toFixed(2);
}

function get_item_priceQty(itemBarcode, itemName, row) {
	hide_toggle_div('item');
	hide_toggle_div('order');
}
function toggle_item_priceQty() {
	$(document).ready(function(){
		$("#item_priceQty_div").animate({
			width:'toggle'
		});
	});
}
// order sheet

function proceed_submit(mode) {
	var table = document.getElementById("order_table");
	var item_num = parseInt(document.forms.order_sheet.item_num.value);
	
	for(var i = 1; i <= item_num; i++) {
		if(table.rows[i].cells[5].style.backgroundColor == "red" || table.rows[i].cells[4].style.backgroundColor == "red") {
			alert("가격/수량이 입력되지 않았습니다.");
			return false;
		}
	}
	var answer = confirm("수정 하시겠습니까?");

	if(answer) {
		document.forms.order_sheet.mode.value = mode;
		document.forms.order_sheet.submit();
	} else {
		return false;
	}
}

var dragapproved=false;
var minrestore=0;
var initialwidth,initialheight;
var ie5=document.all&&document.getElementById;
var ns6=document.getElementById&&!document.all;

function drag_drop(e){
	if (ie5&&dragapproved&&event.button==1) {
		document.getElementById("item_list_div").style.left=tempx+event.clientX-offsetx;
		document.getElementById("item_list_div").style.top=tempy+event.clientY-offsety;
	}
	else if (ns6&&dragapproved){
		document.getElementById("item_list_div").style.left=tempx+e.clientX-offsetx;
		document.getElementById("item_list_div").style.top=tempy+e.clientY-offsety;
	}
}

function initializedrag(e) {
	offsetx=ie5? event.clientX : e.clientX;
	offsety=ie5? event.clientY : e.clientY;
	if (ie5)
	document.getElementById("saver").style.display='';

	tempx=parseInt(document.getElementById("item_list_div").style.left);
	tempy=parseInt(document.getElementById("item_list_div").style.top);

	dragapproved=true;
	document.onmousemove=drag_drop;
}

function invoice_print(code, cid, invdate, invno) {
	var popupw;
	if(cid == '1')
		popupw = window.open("FormTBInvoiceWithoutFrame.php?target_date="+invdate+"&invoiceno="+invno);
	else
		popupw = window.open("FormMannaInvoiceWithoutFrame.php?target_date="+invdate+"&invoiceno="+invno);
	try
	{
		popupw.focus();
		var urlA = window.opener.location.href.split("?");
		window.opener.location = urlA[0]+"?currentTab=tab3";
		//window.opener.location.reload();
		window.open('about:blank','_self').close();
	}
	catch(e)
	{
		alert("Pop-up Blocker is enabled! Please add this site to your exception list.");
	}
	return false;
}

function invoice_download(code, cid, invdate, invno) {
	var popupw;
	if(cid == '1')
		popupw = window.open("FormTBInvoice.php?target_date="+invdate+"&invoiceno="+invno);
	else
		popupw = window.open("FormMannaInvoice.php?target_date="+invdate+"&invoiceno="+invno);
	try
	{
		popupw.focus();
		var urlA = window.opener.location.href.split("?");
		window.opener.location = urlA[0]+"?currentTab=tab3";
		//window.opener.location.reload();
		window.open('about:blank','_self').close();
	}
	catch(e)
	{
		alert("Pop-up Blocker is enabled! Please add this site to your exception list.");
	}
	return false;
}
// 인보이스 최종 마감
function invoice_delete(code, cid, invdate, invno) {
	//alert(code+" "+cid+" "+invdate+" "+invno);
	//sales_invoice_update.php

	var xmlhttp = new XMLHttpRequest();
	var param = "mode=delete&CID="+cid+"&invno="+invno;
	if(!confirm("인보이스 "+invno+" 를 삭제하시겠습니까?")) return false;

	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			alert(xmlhttp.responseText);
			var urlA = window.opener.location.href.split("?");
			window.opener.location = urlA[0]+"?currentTab=tab3";
			//window.opener.location.reload();
			window.open('about:blank','_self').close();
		}
	}

	xmlhttp.open("GET","sales_invoice_update.php?"+param,true);
	xmlhttp.send();
	return false;
}
// 인보이스 최종 마감
function invoice_close(code, cid, invdate, invno) {
	//alert(code+" "+cid+" "+invdate+" "+invno);
	//sales_invoice_update.php

	var xmlhttp = new XMLHttpRequest();
	var param = "mode=close&CID="+cid+"&invno="+invno;
	if(!confirm("인보이스 "+invno+" 를 마감하시겠습니까?")) return false;

	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			alert(xmlhttp.responseText);
			var urlA = window.opener.location.href.split("?");
			window.opener.location = urlA[0]+"?currentTab=tab3";
			//window.opener.location.reload();
			window.open('about:blank','_self').close();
		}
	}

	xmlhttp.open("GET","sales_invoice_update.php?"+param,true);
	xmlhttp.send();
	return false;
}

function show_button()
{
	$("#complete_btn").show();
}

function setInit()
{
	var newHeight = window.innerHeight - 270;

	if(newHeight < 200) newHeight = 200;
	$("#order_table_div").css("height",newHeight + "px");
}

if (ie5||ns6)
document.onmouseup=new Function("dragapproved=false;document.onmousemove=null;document.getElementById('saver').style.display='none'");

</script>
</head>
<body style="background-color:#eeecea" onload="setInit()">

<table width="760px" style="background-color:#ffffff;">
	<tr>
		<td width="150px;" class="doc_title"><b>■ Sales &gt INVOICE</b></td>
		<td align="right">&nbsp;</td>
	</tr>
</table>

<!-- <form name="order_sheet" action="sales_order_update.php" method="POST" accept-charset="utf-8"> -->
<form name="order_sheet" action="sales_invoice_update.php" method="POST" accept-charset="utf-8">
<input type="hidden" name="customercode" value="<?=$customercode?>">
<input type="hidden" name="customername" value="<?=$customername?>">
<input type="hidden" name="mode">
<input type="hidden" name="invno" value="<?=$invoiceno; ?>">
<input type="hidden" name="item_num" value=0>
<input type="hidden" name="staffID" value="<?=$staffID; ?>">
<input type="hidden" name="CID" value="<?=$CID; ?>">
<div id="orderSheet_wrap" style="margin-top:5px; width:1024px;">
	<table>
	<tr>
		<td>
			<table id="customer_table" width="760px" style="border-collapse:collapse; font-size:12px;">
				<tr height="30" style="background-color:#ff6666; border:1px solid #BBBBBB; border-bottom:0;">
					<td  width="380px" colspan="2" style="font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px;">ORDER INFORMATION</td>
					<td  width="380px" colspan="2" style="font-size:13px; font-weight:bold; color:#FFFFFF;">
						&nbsp;
					</td>
				</tr>
				<tr height="25" style="background-color:#ffffff; border:1px solid #BBBBBB; border-bottom:0;">
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Invoice No : </b></td>
					<td><?=$invoiceno?> &nbsp;</td>
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Amount : </b></td>
					<td>$<?=number_format($totalAmount,2)?></td>
				</tr>
				<tr height="25" style="background-color:#ffffff; border-left:1px solid #BBBBBB; border-right:1px solid #BBBBBB; border-bottom:0;">
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Customer : </b></td>
					<td width="290px"><?=$customername;?>(<?=$customercode?>)</td>
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Sales Person : </b></td>
					<td width="290px"><?=Br_iconv($Salesperson)?></td>
				</tr>
				<tr height="25" style="background-color:#ffffff; border-left:1px solid #BBBBBB; border-right:1px solid #BBBBBB; border-bottom:0px solid #BBBBBB;">
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Bill To : </b></td>
					<td width="290px"><?=Br_iconv($BillTo);?></td>
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Ship To : </b></td>
					<td width="290px"><?=Br_iconv($customer_row['shipto'])?></td>
				</tr>
				<tr height="35" style="background-color:#ffffff; border-left:1px solid #BBBBBB; border-right:1px solid #BBBBBB; border-bottom:1px solid #BBBBBB;">
					<td width="90px" class="doc_field_r" style="padding-right:5px; padding-top:3px; vertical-align:top;"><b>Memo :</b></td>
					<td width="290px">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
		<?  if($invoiceStatus == "O" || $invoiceStatus == "P") { ?>
									<textarea style="background-color:#e2e2e2; height:36px; width:290px; overflow:auto;" id="SalesMemo" name="SalesMemo" type="text" maxlength="100" onchange="show_button()"><?=$SalesMemo?></textarea>
		<?  } else { ?>
									<?=$SalseMemo?>
		<?  } ?>
								</td>
							</tr>
						</table>
					</td>
					<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>CustomerPO : </b></td>
					<td width="290px">
							<input size="12" type="text" style="background-color:#e2e2e2;" name="CustomerPO" value="<?=Br_iconv($customer_row['CustomerPO'])?>" onchange="show_button()" onkeypress="if(event.keyCode==13){ return false; }">
					</td>
				</tr>
			</table>
		</td>
		<td bgcolor="white">
			<table id="customer_info" width="263px" style="border-collapse:collapse; border:0px; font-size:12px; ">
				<tr>
					<td style="padding-left:5px">
<?if($invoiceno != ""){?>
						<input type="image" id="invoice_print" width="250px" height="25px" src="./images/BtnInvoiceForPrintU.png" onmousedown="this.src='./images/BtnInvoiceForPrintD.png'" onclick="this.src='./images/BtnInvoiceForPrintU.png'; return invoice_print(event, '<?=$CID?>', '<?=$target_date?>', '<?=$invoiceno?>')">
<?}?>
					</td>
				</tr>
				<tr>
					<td style="padding-left:5px">
<?if($invoiceno != ""){?>
						<input type="image" id="invoice_email" width="250px" height="25px" src="./images/BtnInvoiceForEmailU.png" onmousedown="this.src='./images/BtnInvoiceForEmailD.png'" onclick="this.src='./images/BtnInvoiceForEmailU.png'; return invoice_download(event, '<?=$CID?>', '<?=$target_date?>', '<?=$invoiceno?>')">
<?}?>
					</td>
				</tr>
				<tr>
					<td style="padding-left:5px">
<?if($invoiceno != "" && $invoiceStatus != 'E' && $invoiceStatus != 'X'){?>
						<input type="image" id="invoice_close" width="250px" height="25px" src="./images/BtnInvoiceCloseU.png" onmousedown="this.src='./images/BtnInvoiceCloseD.png'" onclick="this.src='./images/BtnInvoiceCloseU.png'; return invoice_close(event, '<?=$CID?>', '<?=$target_date?>', '<?=$invoiceno?>')">
<?}?>
					</td>
				</tr>
				<tr>
					<td style="padding-left:5px">
<?if($invoiceno != "" && $invoiceStatus != 'E' && $invoiceStatus != 'X'){?>
						<input type="image" id="invoice_delete" width="250px" height="25px" src="./images/BtnInvoiceDeleteU.png" onmousedown="this.src='./images/BtnInvoiceDeleteD.png'" onclick="this.src='./images/BtnInvoiceDeleteU.png'; return invoice_delete(event, '<?=$CID?>', '<?=$target_date?>', '<?=$invoiceno?>')">
<?}?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

	<table id="order_table_wrap" width="100%" style="border-collapse:collapse; margin-top:10px;" cellspacing=0 cellpadding=0>
		<tr height="30" style="border:1px solid #BBBBBB;">
			<td width="466px" align="center" style="background-color:#808080; font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px; border-right:1px solid #BBBBBB;">TOTAL ($)</td>
			<td width="80px" align="right" style="padding-right:5px;">0.00</td>
			<td width="29px" style="background-color:#808080; border-left:1px solid #BBBBBB;"></td>
			<td bgcolor="#808080">
				<div id="complete_btn" style="background-color:#808080; margin-top:0px; float:right; display:none;">
<? if($invoiceno != "" && $invoiceStatus != 'E' && $invoiceStatus != 'X'){?>
						<input type="button" value="수정" onClick="proceed_submit('modify')" style="width:70px; height:30px; font-size:12px; font-weight:bold;">
<? } ?>
				</div>
			</td>
		</tr>

		<tr height="30" style="background-color:#c0c0c0; border:1px solid #BBBBBB; border-bottom:0;">
			<td style="font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px;">ORDER ITEM LIST</td>
			<td colspan=3 align="right" style="padding-right:5px;"><!-- <input type="button" id="item_list_btn" value="Item List" onClick="toggle_item_list()" /></td> -->
		</tr>
		<tr style="border:1px solid #BBBBBB;">
			<td colspan="4"><div id="order_table_div" style="width:1024; overflow-y:scroll; overflow-x:hidde;"></div> </td>
				
		</tr>
	</table>
</div>
</form>

<div id="search_customer_result" style="border:2px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:600px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#808080;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr height="25">
						<td style="letter-spacing:-1px; font-weight:bold; color:#FFFFFF;">고객 검색결과</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="toggle_customer_list()"></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td><iframe id="customer_iframe" width="100%" height="300" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

<div id="search_orderList_result" scrolling="auto" style="position:absolute; left:770px; top:75px; border:2px solid #666666; width:450px; height:210px; display:none;" >
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#F6CECE;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100px" style="letter-spacing:-1px; font-weight:bold;">Order List</td>
						<td>
							<input type="button" value="<" onClick="PrevNext_orderDate('prev')">
							<input size="12" type="text" name="search_order_date" style="margin:5px;" value="<?=$today?>" onClick="datePicker(event,'search_order_date')" onChange="change_orderDate()" onkeydown="datePicker()">
							<input type="button" value=">" onClick="PrevNext_orderDate('next')">
						</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="toggle_order_list()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><iframe id="orderList_iframe" width="100%" height="175px" frameborder=0 marginheight="4" marginwidth="2"></iframe></td>
		</tr>
	</table>
</div>

<div id="item_priceQty_div" style="border:2px #666666 solid; background-color:#ffffff; position:absolute; z-index:10; display:none; width:300px; height:350px; left:0px; top:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="padding-left:5px; background-color:#F6CECE;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td id="item_priceQty_title" style="letter-spacing:-1px; font-weight:bold;"></td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="toggle_item_priceQty()"></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td><iframe id="item_priceQty_iframe" width="100%" height="325" frameborder=0></iframe></td>
		</tr>
	</table>
</div>

<div id="item_list_div" style="position:absolute; left:645px; top:259px; border:2px solid #666666; width:700px; height:455px; display:none; background-color:#ffffff;" onMousedown="initializedrag(event)">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#808080;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="letter-spacing:-1px; font-weight:bold; color:#FFFFFF;">상품검색</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="toggle_item_list()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div id="css_tabs">
					<input id="tab1" type="radio" name="tab" value="history" checked="checked" onClick="toggle_search_input(this.value)"/>
					<input id="tab2" type="radio" name="tab" value="item" onClick="toggle_search_input(this.value)"/>
					<span style="float:right;">
						<span id="loading_image" style="display:none; margin-top:10px; text-align:center;"><img src="css/img/ajax-loader.gif"></span>
						<input id="search_item_key" name="search_item_key" style="width:250px; margin:6px 18px 0px 10px; display:none;" placeholder="검색" onKeyPress="if (event.keyCode==13){ get_item_list(this.value, '<?=$CID?>'); event.returnValue=false}"/>
<!--						onkeyup="get_item_list(this.value, '<?=$CID?>')"/> -->
					</span>
					<label for="tab1">History</label>
					<label for="tab2">Item All</label>
					<div class="tab1_content">
						<div id="history_result" style="height:397px; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab2_content">
						<div id="item_result" style="height:397px; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id="saver"></div>

</body>
</html>

<?
echo "<script>select_old_customer('".$customercode."','".$customername."','".$CID."')</script>";

//if($orderType == "old") {

	$item_query = "SELECT a.wsCode, a.ProdOwnCode, CONVERT(char(10),tDate,120) AS tDate, tID, tInvNo, tProd, tQty, tOUprice, tPunit, tGst, tPst, tAmt, prodKname, ItemMemo FROM trSales a ".
				  "LEFT JOIN Inventory_Item b ON a.wsCode = b.wsCode AND a.ProdOwnCode = b.ProdOwnCode AND a.SuppCode = b.SuppCode ".
			      "WHERE tInvNo = '".$invoiceno."' AND a.CID='".$CID."' ".
			      "ORDER BY tID ASC ";
//echo $item_query;
	$item_query_result = mssql_query($item_query);
	$html = "<table id='order_table' width='100%' style='border-collapse:collapse; border: 1px solid #BBB'>".
				"<tr height='25'>".
					"<td width='30px' class='order_table_head'>SEQ</td>".
					"<td width='100px' class='order_table_head'>ITEM CODE</td>".
					"<td width='35px' class='order_table_head'>O/CD</td>".
					"<td width='115px' class='order_table_head'>UPC</td>".
					"<td width='304px' class='order_table_head'>ITEM NAME</td>".
					"<td width='85px' class='order_table_head'>UNIT</td>".
					"<td width='30px' class='order_table_head'>QTY</td>".
					"<td width='60px' class='order_table_head'>PRICE</td>".
					"<td width='40px' class='order_table_head'>GST</td>".
					"<td width='40px' class='order_table_head'>PST</td>".
					"<td width='60px' class='order_table_head'>AMOUNT</td>".
					"<td width='100px' class='order_table_head'>MEMO</td>".
					"<td width='25px' class='order_table_head'>&nbsp;</td>".
				"</tr>";
	$num_item = mssql_num_rows($item_query_result);
	// item_num 정보 초기화
	echo "<script>$('input[name=item_num]').val($num_item);</script>";
	$i = 0;
	while($item_row = mssql_fetch_array($item_query_result)) {
		$i++;
		$prodID = $item_row['tProd'];
		$wsCode = $item_row['wsCode'];
		$ProdOwnCode = $item_row['ProdOwnCode'];
		$tGst = $item_row['tGst'];
		$tPst = $item_row['tPst'];
		$tAmt = $item_row['tAmt'];
		$ItemMemo = Br_iconv($item_row['ItemMemo']);
		
		$array = getItemName($wsCode,$ProdOwnCode,$CID);

		if(trim($array[1]) !="") {
			$prodName = $array[0]." / ".$array[1];
		} else {
			$prodName = $array[0];
		}
		
		$tID = $item_row['tID'];
		$qty = $item_row['tQty'];
		$price = 0;
		$hiddenPrice = 0;
		if($item_row['tOUprice'] != "" || isset($item_row['tOUprice']))
		{
			$price = $item_row['tOUprice'];
			$hiddenPrice = $price;
		}	
		
		// 상태에 따른 정보 표시 구분
		$priceHtml = "<input name='order_price[]' style='width:60px; text-align:right;' type='text' value='".number_format($hiddenPrice,2)."' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, $i)' onchange='show_button()' required/>";
		$qtyHtml = "<input name='order_qty[]' style='width:30px; text-align:right;' type='text' value='$qty' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, $i)' onchange='show_button()' required/>";
		$memoHtml = "<input name='order_memo[]' style='width:100px; text-align:left;' type='text' value='$ItemMemo' maxlength='50' onchange='show_button()' required/>";
		$delHtml = "<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item($i)'>";
		
		if($price == 0) $priceBgcolor = "red";
		else $priceBgcolor = "";
		if($qty == 0) $qtyBgcolor = "red";
		else $qtyBgcolor = "";

		$html .= "<tr height='25'>".
		"<td align='center'> $num_item </td>".
		"<td align='left' style='padding-left:5px'> $wsCode <input name='order_wscode[]' type='hidden' value='$wsCode' /> </td>".
		"<td align='center'> $ProdOwnCode <input name='order_prodowncode[]' type='hidden' value='$ProdOwnCode' /> </td>".
		"<td align='left' style='padding-left:5px'> $prodID <input type='hidden' name='order_item[]' value='$prodID'></td>".
		"<td align='left' style='padding-left:5px'> $prodName </td>".
		"<td align='left' style='padding-left:5px'> $array[3] </td>".
		"<td align='center' style='background-color:$qtyBgcolor'> $qtyHtml </td>".
		"<td align='center' style='background-color:$priceBgcolor'> $priceHtml </td>".
		"<td align='right' style='padding-right:5px'> ".number_format($tGst,2)." <input type='hidden' name='order_gst[]' value='$tGst'></td>".
		"<td align='right' style='padding-right:5px'> ".number_format($tPst,2)." <input type='hidden' name='order_pst[]' value='$tPst'></td>".
		"<td align='right' style='padding-right:5px'>".number_format($qty * $price,2)."</td>".
		"<td align='center'> $memoHtml </td>".
		"<td align='center'> $delHtml </td>".
	"</tr>";

		$num_item--;
	}
	$html .= " </table> ";
	echo "<script>document.getElementById('order_table_div').innerHTML = \"$html\";</script>";
	echo "<script>calculate_sum2()</script>";
//}
?>
