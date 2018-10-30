<?php
session_start();
$CID = $_SESSION['staffCID'];
$customercode = ($_REQUEST["customercode"]) ? $_REQUEST["customercode"] : $_GET['customercode'];
$customername = ($_REQUEST["customername"]) ? $_REQUEST["customername"] : $_GET['customername'];

include_once "includes/db_configms.php";
include_once "includes/common_class.php";
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

#css_tabs2 {
    font-family:'nanumgothic', '나눔고딕', 'malgun gothic', '맑은 고딕', 'dotum', '돋움', sans-serif
}
/* 탭 선택 시 표시할 요소(div) 정의(1번 탭 선택 시 첫 번째 div 요소 표시) */
#css_tabs2 > input:nth-of-type(1), #css_tabs2 > input:nth-of-type(1) ~ div:nth-of-type(1), #css_tabs2 > input:nth-of-type(2), #css_tabs2 > input:nth-of-type(2) ~ div:nth-of-type(2) {
    display:none
}
#css_tabs2 > input:nth-of-type(1):checked ~ div:nth-of-type(1), #css_tabs2 > input:nth-of-type(2):checked ~ div:nth-of-type(2) {
    display:block
}
/* 라벨 기본 스타일 지정 */
#css_tabs2 > label {
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
#css_tabs2 > label:hover {
    cursor:pointer
}

/* 선택된 라벨, 커서를 올린 라벨 스타일 지정 */
#css_tabs2 > input:nth-of-type(1):checked ~ label:nth-of-type(1), #css_tabs2 > input:nth-of-type(2):checked ~ label:nth-of-type(2), #css_tabs2 > label:hover {
    background:tomato;
    color:#fff
}

#css_tabs2 > label:hover {
    background:gold;
    color:#fff
}

/* 실제 내용이 담긴 div 요소 스타일 지정 */
#css_tabs2 .tab1_content, #css_tabs2 .tab2_content {
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
		document.getElementById("customer_iframe").src = "sales_order_search.php?mode=customer&key=" + encodeURIComponent(search_key);
		
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
	table.rows[2].cells[1].innerHTML = '<input size="12" type="text" id="search_customer_key" style="width:150px; background-color:#e2e2e2;" onKeyPress="if (event.keyCode==13){ search_customer(); event.returnValue=false}">';
	document.getElementById("customer_btn").setAttribute("onclick", "search_customer()")
	document.getElementById("customer_btn").value = "검색";
}

function select_customer(status,CardNo,CardName,cId) {
	get_item_list(status,CardNo,cId);
	document.forms.order_sheet.customercode.value = CardNo;
	document.forms.order_sheet.customername.value = CardName;
	orderNo = document.forms.order_sheet.orderNo.value;

	location="sales_order.php?customercode="+CardNo+"&customername="+encodeURIComponent(CardName)+"&orderNo="+orderNo;

	document.getElementById("search_customer_result").style.display = "none";
}
// customer

function select_old_customer(status,CardNo,CardName,cId) {
	get_item_list(status,CardNo,cId);

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
			var url = "sales_order.php?orderNo=" + orderNo + "&customercode=" + CardId + "&customername=" + encodeURIComponent(CardName);
			location.replace(url);
		} else {
			location.replace("sales_order.php");  
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
			height:'toggle'
		});
	});
}

function history_item_list() {
	$(document).ready(function(){
		$("#history_item_list_div").animate({
			height:'toggle'
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

function putin_price(row,p) {
	var price = document.getElementsByName("order_price[]");

	if(isNaN(p)) p = 0;

	price[row].value = p.toFixed(2);
	check_value(price[row].value,6,row+1);
}

function get_history(wscode,owncode,row) {
	show_loading_image();

	CardId = document.forms.order_sheet.customercode.value;

	if (wscode.length == 0 || owncode.length == 0) { 
		hide_loading_image();
		return;
	} else {
		var xmlhttp = new XMLHttpRequest();
		var param = "mode=itemHistory&wscode=" + wscode + "&owncode=" + owncode;
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				hide_loading_image();

				var rtn = xmlhttp.responseText.split("::");
				var itemInfo;
				var itemInfoS = "";
				var numItem = parseInt(rtn[0]);
				if(rtn[1] != "")
				{
					itemInfo = rtn[1].split(";");
					itemInfoS = itemInfo[0];	// Korean name
					if(itemInfo[1] != "")
						itemInfoS += "/"+itemInfo[1];	// English name
					if(itemInfo[2] != "")
						itemInfoS += "/"+itemInfo[2];	// Size
				}
				if(numItem == 0)
				{
					alert("'"+wscode+"' 로 주문한 이력이 없습니다.");
					return;
				}
				// for all customer
				var tableHead = 
		'<table width="450px" cellpadding="2px" cellspacing="0px">'+
		'<tr style="font-family:verdana; font-size:13px;">'+
			'<td colspan="5" align="left">&nbsp;<b><font color="blue">'+itemInfoS+'</font></b></td>'+
		'</tr>'+
		'<tr style="background-color:#808080; letter-spacing:-1px; font-family:verdana; font-size:13px; color:#FFF; border:1px solid #BBBBBB;">'+
			'<td width="70px" align="center">DATE</td>'+
			'<td width="60px" align="center">C.CODE</td>'+
			'<td align="center">CUSTOMER NAME</td>'+
			'<td width="40px" align="center">PRICE</td>'+
			'<td width="30px" align="center">QTY</td>'+
		'</tr>';
		//'</table>';
				var itemsTable = "";
				var thisItemsTable = "";
				var tmp;
				var doc_field_name;
				for(var i = 2, j = 0; i <= numItem+1; i++)
				{
					if (i % 2 == 0)	doc_field_name = "doc_field_purchases_bg";
					else			doc_field_name = "doc_field_purchases";
					tmp = rtn[i].split(";");
					// for all customer table
					itemsTable += '<tr class="'+doc_field_name+'"><td style="border:1px solid #BBB;">' + tmp[0] + '</td>' +
						'<td align="center" style="border:1px solid #BBB;">' + tmp[1] + '</td>' +
						'<td style="border:1px solid #BBB;"><a href="javascript:putin_price('+row+','+tmp[3]+')" style="text-decoration:none; color:#000000">' + tmp[2] + '</a></td>' +
						'<td align="right" style="padding-right:5px; border:1px solid #BBB;"><a href="javascript:putin_price('+row+','+tmp[3]+')" style="text-decoration:none; color:#000000">' + tmp[3] + '</a></td>' +
						'<td align="right" style="padding-right:5px; border:1px solid #BBB;">' + tmp[4] + '</td></tr>';
					if(CardId == tmp[1]) // 현 고객 아이디가 동일 할 경우
					{
						if (j++ % 2 == 0)	doc_field_name = "doc_field_purchases_bg";
						else			doc_field_name = "doc_field_purchases";
						// for this customer table
						thisItemsTable += '<tr class="'+doc_field_name+'"><td style="border:1px solid #BBB;">' + tmp[0] + '</td>' +
							'<td align="center" style="border:1px solid #BBB;">' + tmp[1] + '</td>' +
						'<td style="border:1px solid #BBB;"><a href="javascript:putin_price('+row+','+tmp[3]+')" style="text-decoration:none; color:#000000">' + tmp[2] + '</a></td>' +
						'<td align="right" style="padding-right:5px; border:1px solid #BBB;"><a href="javascript:putin_price('+row+','+tmp[3]+')" style="text-decoration:none; color:#000000">' + tmp[3] + '</a></td>' +
							'<td align="right" style="padding-right:5px; border:1px solid #BBB;">' + tmp[4] + '</td></tr>';
					}
				}

				// 현재 고객의 주문 이력이 없을 경우
				if(j == 0)
				{
					thisItemsTable += '<tr><td colspan="5" align="center">이 항목에 대한 주문 이력이 없습니다.</td></tr>';
				}

				itemsTable = tableHead + itemsTable + '</table>';
				$("#all_cust_history").html(itemsTable);

				thisItemsTable = tableHead + thisItemsTable + '</table>';
				$("#this_cust_history").html(thisItemsTable);
				if($("#history_item_list_div").is(":hidden"))
					history_item_list();
			}
		}
		xmlhttp.open("GET","sales_order_search.php?" + param, true);
		xmlhttp.send();
	}    
}

function get_item_list(status, str, cId) {
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
		var param = 'mode=' + mode + "&CID=" + <?=$CID?> + "&CardId=" + CardId + "&key=" + encodeURIComponent(str) + "&status=" + status;
//alert(param);
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
// item list

// order sheet
function select_item(status,wscode,prodowncode,itemBarcode,itemName,prodsize,itemPrice,itemQty,itemMemo,init) {
	//var item_num = parseInt(document.forms.order_sheet.item_num.value);
	var item_num = parseInt($("input[name=item_num]").val());
	var table = document.getElementById("order_table");
	var oldValue=[];
	var oldQty=[];
	var oldMemo=[];

	if(isNaN(itemPrice)) itemPrice = 0;

	itemBarcode = itemBarcode.trim();
/*  2015.11.04 10/30일 영업,지원팀 미팅에서 box barcode대신 TB item code 를 사용하도록 요청받음. 아래 코드 수정
	if(itemBarcode == "") {
		alert("바코드가 없습니다. Inventory메뉴에서 해당 아이템의 바코드를 등록 후 사용 바랍니다.");
		return;
	}
*/
	// 2015.11.04 10/30일 영업,지원팀 미팅에서 box barcode대신 TB item code 를 사용하도록 요청받음. 아래 코드 수정
	if(init == false)
	{
		var wsCodes = document.getElementsByName('order_wscode[]');
		var ownCodes = document.getElementsByName('order_prodowncode[]');
		//for(var i = 1; i <= item_num; i++) {
		for(var i = 0; i < wsCodes.length; i++) {
			if(wscode == wsCodes[i].value && prodowncode == ownCodes[i].value) {
				if(!confirm("이미 오더장에 추가된 상품입니다. 추가하도록 할까요?"))
					return;
			}
		}
	}

	for(var i = 0; i < item_num; i++) {
		oldValue[i] = document.getElementsByName('order_price[]')[i].value;
		oldQty[i] = document.getElementsByName('order_qty[]')[i].value;
		oldMemo[i] = document.getElementsByName('order_memo[]')[i].value;
	}

	// 헤더 다음에 새 row 삽입
	var new_item_num = 1;
	var row = table.insertRow(new_item_num);

	// 새 row에 스타일 지정
	row.style.height = "25";

	// 새 컬럼 생성
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

	var ii = item_num;	// 테이블에 포함된 항목의 갯수 table.rows.length - 1
	for(var j = item_num + 1; j > 1; j--,ii--) { // 테이블 row가 추가되어 내용의 링크도 맞추어 주어야 함. 
		if(status == 'O') // 상태가 Open 인 경우
		{
<?
// 내부 접근일 경우 가격을 표시
if($_SESSION['ActiveIP'] != 'N')
{
?>
			table.rows[j].cells[6].innerHTML = "<input name='order_price[]' style='width:75px; text-align:right;' type='text' value='" + parseFloat(oldValue[ii-1]).toFixed(2) + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, " + j + ")' required/>";
<?
} else {
// 내부 접근일 경우 가격을 표시하지 않음.
?>
			table.rows[j].cells[6].innerHTML = "<input name='order_price[]' style='width:75px; text-align:right;' type='text' value='0' onkeydown='return onlyNumber(event)' disabled/>";
<?
}
?>
			table.rows[j].cells[7].innerHTML = "<input name='order_qty[]' style='width:40px; text-align:right;' type='text' value='" + oldQty[ii-1] + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, " + j + ")' required/>";
			table.rows[j].cells[9].innerHTML = "<input name='order_memo[]' style='width:140px; text-align:left;' type='text' value='" + oldMemo[ii-1] + "' maxlength='50' required/>";
			table.rows[j].cells[10].innerHTML ="<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item(" + j + ")'>";
		} else { // 상태가 Close 인 경우
<?
if($_SESSION['ActiveIP'] != 'N')
{
?>
			table.rows[j].cells[6].innerHTML = parseFloat(oldValue[ii-1]).toFixed(2) + "<input name='order_price[]' type='hidden' value='" + parseFloat(oldValue[ii-1]).toFixed(2) + "' />";
<?
} else {
?>
			table.rows[j].cells[6].innerHTML = "0 <input name='order_price[]' type='hidden' value='0' />";
<?
}
?>
			table.rows[j].cells[7].innerHTML = oldQty[ii-1] + "<input name='order_qty[]' type='hidden' value='" + oldQty[ii-1] + "'/>";
			table.rows[j].cells[9].innerHTML = oldMemo[ii-1] + "<input name='order_memo[]' type='hidden' value='" + oldMemo[ii-1] + "' />";
			table.rows[j].cells[10].innerHTML ="";
		}
		// 기존 항목들 링크 조정
		var tmp = table.rows[j].cells[4].innerHTML;
		
		var idx1 = tmp.search(/,[0-9]+\)/i);
		var idx2 = tmp.indexOf(")");
		var part1 = tmp.substr(0,idx1+1);
		var part2 = tmp.substr(idx2);
		tmp = part1 + (j-1) + part2;
//alert(tmp);
		table.rows[j].cells[4].innerHTML = tmp;
	}

	cell0.style.textAlign = "center";
	cell0.style.borderRight = "0";
	cell0.innerHTML = item_num + 1;

	cell1.style.textAlign = "left";
	cell1.style.borderRight = "0";
	cell1.style.paddingLeft = "5px";
	cell1.innerHTML = wscode + "<input name='order_wscode[]' type='hidden' value='" + wscode + "' />"; // 2015.11.04 10/30일 영업,지원팀 미팅에서 Barcode 입력이 어려워 wscode 와 prodowncode로 제품 주문이 가능토록 수정

	cell2.style.textAlign = "center";
	cell2.style.borderRight = "0";
	cell2.innerHTML = prodowncode + "<input name='order_prodowncode[]' type='hidden' value='" + prodowncode + "' />";  // 2015.11.04 10/30일 영업,지원팀 미팅에서 Barcode 입력이 어려워 wscode 와 prodowncode로 제품 주문이 가능토록 수정

	cell3.style.textAlign = "left";
	cell3.style.borderRight = "0";
	cell3.style.paddingLeft = "5px";
	cell3.innerHTML = itemBarcode;

	cell4.style.textAlign = "left";
	cell4.style.borderRight = "0";
	cell4.style.paddingLeft = "8px";
	cell4.innerHTML = '<a href="javascript:get_history(\''+wscode+'\',\''+prodowncode+'\',0)" style="text-decoration:none; color:black">' + itemName + '</a><input type="hidden" name="order_item[]" value=\'' + itemBarcode + '\'>';

	cell5.style.textAlign = "left";
	cell5.style.borderRight = "0";
	cell5.style.paddingLeft = "8px";
	cell5.innerHTML = prodsize;

	if(itemPrice == 0) {
<?
if($_SESSION['ActiveIP'] != 'N')
{
?>
		cell6.style.backgroundColor = "red";
<?
}
?>
	}
	cell6.style.borderRight = "0";
	cell6.style.textAlign = "center";
	cell6.style.width = "75px";
	if(status == 'O')
	{
<?
if($_SESSION['ActiveIP'] != 'N')
{
?>
		cell6.innerHTML = "<input name='order_price[]' style='width:75px; text-align:right;' type='text' value='" + parseFloat(itemPrice).toFixed(2) + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, " + new_item_num + ")' required/>";
<?
} else {
?>
		cell6.innerHTML = "<input name='order_price[]' style='width:75px; text-align:right;' type='text' value='0' onkeydown='return onlyNumber(event)' disabled/>";
<?
}
?>
	} else {
		cell6.innerHTML = parseFloat(itemPrice).toFixed(2) + "<input name='order_price[]' type='hidden' value='" + parseFloat(itemPrice).toFixed(2) + "' />";
	}

	if(itemQty == 0) {
		cell7.style.backgroundColor = "red";
	}
	cell7.style.textAlign = "center";
	cell7.style.borderRight = "0";
	cell7.style.width = "40px";
	if(status == 'O')
	{
		cell7.innerHTML = "<input name='order_qty[]' style='width:40px; text-align:right;' type='text' value='" + itemQty + "' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, " + new_item_num + ")' required/>";
	} else {
		cell7.innerHTML = itemQty + "<input name='order_qty[]' type='hidden' value='" + itemQty + "' />";
	}

	cell8.style.borderRight = "0";
	cell8.style.textAlign = "right";
	cell8.style.paddingRight = "5px";
	if(itemPrice || itemQty) {
		cell8.innerHTML = parseFloat(itemPrice * itemQty).toFixed(2) ;
	} else {
		cell8.innerHTML = 0.00;
	}

	cell9.style.borderRight = "0";
	cell9.style.width = "140px";
	if(status == 'O')
	{
		cell9.innerHTML = "<input name='order_memo[]' style='width:140px; text-align:left;' maxlength='50' type='text' value='" + itemMemo +"' />";
	} else {
		cell9.innerHTML = itemMemo + "<input name='order_memo[]' type='hidden' value='" + itemMemo +"' />";
	}

	cell10.style.textAlign = "center";
	if(status == 'O')
	{
		cell10.innerHTML = "<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item(" + new_item_num + ")'>";
	} else {
		cell10.innerHTML = "";
	}

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
		if(cell == 6) {
			var price = parseFloat(document.getElementsByName('order_price[]')[row_num-1].value);
			document.getElementsByName('order_price[]')[row_num-1].value = price.toFixed(2);
		}
		if(cell == 7) {
			var qty = parseFloat(document.getElementsByName('order_qty[]')[row_num-1].value);
			document.getElementsByName('order_qty[]')[row_num-1].value = qty;
		}
	} else {
		if(cell == 6)	document.getElementsByName('order_price[]')[row_num-1].value = 0.00;
		if(cell == 7)	document.getElementsByName('order_qty[]')[row_num-1].value = 0;
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
		var prev_price = parseFloat(table.rows[row_num].cells[8].innerHTML);
		table.rows[row_num].cells[8].innerHTML = sum.toFixed(2);
		table = document.getElementById("order_table_wrap");
		table.rows[0].cells[1].innerHTML = (parseFloat(table.rows[0].cells[1].innerHTML) - prev_price + sum).toFixed(2);
	}
}

function calculate_sum2(row_num) {
	var price, qty, sum = 0;

	for(var i=0; i<=row_num-1; i++) {
		price = document.getElementsByName('order_price[]')[i].value;
		qty = document.getElementsByName('order_qty[]')[i].value;
		if(price && qty) {
			sum = sum + parseFloat(price * qty);
		}
	}
	if(sum) {
		table = document.getElementById("order_table_wrap");
		table.rows[0].cells[1].innerHTML = sum.toFixed(2);
	}
}

function delete_item(row) {
	var item_num = parseInt($("input[name=item_num]").val());
	var table = document.getElementById("order_table");
	var del_price = parseFloat(table.rows[row].cells[8].innerHTML);

	$("input[name=item_num]").val(item_num - 1);
	show_button();
	
	if(row < item_num) {
		//for(var i = row-1; i < item_num; i++) {
		for(var i = row + 1; i < table.rows.length; i++) { // 삭제할 열 다음 열의 정보를 삭제 이후 가져야할 값으로 수정
			var itemBarcode = table.rows[i].cells[3].innerHTML;
			var itemName = table.rows[i].cells[4].innerHTML;
			var j = i-1;
			var k = i-2;
			table.rows[i].cells[10].innerHTML = "<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item(" + j + ")'>";

			document.getElementsByName('order_price[]')[i-1].setAttribute("onblur", "check_value(this.value, 6, " + j + ")");
			document.getElementsByName('order_qty[]')[i-1].setAttribute("onblur", "check_value(this.value, 7, " + j + ")");
			
			// 기존 항목들 링크 조정
			var tmp = table.rows[i].cells[4].innerHTML;
			
			var idx1 = tmp.search(/,[0-9]+\)/i);
			var idx2 = tmp.indexOf(")");
			var part1 = tmp.substr(0,idx1+1);
			var part2 = tmp.substr(idx2);
			tmp = part1 + k + part2;
	//alert(tmp);
			table.rows[i].cells[4].innerHTML = tmp;
		}
		// SEQ 조정
		for(i = 1; i < row; i++) {
			table.rows[i].cells[0].innerHTML = parseInt(table.rows[i].cells[0].innerHTML) - 1;
		}
	}
	table.deleteRow(row);
	table = document.getElementById("order_table_wrap");
	table.rows[0].cells[1].innerHTML = (parseFloat(table.rows[0].cells[1].innerHTML) - del_price).toFixed(2);
}

function toggle_item_priceQty() {
	$(document).ready(function(){
		$("#item_priceQty_div").animate({
			width:'toggle'
		});
	});
}
// order sheet

function test()
{
	window.location.reload();
}

function proceed_submit(mode) {
	var table = document.getElementById("order_table");
	var item_num = parseInt(document.forms.order_sheet.item_num.value);
	
	var deliveryDate = document.getElementsByName("delivery_date")[0].value;
	deliveryDate = deliveryDate.split("-");
	var delYear = parseInt(deliveryDate[0]);
	var delMonth = parseInt(deliveryDate[1])-1;
	var delDay = parseInt(deliveryDate[2]);

	var today = new Date();
	deliveryDate = new Date();
	deliveryDate.setFullYear(delYear, delMonth, delDay);

	if(deliveryDate < today) {
		if(mode == "modify" || mode == "delete") {
			alert("배송날짜가 지나 수정/삭제할 수 없습니다.");
		} else {
			alert("배송날짜를 변경해주십시요");
		}
		return false;
	}

	for(var i = 1; i <= item_num; i++) {
		if(table.rows[i].cells[5].style.backgroundColor == "red" || table.rows[i].cells[4].style.backgroundColor == "red") {
			alert("가격/수량이 입력되지 않았습니다.");
			return false;
		}
	}
	
	if(mode == "add")		var answer = confirm("완료 하시겠습니까?");
	if(mode == "modify")	var answer = confirm("수정 하시겠습니까?");
	if(mode == "delete")	var answer = confirm("삭제 하시겠습니까?");

	if(answer) {
		document.forms.order_sheet.mode.value = mode;
		document.forms.order_sheet.submit();
	} else {
		return false;
	}
}

function print_packing_list(code, status, cid, invno) {
	var popupw;

	if(confirm("Order form 을 출력합니다. \n계속 진행할까요?"))
	{
		if(cid == '1')
			popupw = window.open("FormTBOrder.php?orderno="+invno,"_blank");
		else
			popupw = window.open("FormMannaOrder.php?orderno="+invno,"_blank");
		try
		{
			popupw.focus();
			if(status == 'O')
			{
				var urlA = window.opener.location.href.split("?");
				window.opener.location = urlA[0]+"?currentTab=tab1";
			}
			window.open('about:blank','_self').close();
		}
		catch(e)
		{
			alert("Pop-up Blocker is enabled! Please add this site to your exception list.");
		}
	}
}

function show_button()
{
	$("#complete_btn").show();
}

</script>  
<script Language="Javascript">
var dragapproved=false;
var dragElement;
var minrestore=0;
var initialwidth,initialheight;
var ie5=document.all&&document.getElementById;
var ns6=document.getElementById&&!document.all;

function drag_drop(e){
	if (ie5&&dragapproved&&event.button==1) {
		dragElement.style.left=tempx+event.clientX-offsetx;
		dragElement.style.top=tempy+event.clientY-offsety;
	}
	else if (ns6&&dragapproved){
		dragElement.style.left=tempx+e.clientX-offsetx;
		dragElement.style.top=tempy+e.clientY-offsety;
	}
}

//function initializedrag(e,element) {
function initializedrag(e,ele) {
	offsetx=ie5? event.clientX : e.clientX;
	offsety=ie5? event.clientY : e.clientY;
	if (ie5)
	document.getElementById("saver").style.display='';

	dragElement = ele;
	tempx=parseInt(dragElement.style.left);
	tempy=parseInt(dragElement.style.top);

	dragapproved=true;
	document.onmousemove=drag_drop;
}

function creditOrder(element)
{
	var obj = document.getElementById('creditROrderNo'); 
	var obj2 = document.getElementById('credit'); 
	var creditLinkE = document.getElementById('creditLinkE'); 
	var customerName = document.getElementById('customername'); 
	var orderMemo = document.getElementById('orderMemo'); 
	
	if(element.checked) 
	{
		obj.style.display='block'; 
		obj2.value='yes';
		customerName.disabled = true;
		orderMemo.disabled = true;
		creditLinkE.focus();
	} else {
		obj.style.display='none'; 
		obj2.value='no';
		customerName.disabled = false;
		orderMemo.disabled = false;
	}
}

function getCreditLink(element)
{
	var customerName = document.getElementById('customername'); 
	var customerCode = document.getElementById('customercode'); 
	var shipto = document.getElementById('shipto'); 
	var shipto_txt = document.getElementById('shipto_txt');
	var passName = document.getElementById('passName'); 
	var orderMemo = document.getElementById('orderMemo'); 
	var creditCheck = document.getElementById('creditCheck'); 
	var obj = document.getElementById('creditROrderNo'); 
	var obj2 = document.getElementById('credit'); 

	var mode = "creditLink";


	if(element.value == "")
	{
		if(confirm("인보이스 번호가 없습니다. Credit Order를 취소하시겠습니까?"))
		{
			obj.style.display='none'; 
			obj2.value='no';
			customerName.disabled = false;
			orderMemo.disabled = false;
			creditCheck.checked = false;
		} else {
			element.focus();
		}
		return;
	}

	var xmlhttp = new XMLHttpRequest();
	var param = 'mode=' + mode + "&CID=" + <?=$CID?> + "&invno=" + element.value;
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if(xmlhttp.responseText[0] == '1')
			{ 
//alert(xmlhttp.responseText);
				var rtn = xmlhttp.responseText.split("::");
				if(rtn[1] == 'X')
				{
					alert("'"+element.value+"' 는 취소된 인보이스 번호입니다. 다시 시도바랍니다.");
					element.focus();
					return;
				} else if(rtn[1] == 'O' || rtn[1] == 'P') {
					alert("'"+element.value+"' 는 마감되지 않은 인보이스 입니다. 해당 인보이스를 바로 수정 바랍니다.");
					element.focus();
					return;
				}
				customerName.value = rtn[2];
				customerCode.value = rtn[3];
				shipto.value = rtn[4];
				shipto_txt.innerHTML = rtn[4];
				customerName.disabled = false;
				customerName.readOnly = true;
				orderMemo.disabled = false;
			} else {
				if(confirm("'"+element.value+"' 를 DB에서 찾을 수 없습니다. Credit Order를 취소하시겠습니까?"))
				{
					obj.style.display='none'; 
					obj2.value='no';
					customerName.disabled = false;
					orderMemo.disabled = false;
					creditCheck.checked = false;
				} else {
					element.focus();
				}
			}
		}
	}
	xmlhttp.open("GET","sales_order_search.php?" + param, true);
	xmlhttp.send();

	return false;
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
<?
include_once "login_check.php";

$orderNo = (int)$_GET['orderNo'];
if($orderNo)	$orderType = "old";
else			$orderType = "new";

$today = date("Y-m-d");
$creditValue = "no";
if ($customercode)	{
	$query = "SELECT CurrentBalance, cTerm, cLimit, Status, SalesPerson ".
				  "FROM Card ".
				  "WHERE CardID='$customercode' AND CardType=1 AND CID='$CID' ";
	$result = mssql_query($query);
	$row = mssql_fetch_array($result);

	$CurrentBalance = $row['CurrentBalance'];
	$cTerm = $row['cTerm'];
	$cLimit = $row['cLimit'];
	$Status = $row['Status'];
	$SalesPerson = trim(Br_iconv($row['SalesPerson']));
}

$tStatus = 'O';
if($orderType == "old") {
	$arrary = getSaleOrder($CID,$orderNo);
	$passName = $arrary[0];
	$shipto = $arrary[1];
	$orderMemo = $arrary[2];
	$customer_query = "SELECT CONVERT(char(10), a.tDate, 126) AS tDate, a.CID, b.tPassWord, a.tCust, CONVERT(char(10), a.tDeliveryDate, 126) AS tDeliveryDate ".
					  "FROM trOrderMaster a ".
					  	"LEFT JOIN trOrderDetail b ON a.tOrdNo = b.tOrdNo AND a.CID = b.CID ".
					  "WHERE a.tOrdNo = $orderNo AND a.CID = '$CID' ";
//	$customer_query = "SELECT TOP 1 CONVERT(char(10), tDate, 126) AS tDate, CID, tPassWord, tCust, CONVERT(char(10), tDeliveryDate, 126) AS tDeliveryDate ".
//					  "FROM trOrderDetail ".
//					  "WHERE tOrdNo = $orderNo AND tID = 1 AND CID = '$CID' ";
	$customer_query_result = mssql_query($customer_query);
	$customer_row = mssql_fetch_array($customer_query_result);

	$order_date = $customer_row['tDate'];
	$staffID = $customer_row['tPassWord'];
	$order_customer = $customer_row['tCust'];
	$order_deliveryDate = $customer_row['tDeliveryDate'];

	$customer_query = "SELECT tStatus, CreditInvNo, CustomerPO FROM trOrderMaster WHERE tOrdNo = $orderNo AND CID = '$CID' ";
	$customer_query_result = mssql_query($customer_query);
	$customer_row = mssql_fetch_array($customer_query_result);
	$tStatus = $customer_row['tStatus'];
	$CreditInvNo = trim($customer_row['CreditInvNo']);
	$CustomerPO = trim(Br_iconv($customer_row['CustomerPO']));
	if($CreditInvNo != "") $creditValue = "yes";
}
if($orderType == "new") {
	$order_date = $today;
	$staffID = $_SESSION['staffID'];
	$order_deliveryDate = $today;
}
?>
<table width="1024px" style="background-color:#ffffff;">
	<tr>
		<td width="150px;" class="doc_title"><b>■ Sales &gt ORDER</b></td>
		<td align="right">&nbsp;</td>
	</tr>
</table>

<form name="order_sheet" action="sales_order_update.php" method="POST" accept-charset="utf-8">
<input type="hidden" name="mode">
<input type="hidden" name="order_no" value="<?=$orderNo; ?>">
<input type="hidden" name="item_num" value=0>
<input type="hidden" name="staffID" value="<?=$staffID; ?>">
<input type="hidden" name="CID" value="<?=$CID; ?>">
<input type="hidden" name="orderNo" value="<?=$orderNo; ?>">
<input type="hidden" name="credit" id="credit" value="<?=$creditValue;?>">
<? 
if($CreditInvNo != ""){
?> 
<input type="hidden" name="creditLink" id="creditLink" value="<?=$CreditInvNo;?>">
<? 
}
?> 
<div id="orderSheet_wrap" style="margin-top:5px; width:1024px;">
	<table cellspacing=0 cellpadding=0>
		<tr>
			<td>
				<table id="customer_table" width="760px" style="border-collapse:collapse; font-size:12px;">
					<tr height="30" style="background-color:#ff6666; border:1px solid #BBBBBB; border-bottom:0;">
						<td  width="600px" colspan="3" style="font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px;">ORDER INFORMATION</td>
						<td  width="160px" style="font-size:13px; font-weight:bold; color:#FFFFFF;">
<?	
if($orderNo != "") 	
{ 	
	if($_SESSION['ActiveIP'] != 'N')
	{
?>
							<input type="button" name="print" value="ORDER FORM PRINT" onclick="return print_packing_list(event, '<?=$tStatus?>', '<?=$CID?>', '<?=$orderNo?>')">
<?	
	}	
}
?>
						</td>
					</tr>
					<tr height="25" style="background-color:#ffffff; border:1px solid #BBBBBB; border-bottom:0;">
						<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Order No:</b></td>
						<td>
							<table style="border-collapse:collapse; font-size:13px;">
							<tr>
								<td width="90px"> <?=(($orderType == "new") ? '' : $orderNo); ?> </td>
<? 
if($orderType == "new")
{
	if($customername == ""){
?> 
								<td class="doc_field_r"> <b>Credit Order? :</b> </td>
								<td width="20px"> <input id="creditCheck" type="checkbox" onclick="creditOrder(this);"> </td>
								<td class="doc_field_r"> 
									<div id="creditROrderNo" style="display:none">
										 <b>Releated Invoice# :</b> <input type="text" id="creditLinkE" name="creditLinkE" style="font-family:'arial'; font-size:12px; width:65px;" onBlur="return getCreditLink(this);">
									</div>
								</td>
<? 
	}
} else if($CreditInvNo != ""){
?> 

								<td class="doc_field_r"> <font color="blue">Credit Order of Invoice# <b>"<?=$CreditInvNo?>"</b></font> </td>
<? 
}
?> 
							</tr>
							</table>
						</td>
						<td width="120px" class="doc_field_r" style="padding-right:5px;"><b>Order Date:</b></td>
						<td><?=$order_date; ?>&nbsp;</td>
					</tr>
					<tr height="23" style="background-color:#ffffff; border-left:1px solid #BBBBBB; border-right:1px solid #BBBBBB;">
						<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Customer: </b></td>
						<td width="393px">
			<?  if($tStatus == "O" || $orderType == "new") { ?>
							<input style="background-color: #e2e2e2;" class="doc_field_270" id="customername" name="customername" type="text" value="<?=$customername;?>" onKeyPress="if (event.keyCode==13){ search_customer(); event.returnValue=false}"/><input class="doc_field_80" id="customercode" name="customercode" type="text" value="<?=$customercode?>" readonly/>
			<?  } else { ?>
							<input id="customername" type="hidden" value="<?=$customername;?>" /><input id="customercode" type="hidden" value="<?=$customercode?>"/>
							<?=$customername;?> (<?=$customercode?>)
			<?  } ?>
						</td>
						<td width="120px" class="doc_field_r" style="padding-right:5px;"><b>Delivery Date:</b></td>
						<td width="157px">
			<?  if($tStatus == "O" || $orderType == "new") { ?>
							<input size="12" type="text" style="background-color:#e2e2e2;" id="delivery_date" name="delivery_date" value="<?=$order_deliveryDate?>" onClick="datePicker(event,'delivery_date')" onkeydown="datePicker()"  required >
			<?  } else { ?>
							<?=$order_deliveryDate?>
			<?  } ?>
						</td>
					</tr>
					<tr height="23" style="background-color:#ffffff; border-left:1px solid #BBBBBB; border-right:1px solid #BBBBBB;">
						<td width="90px" class="doc_field_r" style="padding-right:5px;"><b>Ship To: </b></td>

						<td width="393px">
			<?	if($customercode != "") { 
					$Query_shipto = "SELECT * FROM ShipTo WHERE CID='".$CID."' AND CardID=".$customercode;
					$rst = mssql_query($Query_shipto);
			
					if($tStatus == "O" || $orderType == "new") { 
			?>
							<select id="shipto" name="shipto" style="vertical-align: middle; font-family:'arial'; font-size: 12px; width:352px;">
							  <option value=""> 선 택 </option>
			<?			while($row = mssql_fetch_array($rst)) { ?>
							  <option value="<?=Br_iconv($row['shipto'])?>" <? if($shipto == Br_iconv($row['shipto'])) { echo "selected"; }?>><?=Br_iconv($row['shipto'])?></option> 
			<?			}	?>
							</select>		
			<?		} else { 
							  Br_iconv($row['shipto']);
					}	?>			
			<?	} else {//if($customercode != "")?>			
							<div id="shipto_txt"></div>
							<input type="hidden" id="shipto" name="shipto" value=""/>
			<?	} //if($customercode != "")	?>			
						</td>
						<td width="120px" class="doc_field_r" style="padding-right:5px;"><b>Sales Person:</b></td>
						<td>
			<?		$Query_sales = "SELECT * FROM mfPassWord WHERE CID = '".$CID."' AND SalesYN = 1";
					$rst2 = mssql_query($Query_sales);
					if($tStatus == "O" || $orderType == "new") { 
			?>
							<select id="passName" name="passName" style="vertical-align: middle; font-family:'arial'; font-size: 12px; width: 117px;">
							  <option value=""> 선 택 </option>
			<?				while($row2 = mssql_fetch_array($rst2)) { ?>
							  <option value="<?=Br_iconv($row2['passName'])?>" <? if($passName == Br_iconv($row2['passName'])) { echo "selected"; }?>><?=Br_iconv($row2['passName'])?></option> 
			<?				}	?>
							</select>		
			<?		} else { 
							  echo $passName;
					}	?>			
						</td>
					</tr>
					<tr height="45px" valign="top" style="background-color:#ffffff; border:1px solid #BBBBBB; border-top:0;">
						<td width="90px" class="doc_field_r" style="padding-right:5px; padding-top:3px; vertical-align:top;"><b>Memo:</b></td>
						<td width="393px">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>
			<?  if($tStatus == "O" || $orderType == "new") { ?>
										<textarea style="background-color:#e2e2e2; height:36px; width:390px; overflow:auto;" id="orderMemo" name="orderMemo" type="text" maxlength="100" onchange="show_button()"><?=$orderMemo?></textarea>
			<?  } else { ?>
										<?=$orderMemo?>
			<?  } ?>
									</td>
								</tr>
							</table>
						</td>
						<td width="120px" class="doc_field_r" style="padding-right:5px; padding-top:3px; vertical-align:top;"><b>CustomerPO:</b></td>
						<td>
			<?  if($tStatus == "O" || $orderType == "new") { ?>
							<input size="12" type="text" style="background-color:#e2e2e2;" name="CustomerPO" value="<?=$CustomerPO?>" onchange="show_button()">
			<?  } else { ?>
							<?=$CustomerPO?>
							<input type="hidden" name="CustomerPO" value="<?=$CustomerPO?>">
			<?  } ?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table id="customer_info" width="263px" style="border-collapse:collapse; board:1px font-size:12px;" height="147px">
					<tr height="30" style="background-color:#ff6666; border:1px solid #BBBBBB; border-bottom:0;">
						<td  align="center" colspan="2" style="font-size:13px; font-weight:bold; color:#FFFFFF;">CUSTOMER INFORMATION</td>
					</tr>
					<tr style="border:1px solid #BBBBBB; border-top:0; border-left:0; border-bottom:0; border-right:1;">
						<td style="padding-left:20px" class="doc_field_l">Balance:</td>
						<td style="padding-right:20px" class="doc_field_r">
<?			    if($_SESSION['ActiveIP'] != 'N')
				{
?>
							<b><?=number_format($CurrentBalance,2)?></b>
<?			    } ?>
						</td>
					</tr>
					<tr style="background-color:#FFFFFF; border:1px solid #BBBBBB; border-top:0; border-left:0; border-bottom:0; border-right:1;">
						<td style="padding-left:20px" class="doc_field_l">Term:</td>
						<td style="padding-right:20px" class="doc_field_r"><b><?=$cTerm?></b></td>
					</tr>
					<tr style="border:1px solid #BBBBBB; border-top:0; border-left:0; border-bottom:0; border-right:1;">
						<td style="padding-left:20px" class="doc_field_l">Limit:</td>
						<td style="padding-right:20px" class="doc_field_r"><b><? if($cLimit > 0) { echo number_format($cLimit,2); }?></b></td>
					</tr>
					<tr style="background-color:#FFFFFF; border:1px solid #BBBBBB; border-top:0; border-left:0; border-bottom:0; border-right:1;">
						<td style="padding-left:20px" class="doc_field_l">Sales Person:</td>
						<td style="padding-right:20px" class="doc_field_r"><b><?=$SalesPerson?></b></td>
					</tr>
					<tr style="border:1px solid #BBBBBB; border-top:0; border-left:0; border-bottom:1; border-right:1;">
						<td style="padding-left:20px" class="doc_field_l">Status:</td>
						<td style="padding-right:20px;" class="doc_field_r"><b><?=$Status?></b></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table id="order_table_wrap" width="100%" style="border-collapse:collapse; margin-top:10px;" cellspacing=0 cellpadding=0>
		<tr height="30" style="border:1px solid #BBBBBB;">
			<td width="540px" align="center" style="background-color:#808080; font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px; border-right:1px solid #BBBBBB;">TOTAL ($)</td>
			<td width="80px" align="right" style="padding-right:5px;">0.00</td>
			<td width="29px" style="background-color:#808080; border-left:1px solid #BBBBBB;"></td>
			<td bgcolor="#808080">
				<div id="complete_btn" style="background-color:#808080; margin-top:0px; float:right; display:none;">
					<? if($orderType == "old") { 
							if($tStatus == "O")
							{
					?>
						<input type="button" value="삭제" onClick="proceed_submit('delete')" style="width:70px; height:30px; font-size:12px; font-weight:bold;">
						<input type="button" value="수정" onClick="proceed_submit('modify')" style="width:70px; height:30px; font-size:12px; font-weight:bold;">
					<?		}
					   } else { ?>
						<input type="button" value="완료" onClick="proceed_submit('add')" style="width:70px; height:30px; font-size:12px; font-weight:bold;">
					<? } ?>
				</div>
			</td>
		</tr>

		<tr height="30" style="background-color:#c0c0c0; border:1px solid #BBBBBB; border-bottom:0;">
			<td style="font-size:13px; font-weight:bold; color:#FFFFFF; padding-left:30px;">ORDER ITEM LIST</td>
			<td colspan=3 align="right" style="padding-right:5px;">
<?  if($tStatus == "O") { ?>
				<input type="button" id="item_list_btn" value="Item List" onClick="toggle_item_list()" />
<?	} ?>
			</td>
		</tr>
		<tr style="border:1px solid #BBBBBB;">
			<td colspan="4"> <div id="order_table_div" style="width:1024; overflow-y:scroll; overflow-x:hidde;"></div> </td>
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

<div id="item_list_div" style="position:absolute; left:645px; top:259px; border:2px solid #666666; width:700px; height:455px; display:none; background-color:#ffffff;"><!-- onMousedown="initializedrag(event)"> -->
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#808080;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="letter-spacing:-1px; font-weight:bold; color:#FFFFFF;">
							<div id="for_drag" onMousedown="initializedrag(event,document.getElementById('item_list_div'))">
							상품검색
							</div>
						</td>
						<!--<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="toggle_item_list()"></td>-->
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
						<input id="search_item_key" name="search_item_key" style="width:250px; margin:6px 18px 0px 10px; display:none;" placeholder="검색" onKeyPress="if (event.keyCode==13){ get_item_list('<?=$tStatus?>', this.value, '<?=$CID?>'); event.returnValue=false}"/>
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
<div id="history_item_list_div" style="position:absolute; left:645px; top:310px; border:2px solid #666666; width:470; height:455px; display:none; background-color:#ffffff;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#808080;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="letter-spacing:-1px; font-weight:bold; color:#FFFFFF;">
							<div id="for_drag2" onMousedown="initializedrag(event,document.getElementById('history_item_list_div'))">HISTORY 검색(최근 1년)</div>
						</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="history_item_list()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div id="css_tabs2">
					<input id="ctab1" type="radio" name="tab2" value="this_cust" checked="checked" />
					<input id="ctab2" type="radio" name="tab2" value="all_cust" />
					<label for="ctab1">This Cust</label>
					<label for="ctab2">All Cust</label>
					<div class="tab1_content">
						<div id="this_cust_history" style="height:397px; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab2_content">
						<div id="all_cust_history" style="height:397px; overflow-y:scroll; overflow-x:hidden"></div>
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
echo "<script>select_old_customer('".$tStatus."','".$customercode."','".$customername."','".$CID."')</script>";

if($orderType == "old") {
	$item_query = "SELECT wsCode, ProdOwnCode, tID, tProd, tQty, tOUprice, tPunit, prodSize, tMemo ".
				  "FROM trOrderDetail ".
				  "WHERE tOrdNo = $orderNo AND CID = '$CID' ".
				  "ORDER BY tID DESC";
	$item_query_result = mssql_query($item_query);

	$html = "<table id='order_table' width='100%' style='border-collapse:collapse; border: 1px solid #BBB'>".
				"<tr height='25'>".
					"<td width='20px' class='order_table_head'>SEQ</th>".
					"<td width='105px' class='order_table_head'>CODE</th>".
					"<td width='35px' class='order_table_head'>O/CD</th>".
					"<td width='100px' class='order_table_head'>BARCODE</th>".
					"<td class='order_table_head'>ITEM NAME</th>".
					"<td width='100px' class='order_table_head'>SIZE</th>".
					"<td width='75px' class='order_table_head'>PRICE</th>".
					"<td width='40px' class='order_table_head'>QTY</th>".
					"<td width='85px' class='order_table_head'>SUM</th>".
					"<td width='140px' class='order_table_head'>MEMO</th>".
					"<td width='30px' class='order_table_head'>DEL</th>".
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
		$memo = Br_iconv($item_row['tMemo']);
		
		$array = getItemName($wsCode,$ProdOwnCode,$CID);

		if(trim($array[1]) !="") {
			$prodName = $array[0]." / ".$array[1];
		} else {
			$prodName = $array[0];
		}
		
		$qty = $item_row['tQty'];
		// 외부 접속에 여부에 따른 정보 표시 제한
		if($_SESSION['ActiveIP'] != 'N')
		{
			$price = 0;
			if($item_row['tOUprice'] != "" || isset($item_row['tOUprice']))
			{
				$price = $item_row['tOUprice'];
				$hiddenPrice = $price;
			}	
		} else {
			$price = 0;
			$hiddenPrice = 0;
		}	

		if($tStatus == 'O')
			$disable = "";
		else
			$disable = "disabled";

		// 상태에 따른 정보 표시 구분
		$priceHtml = "<input name='order_price[]' style='width:75px; text-align:right;' type='text' value='".number_format($hiddenPrice,2)."' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 6, $i)' onchange='show_button()' $disable required/>";
		$qtyHtml = "<input name='order_qty[]' style='width:40px; text-align:right;' type='text' value='$qty' onkeydown='return onlyNumber(event)' onblur='check_value(this.value, 7, $i)' onchange='show_button()' $disable required/>";
		$memoHtml = "<input name='order_memo[]' style='width:140px; text-align:left;' type='text' value='$memo' maxlength='50' onchange='show_button()' $disable required/>";
		$delHtml = "";
		if($tStatus == 'O')
			$delHtml = "<img src='css/img/bt_cancel.gif' width='20px' height='20px' style='cursor:pointer;' onClick='delete_item($i)'>";

		$omemo = Br_iconv($item_row['tMemo']);
		if($today > $order_deliveryDate) {
			echo "<script>set_deliveryDate('".$order_deliveryDate."')</script>";
		}
		/* // 100개가 넘어가는 오더일 경우 불러오는데 시간이 걸림.
		echo "<script>select_item('".$tStatus."','".$wsCode."','".$ProdOwnCode."','".$prodID."','".$prodName."', '".$array[3]."','".$price."','".$qty."','".$omemo."')</script>";
		 */
		if($price == 0) $priceBgcolor = "red";
		else $priceBgcolor = "";
		if($qty == 0) $qtyBgcolor = "red";
		else $qtyBgcolor = "";
		
		if($tStatus != 'O')
		{
			$priceBgcolor = "";
			$qtyBgcolor = "";
		}

		$j = $i - 1;

		$html .= "<tr height='25'>".
		"<td align='center'> $num_item </td>".
		"<td align='left' style='padding-left:5px'> $wsCode <input name='order_wscode[]' type='hidden' value='$wsCode' /> </td>".
		"<td align='center'> $ProdOwnCode <input name='order_prodowncode[]' type='hidden' value='$ProdOwnCode' /> </td>".
		"<td align='left' style='padding-left:5px'> $prodID <input type='hidden' name='order_item[]' value='$prodID'></td>".
		"<td align='left' style='padding-left:5px'> <a href='javascript:get_history(\\\"$wsCode\\\",\\\"$ProdOwnCode\\\",$j)' style='text-decoration:none; color:black'> $prodName </a> </td>".
		"<td align='left' style='padding-left:5px'> $array[3] </td>".
		"<td align='center' style='background-color:$priceBgcolor'> $priceHtml </td>".
		"<td align='center' style='background-color:$qtyBgcolor'> $qtyHtml </td>".
		"<td align='right' style='padding-right:5px'>".number_format($qty * $price,2)."</td>".
		"<td align='center'> $memoHtml </td>".
		"<td align='center'> $delHtml </td>".
	"</tr>";

		$num_item--;
	}
	$html .= " </table> ";
	echo "<script>document.getElementById('order_table_div').innerHTML = \"$html\";</script>";
	echo "<script>calculate_sum2(".$i.")</script>";
}
else
{
	$html = "<table id='order_table' width='100%' style='border-collapse:collapse; border: 1px solid #BBB'>".
			   	"<tr height='25'>".
					"<td width='20px' class='order_table_head'>SEQ</th>".
					"<td width='105px' class='order_table_head'>CODE</th>".
					"<td width='35px' class='order_table_head'>O/CD</th>".
					"<td width='100px' class='order_table_head'>BARCODE</th>".
					"<td class='order_table_head'>ITEM NAME</th>".
					"<td width='100px' class='order_table_head'>SIZE</th>".
					"<td width='75px' class='order_table_head'>PRICE</th>".
					"<td width='40px' class='order_table_head'>QTY</th>".
					"<td width='85px' class='order_table_head'>SUM</th>".
					"<td width='140px' class='order_table_head'>MEMO</th>".
					"<td width='30px' class='order_table_head'>DEL</th>".
				"</tr>".
			"</table>";
	echo "<script>document.getElementById('order_table_div').innerHTML = \"$html\";</script>";
	// item_num 정보 초기화
	echo "<script>$('input[name=item_num]').val(0);</script>";
}
// mssql_close();
?>
