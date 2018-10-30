<?
include_once "includes/db_configms.php";
include_once "includes/common_class.php";

function order_delete($OrdNo, $cId) {
	$delOrder_query = "DELETE FROM trOrderDetail ".
					  "WHERE tOrdNo = '$OrdNo' AND CID = '$cId' ";
	mssql_query($delOrder_query);

	$delOrder_query = "DELETE FROM trOrderMaster ".
					  "WHERE tOrdNo = '$OrdNo' AND CID = '$cId' ";
	mssql_query($delOrder_query);
}

$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
$tCust = ($_GET['customercode']) ? $_GET['customercode'] : $_POST['customercode'];
$tCustName = ($_GET['customername']) ? $_GET['customername'] : $_POST['customername'];
$item_num = ($_GET['item_num']) ? $_GET['item_num'] : $_POST['item_num'];
$order_item = ($_GET['order_item']) ? $_GET['order_item'] : $_POST['order_item'];
$order_price = ($_GET['order_price']) ? $_GET['order_price'] : $_POST['order_price'];
$order_qty = ($_GET['order_qty']) ? $_GET['order_qty'] : $_POST['order_qty'];
$order_memo = ($_GET['order_memo']) ? $_GET['order_memo'] : $_POST['order_memo'];
$order_wscode = ($_GET['order_wscode']) ? $_GET['order_wscode'] : $_POST['order_wscode'];
$order_owncode = ($_GET['order_prodowncode']) ? $_GET['order_prodowncode'] : $_POST['order_prodowncode'];
$tDeliveryDate = ($_GET['delivery_date']) ? $_GET['delivery_date'] : $_POST['delivery_date'];
$CID = ($_GET['CID']) ? $_GET['CID'] : $_POST['CID'];
$tPassWord = ($_GET['staffID']) ? $_GET['staffID'] : $_POST['staffID'];

$shipto = ($_GET['shipto']) ? $_GET['shipto'] : $_POST['shipto'];
$passName = ($_GET['passName']) ? $_GET['passName'] : $_POST['passName'];
$orderMemo = ($_GET['orderMemo']) ? $_GET['orderMemo'] : $_POST['orderMemo'];
$creditLink = ($_GET['creditLink']) ? $_GET['creditLink'] : $_POST['creditLink']; // credit order일 경우 넘어오는 관련 Order#
$CustomerPO = ($_GET['CustomerPO']) ? $_GET['CustomerPO'] : $_POST['CustomerPO']; 
$credit = ($_GET['credit']) ? $_GET['credit'] : $_POST['credit']; // credit order여부 확인

//return;

$total_Amt = 0;

$tDate = date("Y-m-d");

if($mode == "delete" || $mode == "modify") {
	$tOrdNo = ($_GET['order_no']) ? $_GET['order_no'] : $_POST['order_no'];

	order_delete($tOrdNo, $CID);
	
	if($mode == "delete")	
	{
		echo("<script>
				alert('삭제되었습니다.');
				var urlA = window.opener.location.href.split('?');
				window.opener.location = urlA[0]+'?currentTab=tab1';
				window.open('about:blank','_self').close();
			</script>"); 
		return;
	}
}

// credit order인지 확인 후 credit order일 경우 관련 order no가 정상적인지 먼저 확인한다.
if($credit == 'yes' && $mode == "add")
{
	$query = "SELECT COUNT(*) as rowNum FROM trSalesMaster WHERE CID='$CID' AND colInvNo='$creditLink'";
	$rslt = mssql_query($query);
	$row = mssql_fetch_array($rslt);
	if($row['rowNum'] == '0')
	{
		echo("<script>alert('입력하신 인보이스 번호($creditLink)를 찾을 수 없습니다. 다시 시도 바랍니다.');history.back();</script>"); 
		return;
	}
}

$tOrdNo = "";
$item_num = intval($item_num);

for($i = 1; $i <= $item_num; $i++) {
	// checking history
	// 2015.11.04 10/30일 영업,지원팀 미팅에서 Barcode 입력이 어려워 wscode 와 prodowncode로 제품 주문이 가능토록 수정
	//$itemInfo_query = "SELECT wsCode, ProdOwnCode, suppCode, tType, tProd, tPtype, tTax, tPtype2, tPunit, prodSize, tMemo ".
	//				  "FROM trOrderDetail ".
	//				  "WHERE tProd = '".$order_item[$item_num-$i]."' AND tCust = '".$tCust."' AND CID = '$CID' ";
	$itemInfo_query = "SELECT wsCode, ProdOwnCode, suppCode, tType, tProd, tPtype, tTax, tPtype2, tPunit, prodSize, tMemo ".
					  "FROM trOrderDetail ".
					  "WHERE wscode = '".$order_wscode[$item_num-$i]."' AND ProdOwnCode = '".$order_owncode[$item_num-$i]."' AND tCust = '".$tCust."' AND CID = '$CID' ";
	$itemInfo_query_result = mssql_query($itemInfo_query);
	$itemInfo_row = mssql_fetch_array($itemInfo_query_result);

	if(!$itemInfo_row) {
		// 2015.11.04 10/30일 영업,지원팀 미팅에서 Barcode 입력이 어려워 wscode 와 prodowncode로 제품 주문이 가능토록 수정
		//$itemInfo_query = "SELECT wsCode, ProdOwnCode, SuppCode AS suppCode, prodType AS tType, prodId AS tProd, prodType AS tPtype, prodTax AS tTax, prodType2 AS tPtype2, prodUnit AS tPunit, prodsize ".
		//				  "FROM Inventory_Item ".
		//				  "WHERE prodId = '".$order_item[$item_num-$i]."' AND CID='$CID' ";
		$itemInfo_query = "SELECT wsCode, ProdOwnCode, SuppCode AS suppCode, prodType AS tType, prodId AS tProd, prodType AS tPtype, prodTax AS tTax, prodType2 AS tPtype2, prodUnit AS tPunit, prodsize ".
						  "FROM Inventory_Item ".
						  "WHERE wscode = '".$order_wscode[$item_num-$i]."' AND ProdOwnCode = '".$order_owncode[$item_num-$i]."' AND CID='$CID' ";
		$itemInfo_query_result = mssql_query($itemInfo_query);
		$itemInfo_row = mssql_fetch_array($itemInfo_query_result);
	}

	$wsCode = $itemInfo_row['wsCode'];
	$ProdOwnCode = $itemInfo_row['ProdOwnCode'];
	$suppCode = $itemInfo_row['suppCode'];
	$tType = $itemInfo_row['tType'];
	$tProd = $itemInfo_row['tProd'];
	$tPtype = $itemInfo_row['tPtype'];
	$tTax = $itemInfo_row['tTax'];
	$tPtype2 = $itemInfo_row['tPtype2'];
	$tPunit = $itemInfo_row['tPunit'];
	$prodsize = $itemInfo_row['prodsize'];

	$tID = $i+1;
	$tQty = $order_qty[$item_num - $i];
	$tOUprice = $order_price[$item_num - $i];
	$tAmt = ($tQty * $tOUprice);
	$tGst = 0;
	$tPst = 0;
	if($tTax == 'G')
	{
		$tGst = floor(0.05 * $tAmt * 100)/100;
	}
	else if($tTax == 'B')
	{
		$tGst = floor(0.05 * $tAmt * 100)/100;
		$tPst = floor(0.07 * $tAmt * 100)/100;
	}
	//$tPassStation = '9'; // need to change
	$tTime = date("H:i:s");

	$total_Amt = $total_Amt + $tAmt + $tGst + $tPst;

	if($mode == "add") {
		// get OrdNo
		if(!$tOrdNo) {
			$getOrdNo_query = "SELECT TOP 1 tOrdNo FROM trOrderDetail WHERE tDate = '$tDate' AND CID = '$CID' ORDER BY tOrdNo DESC";
			$getOrdNo_query_result = mssql_query($getOrdNo_query);
			$getOrdNo_row = mssql_fetch_array($getOrdNo_query_result);
			if($getOrdNo_row) {
				$tOrdNo = $getOrdNo_row['tOrdNo'] + 1;
			} else {
				$tOrdNo = (date("ymd")."01");
			}
		}

		$addOrder_query = "INSERT INTO trOrderDetail (CID, tID, wsCode, ProdOwnCode, suppCode, tType, tProd, tDate, tPtype, tQty, tOUPrice, tAmt, tGst, tPst, tPassword, tTax, tOrdNo, tCust, tTime, tPtype2, tPunit, tDeliveryDate, prodSize, tMemo) ".
						  "VALUES ('$CID', $i, '$wsCode', '$ProdOwnCode', '$suppCode', '$tType', '$tProd', '$tDate', '$tPtype', ".$order_qty[$item_num-$i].", ".$order_price[$item_num-$i].", $tAmt, $tGst, $tPst, '$tPassWord', '$tTax', '$tOrdNo', '$tCust', '$tTime', '$tPtype2', '$tPunit', '$tDeliveryDate','$prodsize','".Br_dconv($order_memo[$item_num-$i])."') ";
		mssql_query($addOrder_query);
//		echo $modOrder_query."<br>";
	} 
	if($mode == "modify") {
		$tOrdNo = ($_GET['order_no']) ? $_GET['order_no'] : $_POST['order_no'];

		$modOrder_query = "INSERT INTO trOrderDetail (CID, tID, wsCode, ProdOwnCode, suppCode, tType, tProd, tDate, tPtype, tQty, tOUPrice, tAmt, tGst, tPst, tPassword, tTax, tOrdNo, tCust, tTime, tPtype2, tPunit, tDeliveryDate, prodSize, tMemo) ".
						  "VALUES ('$CID', $i, '$wsCode', '$ProdOwnCode', '$suppCode', '$tType', '$tProd', '$tDate', '$tPtype', ".$order_qty[$item_num-$i].", ".$order_price[$item_num-$i].", $tAmt, $tGst, $tPst, '$tPassWord', '$tTax', '$tOrdNo', '$tCust', '$tTime', '$tPtype2', '$tPunit', '$tDeliveryDate','$prodsize','".Br_dconv($order_memo[$item_num-$i])."') ";
		mssql_query($modOrder_query);
//		echo $modOrder_query."<br>";
	}

	$Insert_Record = "YES";
}

if ($Insert_Record) {

	$orderMemo=Br_dconv($orderMemo);
	$query = "INSERT INTO trOrderMaster (CID,tOrdNo,tDate,tAMT,tGst,tPst,tCust,salesman,tTerm,tlimit,ShipTo,tMemo,tDeliveryDate,tStatus,CreditInvNo,CustomerPO) ".
				  "VALUES ('$CID', '$tOrdNo', '$tDate', $total_Amt, 0, 0, '$tCust', '".Br_dconv($passName)."', 0, 0, '".Br_dconv($shipto)."', '$orderMemo', '$tDeliveryDate', 'O', '$creditLink', '".Br_dconv($CustomerPO)."') ";
	mssql_query($query);
} 

if($mode == "add") {
	echo("<script>
			alert('완료되었습니다.');
			var urlA = window.opener.location.href.split('?');
			window.opener.location = urlA[0]+'?currentTab=tab1';
			window.open('about:blank','_self').close();
		</script>"); 
} else {
	echo("<script>location.replace('sales_order.php?orderNo=$tOrdNo&customercode=$tCust&customername=".urlencode($tCustName)."');</script>"); 
}

?>
