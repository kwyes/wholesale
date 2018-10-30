<?php
	session_start();
	include_once "login_check.php";
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$ITEMS_PER_PAGE = 20;
	//$newPrint = false; // 새로운 sales를 위해 사용되던 코드로 사용안하고 있음.

	$err = new Exception('DB 실행에 문제가 있습니다. 관리자에게 문의 바랍니다.');

	$invoiceno = ($_GET['invoiceno']) ? $_GET['invoiceno'] : $_POST['invoiceno'];
	$target_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];

	$today = date("Y-m-d");

	if ($target_date == "") { $target_date = $today; }

// 2015.12.04
// ****** tUIprice 값 구하기 ******* 
// 2015.12.07 tUIprice 값은 인보이스 발생시점에 구하는 것으로 한다.
//            order 시점에서 할 경우 실제 물품이 나가는 날짜가 달라져 정확한 원가를 구할 수 없음.
	// wscode, owncode : 제품코드
	// qty : 현 제품 주문갯수
	// balance : 현 제품 재고
	function calcIUprice($cid, $wscode, $owncode, $qty, $balance)
	{
//* 간략 절차
//1. purchase_detail 에서 purchase_master의 상태가 close인 항목의 리스트를 시간 역순으로 쿼리
		$query2 = "SELECT a.pd_date, a.pd_po_no, a.wsCode, a.ProdOwnCode, a.qty, a.prodPrice, b.status, myobAvgCost FROM purchase_detail a ".
					"LEFT JOIN purchase_master b ON a.CID = b.CID AND a.pd_po_no = b.pm_po_no AND a.pd_date = b.pm_date ".
					"LEFT JOIN Inventory_Item c ON c.CID = a.CID AND c.wsCode = '$wscode' AND c.ProdOwnCode = '$owncode' ".
				 "WHERE a.wsCode = '$wscode' AND a.ProdOwnCode = '$owncode' AND a.CID='$cid' AND b.status = 'C' ".
				 "ORDER BY a.pd_date DESC";

		$rst2 = mssql_query($query2);
		$iuprice = 0; // 제품 원가 초기화
		if(mssql_num_rows($rst2) > 0) // 0 보다 작은 경우는 구매 이력자체가 없는 경우로 원가 계산을 할 수 없음. 예외 케이스임
		{
			$idx = 0; // 해당항목 구매 배열 인텍스
			$sum = 0; // 누적합을 위한 변수
			while($row2 = mssql_fetch_array($rst2))
			{
		//2. purchaseQty배열 작성 (idx, qtySum, price)  qtySum 누적합이 현재 balance보다 클때까지만
				$sum += $row2['qty']; // 누적합을 구한다.
				$purchaseQty[$idx] = array('qty' => $row2['qty'], 'qtySum' => $sum, 'price' => $row2['prodPrice']); // 누적 갯수랑 원가를 배열에 저장
				if($sum >= $balance) break; // 누적합이 현 아이템의 재고보다 크면 루틴을 빠져 나온다. 
				$idx++;
			}

		//3. idx2를 0으로 초기화, 
		//    leftQty[idx2].qty = Balance - purchaseQty[idx].qtySum 실행, 원가도 같이 저장
		//    leftQty[idx2].price = purchaseQty[idx].price 
		//    orderQty = qty(현 주문의 갯수)
			$idx2 = 0; // 주문 아이템의 원가를 계산하기 위해 구매 별 갯수 및 원가 저장 배열을 위한 인덱스
			$orderQty = $qty; // 현 아이템의 주문 갯수

			if($balance >= 0) // 재고가 0 보다 큰 경우만 원가 계산을 위한 배열을 만듬, 재고가 0 보자 작을 경우 최종 구매 원가 적용
			{
				if($idx > 0) // idx 0 일 경우는 재고 자체가 최종 구매량 보다 작은 경우로 최종 구매 원가 적용 
							 // $idx가 1이상이라는 의미는 재고가 최소 2개 이상의 구매 이력이 포함되어 있음을 의미
				{
					$tmp = $balance - $purchaseQty[$idx-1]['qtySum'];
					while($idx >= 0)
					{
		//4. 현 orderQty 가 구매 잔량보다 크면 4-1, 작으면 4-2
						$tmp2 = $orderQty - $tmp;
						if($tmp2 > 0) // 주문갯수가 구매 잔량 갯수 보다 큼
						{
		//4-1. 원가 계산을 위한 배열에 현 구매의 갯수와 원가 저장
							$leftQty[$idx2] = array('qty' => $tmp, 'price' => $purchaseQty[$idx]['price'] );
							$orderQty = $tmp2;
						} else { // 주문갯수가 최종 잔량 갯수 보다 작음 
		// 4-2. 원가 계산을 위한 배열에 잔량 구매 갯수 및 현 구매 원가 저장
							$leftQty[$idx2] = array('qty' => $orderQty, 'price' => $purchaseQty[$idx]['price'] );
							break; // 루프를 빠져 배열에 저장된 정보를 바탕으로 원가 계산
						}
						$idx--;
						$idx2++;
						if($idx == 0) // $idx가 0되는 시점에서 나머지 갯수에 대해서는 무조건 최종 원가를 적용시킨다.
						{
							$leftQty[$idx2] = array('qty' => $orderQty, 'price' => $purchaseQty[$idx]['price'] );
							break;
						}
						$tmp = $purchaseQty[$idx]['qty'];
					}
		//5. idx2가 0 될때까지 반복해서 갯수*원가 값을 더한 후 qty(현제품주문갯수) 로 나눈 값을 iuprice에 저장
					$sum = 0;
					while($idx2 >= 0){
						$sum += $leftQty[$idx2]['qty'] * $leftQty[$idx2]['price'];
						$idx2--;
					}
					$iuprice = $sum/$qty;

				} else { // 재고가 최종 구매량 보다 작음. 원가는 최종 구매의 원가
					$iuprice = $purchaseQty[0]['price']; 
				}
			} else { // 재고가 0보다 작은 경우는 최종 구매의 원가 적용
				$iuprice = $purchaseQty[0]['price']; 
			}
		} else { // 0 보다 작은 경우는 구매 이력자체가 없는 경우로 원가 계산을 할 수 없음. 예외 케이스로 myobAvgCost로 대체
			$query2 = "SELECT myobAvgCost FROM Inventory_Item WHERE CID = '$cid' AND wsCode = '$wscode' AND ProdOwnCode = '$owncode' ";
			$rst2 = mssql_query($query2);
			$row2 = mssql_fetch_array($rst2);

			$iuprice = trim($row2['myobAvgCost']) == "" ? 0 : $row2['myobAvgCost']; 
		}

		$iuprice = round($iuprice,2);
		return $iuprice;
	} // function calcIUprice($cid, $wscode, $owncode, $qty, $balance)

	$Query = "SELECT CONVERT(char(10),a.tDate,120) AS tDate, a.CID, tID, tInvNo, d.tOrdNo, tProd, tQty, tOuprice, tPunit, a.tGst, a.tPst, a.tAmt, prodKname, a.wsCode, a.ProdOwnCode, a.ItemMemo, a.tIUprice, b.prodBalance, b.prodId, b.prodSize, c.CustomerPO, d.ShipTo, d.salesman, e.shipto AS BillTo, a.tCust, c.SalesMemo, c.colStatus, d.CreditInvNo FROM trSales a ".
			"LEFT JOIN Inventory_Item b ON a.wsCode = b.wsCode AND a.ProdOwnCode = b.ProdOwnCode AND b.CID='".$cId."' ".
			"LEFT JOIN trSalesMaster c ON c.colInvNo = a.tInvNo AND a.CID = c.CID ".
			"LEFT JOIN trOrderMaster d ON d.tOrdNo = c.OrderNo AND a.CID = d.CID ".
			"LEFT JOIN ShipTo e ON e.CardID = a.tCust AND seq=1 AND a.CID = e.CID ".
		 "WHERE tInvNo = '".$invoiceno."' AND a.CID='".$cId."' ".
		 "ORDER BY tID ASC ";

try{
	if(!$rst = mssql_query($Query)) throw $err;
	$list = "";
	$SalesAmount = 0; //!!!!TODO 페이지 넘어갈 경우에 대한처리 필요
	$Freight = 0; //!!!!TODO DB 필드 추가 필요
	$SalesTax = 0.0;
	$TotalAmount = 0;
	$PaidToday = 0; //!!!!TODO 업무 흐름 정의 필요
	$BalanceDue = 0;
	$BillTo = "";
	$ShipTo = "";
	$SalesPerson = "";
	$tOrdNo = "";
	$tDeliveryDate = "";
	$tCust = "";
	$SalesMemo = "";
	$CustomerPO = "";
	$CreditInvNo = "";
	$MasterStatus = "";
	$ProdSize = "";
	
	$num_row = mssql_num_rows($rst);
	$pages = ceil($num_row / $ITEMS_PER_PAGE); 
	$list = array();

	$i = 0;
	while($row = mssql_fetch_array($rst)) {
	
		if($tCust == "")	$tCust = $row['tCust'];
		if($BillTo == "")	$BillTo = $row['BillTo'];
		if($ShipTo == ""){
			$ShipTo = $row['ShipTo'];
		}
		if($SalesPerson == "")	$SalesPerson = Br_iconv($row['salesman']);
		if($tOrdNo == "")	$tOrdNo = $row['tOrdNo'];
		if($tDeliveryDate == "")	$tDeliveryDate = $row['tDeliveryDate'];
		if($SalesMemo == "")	$SalesMemo = Br_iconv($row['SalesMemo']);
		if($CustomerPO == "")	$CustomerPO = Br_iconv($row['CustomerPO']);
		if($CreditInvNo == "")	$CreditInvNo = $row['CreditInvNo'];
		if($MasterStatus == "")	$MasterStatus = $row['colStatus'];
		$ItemMemo = "";
		if(trim($row['ItemMemo']) != "")
			$ItemMemo = '('.Br_iconv(trim($row['ItemMemo'])).')';
		$ProdSize = "";
		if(trim($row['prodSize']) != "")
			$ProdSize = '/'.Br_iconv(trim($row['prodSize']));
//echo $tCust."<br/>";
		$tax = "";
		if($row['tGst'] != ""){
			$tax .= "GST";
			$SalesTax += $row['tGst'];
		}
		if($row['tPst'] != ""){
			$tax .= "/PST";
			$SalesTax += $row['tPst'];
		}

		$prodName = Br_iconv(trim($row['prodKname']));

		$idx = floor($i / $ITEMS_PER_PAGE); 
		if($tmp != $idx)
		{
			//echo "idx:".$idx.":".$list[$idx-1];
			$tmp = $idx;
			$list[$idx] = "";
		}
		$i++;

		$tOuprice = "$".number_format($row['tOuprice'],2);
		$tAmt = "$".number_format($row['tAmt'],2);
	
		$list[$idx] .= '
<tr>
  <td width="22" align="right">'.$row['tQty'].'</td>
  <td width="92" align="left">'.$row['wsCode'].'</td>
  <td width="202" align="left">'.$prodName.$ProdSize.'&nbsp;<font color="blue">'.$ItemMemo.'</font></td>
  <td width="56" align="right">'.$tOuprice.'</td>
  <td width="37" align="right">'.$row['tPunit'].'</td>
  <td width="37"align="left"><!-- DISC% -->&nbsp;</td>
  <td width="89" align="right">'.$tAmt.'</td>
  <td width="45" align="left">'.$tax.'</td>
</tr>';
	
		// 현 제품의 원가가 비어 있을 경우 제품의 원가를 계산해서 가져온다.
		if(trim($row['tIUprice']) == "" || trim($row['tIUprice']) == "0")
		{
			$tIUprice = calcIUprice($cId,$row['wsCode'],$row['ProdOwnCode'],$row['tQty'],$row['prodBalance']);
			// trSales에서 tIUprice 갱신
			$Query = "UPDATE trSales SET tIUprice = $tIUprice WHERE CID='".$row['CID']."' and tInvNo ='$invoiceno' and wsCode='".$row['wsCode']."' and ProdOwnCode='".$row['ProdOwnCode']."'";

			if(!mssql_query($Query)) throw $err;
		}

		$SalesAmount += $row['tAmt'];
	} // while($row = mssql_fetch_array($rst)) 
	$TotalAmount = $SalesAmount + $Freight + $SalesTax;
	$BalanceDue = $TotalAmount + $PaidToday;

	$Query = "SELECT Name,cTerm FROM Card WHERE CID = '$cId' AND CardType = '1' AND CardID = '".$tCust."'";
	$rst = mssql_query($Query);
	$row2 = mssql_fetch_array($rst);
	$custName = Br_iconv($row2['Name']);
	$BillTo = $custName."<br/>".$BillTo;
	if($row2['cTerm'] != "" && $row2['cTerm'] != "0") $cTerm = "Net ".$row2['cTerm'];

// Include the main TCPDF library (search for installation path).
	require_once('tcpdf_include.php');
	require_once('tcpdf/tcpdf_barcodes_1d.php');

	ini_set("memory_limit" , -1);

// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetAutoPageBreak(true, 5);
	//$pdf->setPrintFooter(false);

// ---------------------------------------------------------
// set font
	$pdf->SetFont('cid0kr', '', 9);
	$pdf->SetMargins(0, 0, 0);
	// 기존 용지를 맞추기 위해 테이블 윤곽선을 그리도록 => 완료되면 0으로 세팅
	$border = 0;
	
//=========================================================================
	for($i = 1; $i <= $pages; $i++)
	{
		// add a page
		//if($i != $pages) 
		$pdf->AddPage();
		$html_logo = <<<EOD
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#E0E0FF">
 <tr>
<!-------- 로고 이미지 ------------------------->
  <td width="200px" align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
     <td width="100%" cellpadding="0" cellspacing="0" align="left">
      <img src="images/tb-logo.png" width="200">
     </td>
	</tr>
   </table>
  </td>
<!-------- Contact information ------------------------->
  <td width="250px" align="left">
	<table cellpadding="10px">
	<tr>
		<td>
			<table border="0" width="100%">
			<tr>
			 <td width="100%" align="left" style="color:blue;font-size:8;">
			  100-88 Briganline Dr, Coquitlam B.C. Canada V3K 6Z6
			 </td>
			</tr>
			<tr>
			 <td width="100%" align="left" style="color:blue;font-size:8;">
			  Tel: (604) 540-0306  Fax: (604) 540-0542 
			 </td>
			</tr>
			<tr>
			 <td width="100%" align="left" style="color:blue;font-size:8;">
			  www.t-brothers.com
			 </td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
  </td>
  <td width="130px" hight="150px" cellpadding="5px" cellspacing="0" align="center">
   <table border="0" cellpadding="2" width="100%">
	<tr>
	 <td width="100%" align="Center"><b style="color:blue;font-size:16;">INVOICE</b></td>
	</tr>
	<tr>
	 <td width="100%" height="20" align="Center"><b style="color:black;font-size:16">$invoiceno</b></td>
	</tr>
   </table>
  </td>
 </tr>
 <tr>
	<td colspan="3">
		<!-------- Bill To / Ship To ------------------------->
		<table width="580" border="0" cellpadding="5" cellspacing="0" bgcolor="#E0E0FF">
		<tr>
			<td height="30" colspan="4">&nbsp;</td>
		</tr>
		<tr>
		  <td width="10%" height="80" align="right" style="color:blue;font-size:8;">
			&nbsp;&nbsp;Bill To : 
		  </td>
		  <td width="40%" height="80" align="left">
			$BillTo
		  </td>
		  <td width="10%" height="80" align="right" style="color:blue;font-size:8;">
			Ship To : 
		  </td>
		  <td width="40%" height="80" align="left">
			$ShipTo <br/> $CustomerPO
		  </td>
		</tr>
		</table>
	</td>
 </tr>
 <tr>
	<td colspan="3">
		<!-------- GST Registration ------------------------->
		<table width="580" border="0" cellpadding="3" cellspacing="0" bgcolor="#E0E0FF">
		<tr>
		  <td width="100%" align="left" style="color:blue;font-size:10;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GST Registration #: 88423 0525RT 
		  </td>
		</tr>
		</table>
	</td>
 </tr>
 <tr>
	<td colspan="3">
		<!-------- Order Information ------------------------->
		<table width="580" cellpadding="1" cellspacing="0">
		<tr>
		  <th width="100" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			SALESPERSON 
		  </th>
		  <th width="100" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			YOUR ORDER NO. 
		  </th>
		  <th width="70" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			SHIP VIA 
		  </th>
		  <th width="30" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			COL 
		  </th>
		  <th width="30" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			PPD 
		  </th>
		  <th width="80" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			SHIP DATE 
		  </th>
		  <th width="90" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			TERMS 
		  </th>
		  <th width="60" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			DATE 
		  </th>
		  <th width="20" align="center" bgcolor="#0000FF" style="color:white;font-size:6;">
			PG. 
		  </th>	
		</tr>
		<tr>
		  <td width="100" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- SALESPERSON -->$SalesPerson
		  </td>
		  <td width="100" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- YOUR ORDER NO.  -->$tOrdNo
		  </td>
		  <td width="70" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- SHIP VIA  -->&nbsp;
		  </td>
		  <td width="30" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- COL  -->&nbsp;
		  </td>
		  <td width="30" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- PPD  -->&nbsp;
		  </td>
		  <td width="80" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- SHIP DATE  -->$tDeliveryDate
		  </td>
		  <td width="90" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- TERMS  -->$cTerm
		  </td>
		  <td width="60" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;border-right:1px solid blue;">
			<!-- DATE  -->$today
		  </td>
		  <td width="30" height="30" align="center" style="vertical-align:middle; background-color:white;color:black;font-size:10;">
			<!-- PAGE  -->$i/$pages
		  </td>
		</tr>
		</table>
	</td>
 </tr>
</table>
<!-------- Item List ------------------------->
<table width="580" cellpadding="1" cellspacing="0" style="border-bottom:1px solid blue;">
<!-------- Item List table title ------------------------->
<tr>
  <th width="22" align="center" bgcolor="blue" style="color:white;font-size:6;">
	QTY 
  </th>
  <th width="92" align="center" bgcolor="blue" style="color:white;font-size:6;">
	ITEM NUMBER 
  </th>
  <th width="202" align="center" bgcolor="blue" style="color:white;font-size:6;">
	DESCRIPTION
  </th>
  <th width="56" align="center" bgcolor="blue" style="color:white;font-size:6;">
	PRICE
  </th>
  <th width="37" align="center" bgcolor="blue" style="color:white;font-size:6;">
	UNIT
  </th>
  <th width="37" align="center" bgcolor="blue" style="color:white;font-size:6;">
	DISC% 
  </th>
  <th width="89" align="center" bgcolor="blue" style="color:white;font-size:6;">
	EXTENDED PRICE 
  </th>
  <th width="45" align="center" bgcolor="blue" style="color:white;font-size:6;">
	TAX
  </th>
</tr>
<tr>
  <td width="22" height="420" align="right" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="92" height="420" align="left" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="202" height="420" align="left" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="56" height="420" align="right" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="37" height="420" align="right" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="37"height="420" align="left" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="89" height="420" align="right" style="border-right:1px solid blue;border-bottom:1px solid blue;">&nbsp;</td>
  <td width="45" height="420" align="left" style="border-bottom:1px solid blue;">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" color="blue" style="border-right:1px solid blue;">&nbsp;</td>
  <td colspan="2" color="blue" align="center" style="border-right:1px solid blue;"><font size="8">SALE AMOUNT</font></td>
  <td colspan="2" color="blue" align="center">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" color="blue" style="border-right:1px solid blue;">&nbsp;</td>
  <td colspan="2" color="blue" align="center" style="border-right:1px solid blue;"><font size="8">FREIGHT</font></td>
  <td colspan="2" color="blue" align="center">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" color="blue" style="border-right:1px solid blue;">&nbsp;</td>
  <td colspan="2" color="blue" align="center" style="border-right:1px solid blue;"><font size="8">SALES TAX</font></td>
  <td colspan="2" color="blue" align="center">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" color="blue" style="border-right:1px solid blue;">&nbsp;</td>
  <td colspan="2" color="blue" align="center" style="border-right:1px solid blue;"><font size="8">TOTAL AMOUNT</font></td>
  <td colspan="2" color="blue" align="center">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" color="blue" style="border-right:1px solid blue; border-bottom:1px solid blue;">&nbsp;</td>
  <td colspan="2" color="blue" align="center" style="border-right:1px solid blue; border-bottom:1px solid blue;"><font size="8">PAID TODAY</font></td>
  <td colspan="2" color="blue" align="center"  style="border-bottom:1px solid blue;">&nbsp;</td>
</tr>
<tr>
  <td colspan="4" style="height:30px; vertical-align:middle; border-right:1px solid blue; border-bottom:3px solid blue;">Memo: All claims must be made within 7 days from the date received</td>
  <td colspan="2" align="center" style="height:30px; color:white; background-color:blue; vertical-align:middle; border-right:1px solid blue; border-bottom:3px solid blue;"><font size="8">BALANCE DUE</font></td>
  <td align="center" style="border-bottom:3px solid blue;">&nbsp;</td>
  <td bgcolor="#E0E0FF" align="center" style="height:30px; vertical-align:middle; border-bottom:3px solid blue;">&nbsp;</td>
</tr>
</table>
EOD;
		$j = $i-1;
// 항목 아이템 리스트 출력
		$html_list = <<<EOD
<!-------- Item List ------------------------->
<table width="580" cellpadding="1" cellspacing="0" border="0">
<!-------- Item List table body ------------------------->
$list[$j]
</table>
EOD;
		$pdf->writeHTMLCell(760, 250, 5, 5, $html_logo);
		// define barcode style
		$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => false,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => true,
			'font' => 'helvetica',
			'fontsize' => 8,
			'stretchtext' => 4
		);
		if($CreditInvNo == "")
			$pdf->write1DBarcode($invoiceno, 'C128', '173', '22', '', '15', 0.4, $style, 'N');
		else
			$pdf->write1DBarcode($invoiceno, 'C128', '168', '22', '', '15', 0.4, $style, 'N');

	//	$pdf->SetFillColor(255, 255, 255);
		$pdf->writeHTMLCell(0, 0, 5, 87, $html_list);
	}
//=========================================================================
	
	$SalesAmount = "$".number_format($SalesAmount,2);
	$Freight = "$".number_format($Freight,2);
	$SalesTax = "$".number_format($SalesTax,2);
	$TotalAmountS = "$".number_format($TotalAmount,2);
	$PaidToday = "$".number_format($PaidToday,2);
	$BalanceDue = "$".number_format($BalanceDue,2);

	$html_tail = <<<EOD
<table width="100%" cellpadding="1" cellspacing="0" border="$border">
<!-------- Item List table total info ------------------------->
<tr>
  <td width="22" align="right">&nbsp;</td>
  <td width="92" align="left">&nbsp;</td>
  <td width="202" align="left">&nbsp;</td>
  <td width="56" align="right">&nbsp;</td>
  <td width="37" align="right">&nbsp;</td>
  <td width="37"align="left">&nbsp;</td>
  <td width="89" align="right">$SalesAmount</td>
  <td width="45" align="left">&nbsp;</td>
</tr>
<tr>
  <td colspan="6" rowspan="4" align="center">&nbsp;$SalesMemo</td>
  <td align="right">$Freight</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td align="right">$SalesTax</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td align="right">$TotalAmountS</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td align="right">$PaidToday</td>
  <td>&nbsp;</td>
</tr>
<!-------- Item List bottom ------------------------->

<tr>
  <td height="30" colspan="4">&nbsp;</td>
  <td height="30" colspan="2">&nbsp;</td>
  <td height="30" align="right"><b style="color:black;font-size:12;">$BalanceDue</b></td>
  <td height="30">&nbsp;</td>
</tr>
</table>
EOD;

	$pdf->writeHTMLCell(760, 0, 5, 233, $html_tail); 
	$pdf->lastPage();

	// 2015.11.12 $newPrint 기능을 사용하지 않게 되어 필요한 코드가 실행되지 않음.
	if($MasterStatus == 'O')
	{
		// 상태를 'P'(process)로 변경, 이후 수정되는 Invoice Qty 값들은 trSales와 Inventory_Item DB의 OnHand에도 적용되어야 함.
		$Query = "UPDATE trSalesMaster SET colStatus='P' WHERE CID = '".$cId."' AND colDate = '".$target_date."' AND colInvNo = '".$invoiceno."' ";
		if(!$rst = mssql_query($Query)) throw $err;
	}

	//Close and output PDF document
	$pdf->Output('TBInvoice_'.$invoiceno.'.pdf', 'I');
}
catch(Exception $e)
{
	echo Br_iconv($e->getMessage());
}
//============================================================+
// END OF FILE
//============================================================+
