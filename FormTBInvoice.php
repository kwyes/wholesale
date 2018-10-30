<?php
	session_start();
	include_once "login_check.php";
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$cId = $_SESSION['staffCID'];
	$ITEMS_PER_PAGE = 20;
	//$newPrint = false; // ���ο� sales�� ���� ���Ǵ� �ڵ�� �����ϰ� ����.

	$err = new Exception('DB ���࿡ ������ �ֽ��ϴ�. �����ڿ��� ���� �ٶ��ϴ�.');

	$invoiceno = ($_GET['invoiceno']) ? $_GET['invoiceno'] : $_POST['invoiceno'];
	$target_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];

	$today = date("Y-m-d");

	if ($target_date == "") { $target_date = $today; }

// 2015.12.04
// ****** tUIprice �� ���ϱ� ******* 
// 2015.12.07 tUIprice ���� �κ��̽� �߻������� ���ϴ� ������ �Ѵ�.
//            order �������� �� ��� ���� ��ǰ�� ������ ��¥�� �޶��� ��Ȯ�� ������ ���� �� ����.
	// wscode, owncode : ��ǰ�ڵ�
	// qty : �� ��ǰ �ֹ�����
	// balance : �� ��ǰ ���
	function calcIUprice($cid, $wscode, $owncode, $qty, $balance)
	{
//* ���� ����
//1. purchase_detail ���� purchase_master�� ���°� close�� �׸��� ����Ʈ�� �ð� �������� ����
		$query2 = "SELECT a.pd_date, a.pd_po_no, a.wsCode, a.ProdOwnCode, a.qty, a.prodPrice, b.status, myobAvgCost FROM purchase_detail a ".
					"LEFT JOIN purchase_master b ON a.CID = b.CID AND a.pd_po_no = b.pm_po_no AND a.pd_date = b.pm_date ".
					"LEFT JOIN Inventory_Item c ON c.CID = a.CID AND c.wsCode = '$wscode' AND c.ProdOwnCode = '$owncode' ".
				 "WHERE a.wsCode = '$wscode' AND a.ProdOwnCode = '$owncode' AND a.CID='$cid' AND b.status = 'C' ".
				 "ORDER BY a.pd_date DESC";

		$rst2 = mssql_query($query2);
		$iuprice = 0; // ��ǰ ���� �ʱ�ȭ
		if(mssql_num_rows($rst2) > 0) // 0 ���� ���� ���� ���� �̷���ü�� ���� ���� ���� ����� �� �� ����. ���� ���̽���
		{
			$idx = 0; // �ش��׸� ���� �迭 ���ؽ�
			$sum = 0; // �������� ���� ����
			while($row2 = mssql_fetch_array($rst2))
			{
		//2. purchaseQty�迭 �ۼ� (idx, qtySum, price)  qtySum �������� ���� balance���� Ŭ��������
				$sum += $row2['qty']; // �������� ���Ѵ�.
				$purchaseQty[$idx] = array('qty' => $row2['qty'], 'qtySum' => $sum, 'price' => $row2['prodPrice']); // ���� ������ ������ �迭�� ����
				if($sum >= $balance) break; // �������� �� �������� ����� ũ�� ��ƾ�� ���� ���´�. 
				$idx++;
			}

		//3. idx2�� 0���� �ʱ�ȭ, 
		//    leftQty[idx2].qty = Balance - purchaseQty[idx].qtySum ����, ������ ���� ����
		//    leftQty[idx2].price = purchaseQty[idx].price 
		//    orderQty = qty(�� �ֹ��� ����)
			$idx2 = 0; // �ֹ� �������� ������ ����ϱ� ���� ���� �� ���� �� ���� ���� �迭�� ���� �ε���
			$orderQty = $qty; // �� �������� �ֹ� ����

			if($balance >= 0) // ��� 0 ���� ū ��츸 ���� ����� ���� �迭�� ����, ��� 0 ���� ���� ��� ���� ���� ���� ����
			{
				if($idx > 0) // idx 0 �� ���� ��� ��ü�� ���� ���ŷ� ���� ���� ���� ���� ���� ���� ���� 
							 // $idx�� 1�̻��̶�� �ǹ̴� ��� �ּ� 2�� �̻��� ���� �̷��� ���ԵǾ� ������ �ǹ�
				{
					$tmp = $balance - $purchaseQty[$idx-1]['qtySum'];
					while($idx >= 0)
					{
		//4. �� orderQty �� ���� �ܷ����� ũ�� 4-1, ������ 4-2
						$tmp2 = $orderQty - $tmp;
						if($tmp2 > 0) // �ֹ������� ���� �ܷ� ���� ���� ŭ
						{
		//4-1. ���� ����� ���� �迭�� �� ������ ������ ���� ����
							$leftQty[$idx2] = array('qty' => $tmp, 'price' => $purchaseQty[$idx]['price'] );
							$orderQty = $tmp2;
						} else { // �ֹ������� ���� �ܷ� ���� ���� ���� 
		// 4-2. ���� ����� ���� �迭�� �ܷ� ���� ���� �� �� ���� ���� ����
							$leftQty[$idx2] = array('qty' => $orderQty, 'price' => $purchaseQty[$idx]['price'] );
							break; // ������ ���� �迭�� ����� ������ �������� ���� ���
						}
						$idx--;
						$idx2++;
						if($idx == 0) // $idx�� 0�Ǵ� �������� ������ ������ ���ؼ��� ������ ���� ������ �����Ų��.
						{
							$leftQty[$idx2] = array('qty' => $orderQty, 'price' => $purchaseQty[$idx]['price'] );
							break;
						}
						$tmp = $purchaseQty[$idx]['qty'];
					}
		//5. idx2�� 0 �ɶ����� �ݺ��ؼ� ����*���� ���� ���� �� qty(����ǰ�ֹ�����) �� ���� ���� iuprice�� ����
					$sum = 0;
					while($idx2 >= 0){
						$sum += $leftQty[$idx2]['qty'] * $leftQty[$idx2]['price'];
						$idx2--;
					}
					$iuprice = $sum/$qty;

				} else { // ��� ���� ���ŷ� ���� ����. ������ ���� ������ ����
					$iuprice = $purchaseQty[0]['price']; 
				}
			} else { // ��� 0���� ���� ���� ���� ������ ���� ����
				$iuprice = $purchaseQty[0]['price']; 
			}
		} else { // 0 ���� ���� ���� ���� �̷���ü�� ���� ���� ���� ����� �� �� ����. ���� ���̽��� myobAvgCost�� ��ü
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
	$SalesAmount = 0; //!!!!TODO ������ �Ѿ ��쿡 ����ó�� �ʿ�
	$Freight = 0; //!!!!TODO DB �ʵ� �߰� �ʿ�
	$SalesTax = 0.0;
	$TotalAmount = 0;
	$PaidToday = 0; //!!!!TODO ���� �帧 ���� �ʿ�
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
	
		// �� ��ǰ�� ������ ��� ���� ��� ��ǰ�� ������ ����ؼ� �����´�.
		if(trim($row['tIUprice']) == "" || trim($row['tIUprice']) == "0")
		{
			$tIUprice = calcIUprice($cId,$row['wsCode'],$row['ProdOwnCode'],$row['tQty'],$row['prodBalance']);
			// trSales���� tIUprice ����
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
	// ���� ������ ���߱� ���� ���̺� �������� �׸����� => �Ϸ�Ǹ� 0���� ����
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
<!-------- �ΰ� �̹��� ------------------------->
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
// �׸� ������ ����Ʈ ���
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

	// 2015.11.12 $newPrint ����� ������� �ʰ� �Ǿ� �ʿ��� �ڵ尡 ������� ����.
	if($MasterStatus == 'O')
	{
		// ���¸� 'P'(process)�� ����, ���� �����Ǵ� Invoice Qty ������ trSales�� Inventory_Item DB�� OnHand���� ����Ǿ�� ��.
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
