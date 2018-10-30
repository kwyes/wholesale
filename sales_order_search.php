<?
session_start();
$CID = $_SESSION['staffCID'];
if(!isset($CID)) // session 이 종료되었을 경우
{
	echo '<script>alert("Session time out!"); window.open("about:blank","_self").close();</script>';
	return;
}
include_once "includes/db_configms.php";
include_once "includes/common_class.php";

$mode = ($_REQUEST["mode"]) ? $_REQUEST["mode"] : $_GET['mode'];
$CardId = ($_REQUEST["CardId"]) ? $_REQUEST["CardId"] : $_GET['CardId'];
$key = ($_REQUEST["key"]) ? $_REQUEST["key"] : $_GET['key'];
$invno = ($_REQUEST["invno"]) ? $_REQUEST["invno"] : $_GET['invno'];
$tStatus = ($_REQUEST["status"]) ? $_REQUEST["status"] : $_GET['status'];
$key0 = $key;
$key = Br_dconv(urldecode($key));

if($mode == "itemHistory") { // 선택된 항목의 최근 1년 sales history 
	$wscode = ($_REQUEST["wscode"]) ? $_REQUEST["wscode"] : $_GET['wscode'];
	$owncode = ($_REQUEST["owncode"]) ? $_REQUEST["owncode"] : $_GET['owncode'];

	if($wscode == "" || $owncode == "")
		return;

	$query = "SELECT CONVERT(char(10),a.tDate,120) AS tDate,a.wsCode,a.ProdOwnCode,b.prodKname,b.prodName,b.prodSize,a.tOUprice,tQty,tCust,c.Name ".
	   		 "FROM trSales a ".
				 "LEFT JOIN Inventory_Item b ON b.CID='$CID' AND b.wsCode='$wscode' AND b.ProdOwnCode='$owncode' ".
				 "LEFT JOIN Card c ON c.CID='$CID' AND CardType='1' AND CardID=a.tCust ".
			 "WHERE a.CID='$CID' AND a.wsCode='$wscode' AND a.ProdOwnCode='$owncode' ".
			 "ORDer BY tDate DESC ";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
	// 패킷 패킹 정보 
	// itemNum::prodKname;prodName;prodSize[::tDate;tCust;Name;tOUprice;tQty]
	$packet = "::";
	$i = 0;
	while($row = mssql_fetch_array($query_result))
	{
		if($i == 0) // 한번만 보내야하는 정보
		{
			$packet .= Br_iconv($row['prodKname']).";".Br_iconv($row['prodName']).";".Br_iconv($row['prodSize']);
		}			
		$packet .= "::".$row['tDate'].";".trim($row['tCust']).";".Br_iconv($row['Name']).";".number_format($row['tOUprice'],2).";".$row['tQty'];
		$i++;  // 주문 이력 카운트
	}
	$packet = $i.$packet; // 총 주문 이력 횟수 앞에 추가
	echo $packet;

} else if($mode == "creditLink") { // credit 주문을 위한 상대 인보이스 확인 
	$query = "SELECT a.colStatus, a.colCust, a.shipto, b.Name, b.SalesPerson FROM trSalesMaster a ".
				"LEFT JOIN Card b ON b.CID = '$CID' AND a.colCust = b.CardID AND CardType = 1 ". 
			 "WHERE a.CID = '$CID' AND colInvNo='$invno'"; 
	$query_result = mssql_query($query);
	if($row = mssql_fetch_array($query_result))
	{
		echo '1::'.$row['colStatus']."::".Br_iconv($row['Name'])."::".$row['colCust']."::".Br_iconv($row['shipto'])."::".Br_iconv($row['SalesPerson']);
	} else {
		echo '0';	// 잘못된 인보이스 번호
	}
} else if($mode == "customer") {

	$query = "SELECT CardID, Phone, Name, cType, CID ".
			 "FROM card ".
			 "WHERE (Phone LIKE '%$key%' OR Name LIKE '$key%' OR CardID LIKE '$key%') AND CardType = 1 AND CID = '$CID' ".
			 "ORDER BY CardID ASC";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
?>

<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<table width="100%" style="border-collapse:collapse; letter-spacing:-1px; font-family:verdana; font-size:13px;">
		<tr style="background-color:#808080">
			<td width="70px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">CARD #</td>
			<td align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">NAME</td>
			<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#ffffff">PHONE #</td>
			<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:1; color:#ffffff">TYPE</td>
		</tr>
		<? if($row_num == 0) { ?>
			<tr>
				<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>
			</tr>
		<?	} else { ?>
			<? $i = 0; ?>
			<? while($row = mssql_fetch_array($query_result)) { ?>
				<? 
					$i++;
					if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
					else				$doc_field_name = "doc_field_purchases";
				?>
				<tr class="<?=$doc_field_name?>">
					<td align="center" style="border:1px solid #BBBBBB; border-right:0"><a href="javascript:parent.select_customer('<?=$tStatus; ?>','<?=$row['CardID']; ?>','<?=Br_iconv($row['Name']);?>','<?=$row['CID'];?>');" style="text-decoration:none; color:#000000""><?=$row['CardID']; ?></a></td>
					<td align="left" style="border:1px solid #BBBBBB; border-right:0; padding-left:5px;"><a href="javascript:parent.select_customer('<?=$tStatus; ?>','<?=$row['CardID']; ?>','<?=Br_iconv($row['Name']);?>','<?=$row['CID'];?>');" style="text-decoration:none; color:#000000"><?=Br_iconv($row['Name']); ?></a></td>
					<td align="center" style="border:1px solid #BBBBBB; border-right:0"><?=$row['Phone']; ?></td>
					<td align="center" style="border:1px solid #BBBBBB; border-right:1"><?=$row['cType']; ?></td>
				</tr>

			<? } ?>
		<? } ?>
	</table>
	<script>parent.document.getElementById("search_customer_result").style.display = "";</script>
<?
} else if($mode == "order_list") {
	// tfOrder DB로 변경 후 Order No 필요함
	$query = "SELECT CONVERT(char(10), tDate, 126) AS tDate, tPassWord, tOrdNo, tCust, CID ".
			 "FROM trOrderDetail ".
			 "WHERE tDate = '$key' and tID = 1 AND CID = '$CID' ".
			 "ORDER BY tOrdNo Desc ";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
?>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<table width="100%" style="border-collapse:collapse;">
		<tr style="background-color:#CCEEFF; letter-spacing:-1px; font-family:verdana; font-size:13px;">
			<td width="70px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#0000cc">ORDER #</td>
			<td width="69px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#0000cc">DATE</td>
			<td align="center" style="border:1px solid #BBBBBB; color:#0000cc border-right:0; color:#0000cc">CUSTOMER</td>
			<td width="38px" align="center" style="border:1px solid #BBBBBB; color:#0000cc">작성자</td>
		</tr>

		<? if($row_num == 0) { ?>
			<tr>
				<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>
			</tr>
		<? } else { ?>
			<? $i = 0; ?>
			<? while($row = mssql_fetch_array($query_result)) { ?>
				<? 
					$i++;
					if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
					else				$doc_field_name = "doc_field_purchases";
					$sName = getCardName($CID,$row['tCust'],'1');
				?>
				<tr class="<?=$doc_field_name?>">
					<td align="center" style="border:1px solid #BBBBBB; border-right:0"><a href="javascript:parent.select_order('<?=$row['tOrdNo']; ?>','<?=$row['tCust']; ?>','<?=$sName?>')" style="text-decoration:none"><?=$row['tOrdNo']; ?></a></td>
					<td align="center" style="border:1px solid #BBBBBB; border-right:0"><?=$row['tDate']; ?></td>
					<td align="left" style="border:1px solid #BBBBBB; padding-left:5px; border-right:0"><a href="javascript:parent.select_order('<?=$row['tOrdNo']; ?>','<?=$row['tCust']; ?>','<?=$sName?>')" style="text-decoration:none"><?=$sName?></a></td>
					<td align="center" style="border:1px solid #BBBBBB;"><?=$row['tPassWord']; ?></td>
				</tr>

			<? } ?>
		<? } ?>
	</table>
<?
} else if($mode == "history") {
	$result = "<link rel='stylesheet' type='text/css' href='css/style.css'/>";

	$query = "SELECT TOP 1 CONVERT(char(10), tDate, 126) AS tDate FROM trSales ".
						 "WHERE tCust = '$key' AND CID = '$CID' ".
						 "ORDER BY tDate DESC ";
	$top_result = mssql_query($query);
	$top_row = mssql_fetch_array($top_result);
	$leastDate = $top_row['tDate'];

	$yesterday = get_preMonth($leastDate, 1);

	$history_query = "SELECT * FROM ".
								"	( ".
								"		 SELECT CID, tCust, wsCode, ProdOwnCode, tDate,tProd, tQty, tOUprice, tInvNo, tPunit, ".
								"			 Row_number() over (PARTITION BY wsCode Order By tDate desc) as RND ".
								"		 FROM trSales ".
								"	) h ".
								"WHERE h.rnd = 1 and h.tOUprice > 0 and h.tCust =  '$key' AND h.CID = '$CID' AND h.tDate  BETWEEN '$yesterday' AND '$leastDate' ".
								"ORDER BY h.wsCode ";
	$history_query_result = mssql_query($history_query);
	$history_num = mssql_num_rows($history_query_result);

	$result .= '<table width="679px" style="border-collapse:collapse; overflow:scroll; border:1px solid #BBBBBB;">';
	$result .= '<tbody style="border:1px solid #BBBBBB;">';
	$result .= '<tr style="border:inherit; background-color:#808080; letter-spacing:-1px; font-family:verdana; font-size:13px; color:#FFF;">';
	$result .= '<td width="70px" align="center" style="border:inherit;">INV #</td>';
	$result .= '<td width="80px" align="center" style="border:inherit;">CODE</td>';
	$result .= '<td width="35px" align="center" style="border:inherit;">O/CD</td>';
	$result .= '<td width="100px" align="center" style="border:inherit;">BARCODE</td>';
	$result .= '<td align="center" style="border:inherit;">ITEM NAME</td>';
	$result .= '<td width="60px" align="center" style="border:inherit;">PRICE</td>';
	$result .= '<td width="40px" align="center" style="border:inherit;">QTY</td>';
	$result .= '<td width="40px" align="center" style="border:inherit;">STK</td>';
	$result .= '<td width="40px" align="center" style="border:inherit;">UNIT</td>';
	$result .= '</tr>';

	if($history_num == 0) {
		$result .= '<tr><td align="center" colspan=9><p><b>구매한 상품이 없습니다.</b></p></td></tr>';
	} else {
		$i = 0;
		while($history_query_row = mssql_fetch_array($history_query_result)) {

			$i++;
			if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
			else				$doc_field_name = "doc_field_purchases";

			$wsCode = $history_query_row['wsCode'];
			$ProdOwnCode = $history_query_row['ProdOwnCode'];
			$prodID = $history_query_row['tProd'];

			$array = getItemName($wsCode,$ProdOwnCode,$CID);

			if(trim($array[1])) {
				$prodName = $array[0]." / ".$array[1];
			} else {
				$prodName = $array[0];
			}
			$itemUnit = $array[4];
			$itemBalance = $array[5];

//			$array = getMaxMinPrice($CID,$CardId,$wsCode,$ProdOwnCode);
//			$array = getMaxPrice($CID,$CardId,$wsCode,$ProdOwnCode);
			if($array[0] == '') $array[0] = 0;

			$result .= '<tr class="'.$doc_field_name.'" style="border:inherit;">';
			$result .= '<td align="left" style="border:inherit; padding-left:3px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$wsCode.'\', \''.$ProdOwnCode.'\', \''.$prodID.'\', \''.$prodName.'\', \''.$history_query_row['prodsize'].'\', \''.$array[0].'\', 0, \''."".'\',false);" style="text-decoration:none; color:#000000">'.$history_query_row['tInvNo'].'</a></td>';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$wsCode.'\', \''.$ProdOwnCode.'\', \''.$prodID.'\', \''.$prodName.'\', \''.$history_query_row['prodsize'].'\', \''.$array[0].'\', 0, \''."".'\',false);" style="text-decoration:none; color:#000000">'.$wsCode.'</a></td>';
			$result .= '<td align="center" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$wsCode.'\', \''.$ProdOwnCode.'\', \''.$prodID.'\', \''.$prodName.'\', \''.$history_query_row['prodsize'].'\', \''.$array[0].'\', 0, \''."".'\',false);" style="text-decoration:none; color:#000000">'.$ProdOwnCode.'</a></td>';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$wsCode.'\', \''.$ProdOwnCode.'\', \''.$prodID.'\', \''.$prodName.'\', \''.$history_query_row['prodsize'].'\', \''.$array[0].'\', 0, \''."".'\',false);" style="text-decoration:none; color:#000000">'.$prodID.'</a></td>';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$wsCode.'\', \''.$ProdOwnCode.'\', \''.$prodID.'\', \''.$prodName.'\', \''.$history_query_row['prodsize'].'\', \''.$array[0].'\', 0, \''."".'\',false);" style="text-decoration:none; color:#000000">'.$prodName.'</a></td>';
			if($_SESSION['ActiveIP'] != 'N')
			{
				$result .= '<td align="right" style="border:inherit; padding-right:5px;">'.sprintf("%.2f", $history_query_row['tOUprice']).'</td>';
			}else{
				$result .= '<td align="right" style="border:inherit; padding-right:5px;"></td>';
			}		
			$result .= '<td align="right" style="border:inherit; padding-right:5px;">'.$history_query_row['tQty'].'</td>';
			$result .= '<td align="right" style="border:inherit; padding-right:5px;">'.$itemBalance.'</td>';
			$result .= '<td align="center" style="border:inherit;">'.$itemUnit.'</td>';
			$result .= '</tr>';
		}
	}
	$result .= '</tbody></table>';

	echo $result;
} else if($mode == "priceQty") {

	$customer = $_GET['customer'];

	$query = "SELECT TOP 10 tOrdNo, tOUprice, tQty ".
			 "FROM trOrderDetail ".
			 "WHERE tCust = '$customer' AND tProd = '$key' ".
			 "ORDER BY tOrdNo DESC";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
?>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<table width="100%" style="border-collapse:collapse;">
		<tr style="background-color:#CCEEFF;  letter-spacing:-1px; font-family:verdana; font-size:13px;">
			<td width="100px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#0000cc">ORDER #</td>
			<td width="80px" align="center" style="border:1px solid #BBBBBB; border-right:0; color:#0000cc">PRICE</td>
			<td width="80px" align="center" style="border:1px solid #BBBBBB; color:#0000cc">QTY</td>
		</tr>

		<? if($row_num == 0) { ?>
			<tr>
				<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>
			</tr>
		<? } else { ?>
			<? $i = 0; ?>
			<? while($row = mssql_fetch_array($query_result)) { ?>
				<? 
					$i++;
					if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
					else				$doc_field_name = "doc_field_purchases";
				?>
				<tr class="<?=$doc_field_name?>">
					<td align="center" style="border:1px solid #BBBBBB; border-right:0"><?=$row['tOrdNo']; ?></a></td>
					<td align="right" style="border:1px solid #BBBBBB; border-right:0; padding-right:5px;"><?=sprintf("%.2f", $row['tOUprice']); ?></td>
					<td align="right" style="border:1px solid #BBBBBB; padding-right:5px;"><?=$row['tQty']; ?></td>
				</tr>
			<? } ?>
		<? } ?>
	</table>
<?
} else {		//Item All
	$key = str_replace(" ", "", $key);
	$query = "SELECT wsCode, ProdOwnCode, prodID, prodKname, prodName, prodUnit, prodsize, prodBalance ".
			 "FROM Inventory_Item ".
			 "WHERE (replace(prodKname,' ','')  LIKE '%$key%' OR replace(prodName,' ','')  LIKE '%$key%' OR wsCode LIKE '%$key%') AND CID ='".$CID."' AND useYN='Y' ".
			 "ORDER BY prodKname ASC";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);

	$result = "<link rel='stylesheet' type='text/css' href='css/style.css'/>";
	$result .= '<table width="679px" style="border-collapse:collapse; overflow:scroll; border:1px solid #BBBBBB;">';
	$result .= '<tbody style="border:1px solid #BBBBBB;">';

	if($row_num == 0) {
		$result .= '<tr><td align="center"><p><b>검색된 결과가 없습니다.</b></p></td></tr>';
	} else {
		$result .= '<tr style="background-color:#808080; letter-spacing:-1px; font-family:verdana; font-size:13px; border:inherit;">';
		$result .= '<td width="110px" align="center" style="border:inherit; color:#ffffff">ITEM CD</td>';
		$result .= '<td width="35px" align="center" style="border:inherit; color:#ffffff">O/CD</td>';
		$result .= '<td width="100px" align="center" style="border:inherit; color:#ffffff">BARCODE</td>';
		$result .= '<td align="center" style="border:inherit; color:#ffffff">ITEM NAME</td>';
		$result .= '<td width="30px" align="center" style="border:inherit; color:#ffffff">P(Hi)</td>';
		$result .= '<td width="30px" align="center" style="border:inherit; color:#ffffff">P(Lo)</td>';
		$result .= '<td width="40px" align="center" style="border:inherit; color:#ffffff">STK</td>';
		$result .= '<td align="center" style="border:inherit; color:#ffffff">SIZE</td>';
		$result .= '<td width="50px" align="center" style="border:inherit; color:#ffffff">UNIT</td>';
		$result .= '</tr>';

		$i = 0;
		while($row = mssql_fetch_array($query_result)) {
			$i++;
			if ($i % 2 == 0)	$doc_field_name = "doc_field_purchases_bg";
			else				$doc_field_name = "doc_field_purchases";

			$wsCode = $row['wsCode'];
			$ProdOwnCode = $row['ProdOwnCode'];

//			$prodName = getDescription($wsCode,$ProdOwnCode,$CID);
			if(trim($row['prodName'])) {
				$prodName = Br_iconv($row['prodKname'])." / ".$row['prodName'];
			} else {
				$prodName = Br_iconv($row['prodKname']);
			}

//			$array = getMaxMinPrice($CID,$CardId,$wsCode,$ProdOwnCode);
			$array = getMaxPrice($CID,$CardId,$wsCode,$ProdOwnCode);
			if($array[0] == '') $array[0] = 0;

			$result .= '<tr class="'.$doc_field_name.'" style="border:inherit;">';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$row['wsCode'].'\', \''.$row['ProdOwnCode'].'\', \''.$row['prodID'].'\', \''.$prodName.'\', \''.$row['prodsize'].'\', \''.$array[0].'\', 0,\''."".'\',false);" style="text-decoration:none; color:#000000">'.$wsCode.'</a></td>';
			$result .= '<td align="center" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$row['wsCode'].'\', \''.$row['ProdOwnCode'].'\', \''.$row['prodID'].'\', \''.$prodName.'\', \''.$row['prodsize'].'\', \''.$array[0].'\', 0,\''."".'\',false);" style="text-decoration:none; color:#000000">'.$ProdOwnCode.'</a></td>';
			$result .= '<td align="left" style="border:inherit; border-right:0; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$row['wsCode'].'\', \''.$row['ProdOwnCode'].'\', \''.$row['prodID'].'\', \''.$prodName.'\', \''.$row['prodsize'].'\', \''.$array[0].'\', 0,\''."".'\',false);" style="text-decoration:none; color:#000000">'.$row['prodID'].'</a></td>';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;"><a href="javascript:select_item(\''.$tStatus.'\', \''.$row['wsCode'].'\', \''.$row['ProdOwnCode'].'\', \''.$row['prodID'].'\', \''.$prodName.'\', \''.$row['prodsize'].'\', \''.$array[0].'\', 0,\''."".'\',false);" style="text-decoration:none; color:#000000"">'.$prodName.'</a></td>';
			if($_SESSION['ActiveIP'] != 'N')
			{
				$result .= '<td align="left" style="border:inherit; padding-left:5px;">'.$array[0].'</td>';
				$result .= '<td align="left" style="border:inherit; padding-left:5px;">'.$array[1].'</td>';
			}else{
				$result .= '<td align="left" style="border:inherit; padding-left:5px;"></td>';
				$result .= '<td align="left" style="border:inherit; padding-left:5px;"></td>';
			}		
			$result .= '<td align="right" style="border:inherit; padding-right:5px;">'.$row['prodBalance'].'</td>';
			$result .= '<td align="left" style="border:inherit; padding-left:5px;">'.$row['prodsize'].'</td>';
			$result .= '<td align="center" style="border:inherit;">'.$row['prodUnit'].'</td>';
			$result .= '</tr>';
		}
	}

	$result .= '</tbody></table>';

	echo $result;
}

mssql_close();
?>
