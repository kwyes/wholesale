<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "login_check.php";
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	if($_SESSION['ActiveIP'] == 'N')
	{
	?>
	<script>
		alert("승인되지 않은 접근입니다.");
		window.history.back();
	</script>
	<?
		return;
	}
	if ($_SESSION['staffDID'] != "04"  && $_SESSION['staffDID'] != "07")
	{
	?>
		<script>
		window.alert("이 메뉴를 볼 수 있는 권한이 없습니다. ");
			window.history.back();
		</script>
	<?
	}
	$cId = $_SESSION['staffCID'];
	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	if($mode == "") $mode = "1";
	$inactive = ($_GET['inactive']) ? $_GET['inactive'] : $_POST['inactive'];
	$checked = "";
	if($inactive == "yes") $checked = "checked";

	$today = date("Y-m-d");
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">
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
    width:90px;
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
    background:#64b0b0;
    color:#fff
}

#css_tabs > label:hover {
    background:#bfdfff;
    color:#fff
}

/* 실제 내용이 담긴 div 요소 스타일 지정 */
#css_tabs .tab1_content, #css_tabs .tab2_content, #css_tabs .tab3_content {
    border:1px solid #ddd;
}
</style>
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
function toggle_item_list() {
	var div = document.getElementById("item_list_div");
	if (div.style.display !== "none") {
		div.style.display = "none";
		location.href="index.php";
	} else {
		div.style.display = "block";
	}
}
function all_item_list(mode,cId) {
	document.getElementById("searchKey").value="";
	document.getElementById("SearchName").value="Co. Name";
	document.getElementById("opt").value="Starts With";
	get_item_list(mode,cId);
}

var items_all = [];
var items_all_complete = false;
var items_all_count = 0;
var items_all_position = 0;

var items_customer_idx = [];
var items_customer_count = 0;
var items_customer_position = 0;

var items_vendor_idx = [];
var items_vendor_count = 0;
var items_vendor_position = 0;

//display_div : all_display or customer_display or vendor_display
//count : all or customer or vendor 의 count

function make_item_table(type,count,position,items_array)
{
	var rtn = "";
	var display_div;
	var table_id = "";

	switch(type)
	{
		case "1" : display_div = "customer_display"; table_id = "customer_items";break;
		case "2" : display_div = "vendor_display"; table_id = "vendor_items";break;
		default : display_div = "all_display"; table_id = "all_items";
	}
	
	//console.log("make_item_table:"+display_div+",count:"+count);
	if(count == 0){
		rtn = '<table id="'+table_id+'" width="765px" style="border-collapse:collapse; letter-spacing:-1px; font-family:verdana; font-size:13px;">' +
			  '<tr style="background-color:#64b0b0">' + 
				  '<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">NAME</td>' +
				  '<td width="70px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">CARD #</td>' + 
				  '<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">PHONE #</td>' +
				  '<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">TYPE</td>' + 
				  '<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">BALANCE</td>' +
			  '</tr>' + 
			  '<tr>' +
				  '<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>' +
  			  '</tr>' +
			  '</table>';

		document.getElementById(display_div).innerHTML = rtn;
		return;
	}

	rtn = '<table id="'+table_id+'" width="765px" style="border-collapse:collapse; letter-spacing:-1px; font-family:verdana; font-size:13px;">' +
			'<tr style="background-color:#64b0b0">' + 
				'<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">NAME</td>' +
				'<td width="70px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">CARD #</td>' + 
				'<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">PHONE #</td>' + 
				'<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">TYPE</td>' + 
				'<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">BALANCE</td>' +
			'</tr>';
	
	var tmp = "";
	var fieldName = "";
	var fields;
	var balance = 0;
	var length = 0;
	var istatus = "";
	var color = "black";

	if(position == -1) length = items_array.length;	// search ?
	else 
	{
		if(items_array.length > 50) length = 50;
		else length = items_array.length;
	}

console.log(length);

	//console.log(items_all.length);
	var j=0;
	for(var i=0; j<length; i++){

		var balanceS = "";
		
		if(i >= items_array.length) break; // i가 배열의 길이를 초과하면 break;
		if(type != "0" && items_array[i][1] != type) continue;	// customer, vendor 의 경우 해당 type확인 후 아니면 다음것을 가져옴.

		if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
		else			fieldName = "doc_field_purchases";
		j++;

		//fields = items_all[i].split(';');

		balance = parseInt(items_array[i][5]);
		istatus = items_array[i][12];
		if(istatus == '2') color = "gray";
		else color = "black";
		
		if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
		rtn +=  '<tr class="' + fieldName + '">' +
				'<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;">' + 
					'<a href="cardedit.php?cardId='+items_array[i][0]+'&CardType='+items_array[i][1]+'" target="cardfile2" style="color:'+color+'; text-decoration:none;">'+items_array[i][3]+'</a></td>' + 
				'<td align="center" style="border:1px solid #BBBBBB; border-right:0;">' + 
					'<a href="cardedit.php?cardId='+items_array[i][0]+'&CardType='+items_array[i][1]+'" target="cardfile2" style="color:'+color+'; text-decoration:none;">'+items_array[i][0]+'</a></td>' + 
				'<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:'+color+'">'+items_array[i][2]+'</td>' + 
				'<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:'+color+'">'+items_array[i][4]+'</td>' + 
				'<td align="right" style="border:1px solid #BBBBBB; border-right:1; padding:0 5px 0 0; color:'+color+'">' + balanceS +'</td>' +
				'</tr>';
	}

	rtn += '</table>';

	document.getElementById(display_div).innerHTML = rtn;

	return length;
}

function add_items_all_table()
{
	var length = 0;
	var istatus = "";
	var color = "black";

	if(items_all_count > items_all_position + 50) length = 50;
	else length = items_all_count - items_all_position;

console.log(" count2:"+items_all_count+" position2:"+items_all_position+" length:"+length);

	var j = 0;
	var table = document.getElementById("all_items");
	var balance = 0;
	var balanceS = "";
		
	for(var i=items_all_position; i<length+items_all_position; i++)
	{
		if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
		else			fieldName = "doc_field_purchases";
		j++;

		istatus = items_all[i][12];
		if(istatus == '2') color = "gray";
		else color = "black";

		var row = table.insertRow(-1);
		row.className = fieldName;

		var cell0 = row.insertCell(0);
		var cell1 = row.insertCell(1);
		var cell2 = row.insertCell(2);
		var cell3 = row.insertCell(3);
		var cell4 = row.insertCell(4);

		balanceS = "";
		balance = parseInt(items_all[i][5]);
		if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');

		cell0.style.textAlign = "left";
		cell0.style.border = "1px solid #BBBBBB";
		cell0.style.borderRight = "0";
		cell0.innerHTML = '<a href="cardedit.php?cardId='+items_all[i][0]+'&CardType='+items_all[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_all[i][3]+'</a></td>';

		cell1.style.textAlign = "center";
		cell1.style.border = "1px solid #BBBBBB";
		cell1.style.borderRight = "0";
		cell1.innerHTML = '<a href="cardedit.php?cardId='+items_all[i][0]+'&CardType='+items_all[i][1]+'" target="cardfile" style="color:'+color+'; text-decoration:none;">'+items_all[i][0]+'</a></td>';

		cell2.style.textAlign = "center";
		cell2.style.border = "1px solid #BBBBBB";
		cell2.style.borderRight = "0";
		cell2.style.color = color;
		cell2.innerHTML = items_all[i][2];

		cell3.style.textAlign = "center";
		cell3.style.border = "1px solid #BBBBBB";
		cell3.style.borderRight = "0";
		cell3.style.color = color;
		cell3.innerHTML = items_all[i][4];

		cell4.style.textAlign = "right";
		cell4.style.border = "1px solid #BBBBBB";
		cell4.style.borderRight = "1";
		cell4.style.color = color;
		cell4.innerHTML = balanceS;
	}
	items_all_position += length;
}

function add_items_customer_table()
{
	var length = 0;

	if(items_customer_count > items_customer_position + 50) length = 50;
	else length = items_customer_count - items_customer_position;

console.log(" count2:"+items_customer_count+" position2:"+items_customer_position+" length:"+length);

	var j = 0;
	var table = document.getElementById("customer_items");
	var balance = 0;
	var balanceS = "";
		
	for(var i=items_customer_position; i<length+items_customer_position; i++)
	{
		if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
		else			fieldName = "doc_field_purchases";
		j++;

		var row = table.insertRow(-1);
		row.className = fieldName;

		var cell0 = row.insertCell(0);
		var cell1 = row.insertCell(1);
		var cell2 = row.insertCell(2);
		var cell3 = row.insertCell(3);
		var cell4 = row.insertCell(4);

		balanceS = "";
		balance = parseInt(items_all[items_customer_idx[i]][5]);
		if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');

		cell0.style.textAlign = "left";
		cell0.style.border = "1px solid #BBBBBB";
		cell0.style.borderRight = "0";
		cell0.innerHTML = '<a href="cardedit.php?cardId='+items_all[items_customer_idx[i]][0]+'&CardType='+items_all[items_customer_idx[i]][1]+'" target="cardfile" style="color:#000000; text-decoration:none;">'+items_all[items_customer_idx[i]][3]+'</a></td>';

		cell1.style.textAlign = "center";
		cell1.style.border = "1px solid #BBBBBB";
		cell1.style.borderRight = "0";
		cell1.innerHTML = '<a href="cardedit.php?cardId='+items_all[items_customer_idx[i]][0]+'&CardType='+items_all[items_customer_idx[i]][1]+'" target="cardfile" style="color:#000000; text-decoration:none;">'+items_all[items_customer_idx[i]][0]+'</a></td>';

		cell2.style.textAlign = "center";
		cell2.style.border = "1px solid #BBBBBB";
		cell2.style.borderRight = "0";
		cell2.innerHTML = items_all[items_customer_idx[i]][2];

		cell3.style.textAlign = "center";
		cell3.style.border = "1px solid #BBBBBB";
		cell3.style.borderRight = "0";
		cell3.innerHTML = items_all[items_customer_idx[i]][4];

		cell4.style.textAlign = "right";
		cell4.style.border = "1px solid #BBBBBB";
		cell4.style.borderRight = "1";
		cell4.innerHTML = balanceS;
	}
	items_customer_position += length;
}

function add_items_vendor_table()
{
	var length = 0;

	if(items_vendor_count > items_vendor_position + 50) length = 50;
	else length = items_vendor_count - items_vendor_position;

console.log(" count2:"+items_vendor_count+" position2:"+items_vendor_position+" length:"+length);

	var j = 0;
	var table = document.getElementById("vendor_items");
	var balance = 0;
	var balanceS = "";
		
	for(var i=items_vendor_position; i<length+items_vendor_position; i++)
	{
		if (j % 2 == 0)	fieldName = "doc_field_purchases_bg";
		else			fieldName = "doc_field_purchases";
		j++;

		var row = table.insertRow(-1);
		row.className = fieldName;

		var cell0 = row.insertCell(0);
		var cell1 = row.insertCell(1);
		var cell2 = row.insertCell(2);
		var cell3 = row.insertCell(3);
		var cell4 = row.insertCell(4);
		var ii = parseInt(items_vendor_idx[i]);

		balanceS = "";
		balance = parseInt(items_all[items_vendor_idx[i]][5]);
		if(balance != 0) balanceS = balance.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,'$1,');
		cell0.style.textAlign = "left";
		cell0.style.border = "1px solid #BBBBBB";
		cell0.style.borderRight = "0";
		cell0.innerHTML = '<a href="cardedit.php?cardId='+items_all[items_vendor_idx[i]][0]+'&CardType='+items_all[items_vendor_idx[i]][1]+'" target="cardfile" style="color:#000000; text-decoration:none;">'+items_all[items_vendor_idx[i]][3]+'</a></td>';

		cell1.style.textAlign = "center";
		cell1.style.border = "1px solid #BBBBBB";
		cell1.style.borderRight = "0";
		cell1.innerHTML = '<a href="cardedit.php?cardId='+items_all[items_vendor_idx[i]][0]+'&CardType='+items_all[items_vendor_idx[i]][1]+'" target="cardfile" style="color:#000000; text-decoration:none;">'+items_all[items_vendor_idx[i]][0]+'</a></td>';

		cell2.style.textAlign = "center";
		cell2.style.border = "1px solid #BBBBBB";
		cell2.style.borderRight = "0";
		cell2.innerHTML = items_all[items_vendor_idx[i]][2];

		cell3.style.textAlign = "center";
		cell3.style.border = "1px solid #BBBBBB";
		cell3.style.borderRight = "0";
		cell3.innerHTML = items_all[items_vendor_idx[i]][4];

		cell4.style.textAlign = "right";
		cell4.style.border = "1px solid #BBBBBB";
		cell4.style.borderRight = "1";
		cell4.innerHTML = balanceS;
	}
	items_vendor_position += length;
}

function get_item_list(str, cId) {
	var items = [];
	var current_disp_element;
	document.getElementById("mode").value = str;

	var mode = document.querySelector('input[name = "tab"]:checked').value;
	div_id = mode + "_display";

console.log(str+" "+mode);	//alert(div_id);

	if(mode == "all")
	{
		current_disp_element = document.getElementById("all_display");
		current_disp_element.onscroll = function(){
			if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
				add_items_all_table();
			}
		};
	}
	else if(mode == "customer")
	{
		current_disp_element = document.getElementById("customer_display");
		current_disp_element.onscroll = function(){
			if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
				add_items_customer_table();
			}
		};
	}
	else if(mode == "vendor")
	{
		current_disp_element = document.getElementById("vendor_display");
		current_disp_element.onscroll = function(){
			if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
				add_items_vendor_table();
			}
		};
	}

	if(items_all_complete)
	{
console.log("get_item_list("+str+","+cId+") 3 mode:"+mode+" str:"+str+" "+items_all_complete+" items_all.length:"+items_all.length+" items_all_count:"+items_all_count);
		if(mode == "customer")
		{
			document.getElementById("customer_display").innerHTML = "";
			items_customer_position = make_item_table("1",items_all_count,items_all_position,items_all);
			document.getElementById('found').innerHTML = items_customer_count;
		}
		else if(mode == "vendor")
		{
			document.getElementById("vendor_display").innerHTML = "";
			items_vendor_position = make_item_table("2",items_all_count,items_all_position,items_all);
			document.getElementById('found').innerHTML = items_vendor_count;
		}
		else
		{
			document.getElementById("all_display").innerHTML = "";
			items_all_position = make_item_table("0",items_all_count,items_all_position,items_all);
			document.getElementById('found').innerHTML = items_all_count;
		}
		return;
	}

	if(items_all.length == 0) {
		itemLength = 0;
	}
	else {
		itemLength = items_all.length - 1;
		//clearInterval(tmr);
	}

	var xmlhttp = new XMLHttpRequest();
	var param = 'mode=' + mode + "&CID=" + cId + "&rowNum=" + itemLength + "&inactive=<?=$inactive?>";
	var items_tmp = [];
	var oneRow = [];

	try{
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				document.getElementById("loading_image").style.display = "none";

				if(items_all.length == 0){

					//items_all = xmlhttp.responseText.split('/');
					items_tmp = xmlhttp.responseText.split('::');
					//console.log(items_tmp);
					items_all_count = parseInt(items_tmp[0]);

					for(var i = 1; i < items_tmp.length; i++)
					{
						oneRow = items_tmp[i].split(';');
						items_all.push(oneRow);
						if(oneRow[1] == '1') // if customer?
						{
							items_customer_idx[items_customer_count++] = i-1;
						}

						if(oneRow[1] == '2') // if vendor?
						{
							items_vendor_idx[items_vendor_count++] = i-1;
						}
					}
					items_all_complete = true;
					document.getElementById("found").innerHTML = items_all_count;
				}

console.log("2 mode:"+mode+" "+items_all_complete+" items_all.length:"+items_all.length+" items_all_count:"+items_all_count);
				items_all_position = make_item_table("0",items_all_count,items_all_position,items_all);
			}
		}
	} catch(e) {
		document.getElementById("loading_image").style.display = "none";
		alert("서버와 통신에 문제가 있습니다. 관리자에게 문의 바랍니다. "+e.message)
	}

	xmlhttp.open("GET","cardfile_search.php?" + param, true);
	xmlhttp.send();

	document.getElementById("loading_image").style.display = "block";
}

function search()
{
	var SearchName = document.getElementById("SearchName").value;
	var opt = document.getElementById("opt").value;
	var searchKey = document.getElementById("searchKey").value;
	var mode = document.querySelector('input[name = "tab"]:checked').value;
	var temp = [];
	var idx = 3;
	var display = "";
	var pos = 0;

	if(mode == "all")
	{
		document.getElementById("all_display").onscroll = null;
		display = "0";
	}
	else if(mode == "customer")
	{
		document.getElementById("customer_display").onscroll = null;
		display = "1";
	}
	else if(mode == "vendor")
	{
		document.getElementById("vendor_display").onscroll = null;
		display = "2";
	}

	switch(SearchName){
		case "Co. Name": idx = 3; break;
		case "Card ID": idx = 0; break;
		case "Phone Number": idx = 2; break;
		case "Email": idx = 6; break;
		case "Address": idx = 7; break;
		case "City": idx = 8; break;
		case "Province": idx = 9; break;
		case "Postal Code": idx = 10; break;
		case "Country": idx = 11; break;
		default: idx = 3;
	}

	//console.log("SearchName:"+SearchName+" Opt:"+opt+" searchKey:"+searchKey);

	if(searchKey == "")
	{
console.log("search mode:"+mode+" "+items_all_complete+" items_all.length:"+items_all.length+" items_all_count:"+items_all_count);
		if(mode == "all")
		{
			document.getElementById("found").innerHTML = items_all_count;
			current_disp_element = document.getElementById("all_display");
			current_disp_element.onscroll = function(){
				if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
					add_items_all_table();
				}
			};
		}
		else if(mode == "customer")
		{
			document.getElementById("found").innerHTML = items_customer_count;
			current_disp_element = document.getElementById("customer_display");
			current_disp_element.onscroll = function(){
				if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
					add_items_customer_table();
				}
			};
		}
		else if(mode == "vendor")
		{
			document.getElementById("found").innerHTML = items_vendor_count;
			current_disp_element = document.getElementById("vendor_display");
			current_disp_element.onscroll = function(){
				if(current_disp_element.offsetHeight + current_disp_element.scrollTop >= current_disp_element.scrollHeight){
					add_items_vendor_table();
				}
			};
		}
		items_all_position = make_item_table("0",items_all_count,items_all_position,items_all);
		return;
	}
	//CardID,CardType,Phone1,Name,cType,CurrentBalance,Email,Address,City,Province,PostalCode,Country

	for(var i = 0; i < items_all.length; i++)
	{
		if(items_all[i][idx] == null) continue;
		pos = items_all[i][idx].toLowerCase().indexOf(searchKey.toLowerCase())
		if(pos >= 0)
		{
			if(opt == "Starts With" && pos != 0) continue;	
			if(display != "0")
				if(display != items_all[i][1]) continue;
			temp.push(items_all[i]);
			//console.log("temp.length:"+temp.length);
		}
	}

	document.getElementById("found").innerHTML = temp.length;
	make_item_table(display,temp.length,-1,temp);
}

function append(str) {
	newWindow=window.open("cardedit.php?new="+str, "cardedit", "");	//자식창 OPEN
}

function update_db() {
	var inactive = "";

	if(document.getElementById("inactive").checked)
		inactive = "yes";

	window.location.href="?inactive="+inactive;
}

function setInit()
{
	var disp_div = document.getElementById("item_list_div");
	var disp_all = document.getElementById("all_display");
	var disp_customer = document.getElementById("customer_display");
	var disp_vendor = document.getElementById("vendor_display");

	//console.log("setHeight1 ## disp_div.style.height:"+disp_div.style.height+" disp_inventory.style.height:"+disp_inventory.style.height);

	disp_div.style.height = (window.innerHeight - 80) + "px";
	disp_all.style.height = (window.innerHeight - 183) + "px";
	disp_customer.style.height = (window.innerHeight - 183) + "px";
	disp_vendor.style.height = (window.innerHeight - 183) + "px";

	//console.log("setHeight2 ## disp_div.style.height:"+disp_div.style.height+" disp_inventory.style.height:"+disp_inventory.style.height);
}
</script>

</head>
<!-- <body onLoad="get_item_list(<?=$mode?>, <?=$cId?>); tmr = setInterval(get_item_list, 500, <?=$mode?>, <?=$cId?>);"> -->
<body onLoad="setInit(); get_item_list(<?=$mode?>, <?=$cId?>);">
<?
include_once "includes/header.html";
include_once "includes/menu.html";
?>
<input type="hidden" name="mode" id="mode">
<div id="item_list_div" style="position:absolute; left:8px; top:67px; border:2px solid <?=$bgcolor?>; width:1020px; height:88%; background-color:#ffffff; overflow-y:hidden; overflow-x:hidden;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="middle" style="padding:0 0 0 20px; background-color:#cad8ff;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="letter-spacing:-1px; font-weight:bold;">CARD FILE</td>
						<td width="22" align="left"><img style="cursor:pointer;" src="css/img/bt_closelayer.gif" alt="Close" height="19" width="19" onClick="toggle_item_list()"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div id="css_tabs">
					<input id="tab1" type="radio" name="tab" value="all" checked="checked" onClick="get_item_list('0','<?=$cId?>')"/>
					<input id="tab2" type="radio" name="tab" value="customer" onClick="get_item_list('1',<?=$cId?>)"/>
					<input id="tab3" type="radio" name="tab" value="vendor" onClick="get_item_list('2',<?=$cId?>)"/>
					<label for="tab1">All Cards</label>
					<label for="tab2">Customer</label>
					<label for="tab3">Vendor</label>
					<div>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#c0c0c0;">
							<tr height="30">
								<td width="110px" class="doc_field_r">Search by&nbsp;&nbsp;</td>
								<td width="121px">
									<select id="SearchName" name="SearchName">
									  <option value="Co. Name" <? echo 'Selected'; ?>>Co. Name</option>
									  <option value="Card ID">Card ID</option> 
									  <option value="Phone Number">Phone Number</option> 
									  <option value="Email">Email</option> 
									  <option value="Address">Address</option> 
									  <option value="City">City</option> 
									  <option value="Province">Province</option> 
									  <option value="Postal Code">Postal Code</option> 
									  <option value="Country">Country</option> 
									</select>		
								</td>
								<td width="100px">
									<select id="opt" name="opt">
									  <option value="Contains" 'Selected'>Contains</option>
									  <option value="Starts With">Starts With</option> 
									</select>		
								</td>
								<td><input style="width:175px;" class="doc_field_l" id="searchKey" name="searchKey" type="text" value="<?=$searchKey?>" onKeyPress="if (event.keyCode==13){ search(); event.returnValue=true}"/></td>
								<td align="left" width="50px">&nbsp;<img style="cursor:pointer;" src="css/img/bt_closelayer.gif" onClick="all_item_list('<?=$mode?>','<?=$cId?>')"></td>
								<td align="left" width="75px"><input style="font-size:9pt;height:25" type="button" value="SEARCH" class="btn_style" onClick="return search();"/></td>
								<td align="left" width="150px"><input style="font-size:9pt;height:25" type="button" value="&nbsp;&nbsp; NEW &nbsp;&nbsp;" class="btn_style" onClick="return append('yes');"/></td>
								<td align="left" width="240px" class="doc_field_l"><input type="checkbox" id="inactive" onClick="update_db()" <?=$checked?> /><span style="position: relative;bottom: 2px;">Show Inactive items</span></td>
							</tr>
							<tr>
								<td width="110px" class="doc_field_r"># Found:&nbsp;&nbsp;</td>
								<td align="left"><span style="width:150px;" class="doc_field_l" id="found"></span></td>
							</tr>
						</table>
					</div>
					<div class="tab1_content">
						<div id="all_display" style="height:80%; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab2_content">
						<div id="customer_display" style="height:80%; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div class="tab3_content">
						<div id="vendor_display" style="height:80%; overflow-y:scroll; overflow-x:hidden"></div>
					</div>
					<div id="loading_image" style="position:absolute; left:450px; top:150px; display:none; widht:100px height:100px margin-top:10px; text-align:center;"><img src="css/img/ajax-loader.gif"></br>자료 처리 중입니다.</div>
				</div>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
