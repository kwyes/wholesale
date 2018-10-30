<link rel="stylesheet" type="text/css" href="css/style.css"/>
<?
session_start();
include_once "includes/db_configms.php";
include_once "includes/common_class.php";

$mode = ($_REQUEST["mode"]) ? $_REQUEST["mode"] : $_GET['mode'];
$key = ($_REQUEST["key"]) ? $_REQUEST["key"] : $_GET['key'];
$cId = ($_GET['cId']) ? $_GET['cId'] : $_POST['cId'];

if($mode == "code" || $mode == "name") {
	$key = Br_dconv($key);
	$query = "SELECT wsCode,ProdOwnCode,prodId,prodKname,prodName,prodsize,prodUnit ".
			 "FROM Inventory_Item ".
			 "WHERE (wsCode LIKE '%$key%' OR prodKname LIKE '%$key%') AND CID='".$cId."' ".
			 "ORDER BY wsCode,ProdOwnCode ASC";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
?>
	<table width="100%" style="border-collapse:collapse;">
		<tr height="20" style="background-color:#6699cc" class="doc_field">
			<td width="90px" align="center">Item CD</td>
			<td width="30px" align="center">O/CD</td>
			<td width="100px" align="center">Item Name</td>
			<td width="70px" align="center">Size</td>
			<td width="40px" align="center">Unit</td>
		</tr>
		<? if($row_num == 0) { ?>
		<tr>
			<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>
		</tr>
		<? } else {
				$i = 0;
				while($row = mssql_fetch_array($query_result)) {
					$i++;
					if ($i % 2 == 0)	$doc_field_name = "doc_field_bg";
					else				$doc_field_name = "doc_field";

					$wsCode = $row['wsCode'];
					$ProdOwnCode = $row['ProdOwnCode'];
/*
					$useYN = $row['useYN'];

					$prodKname = Br_iconv($row['prodKname']);
					$prodName = $row['prodName'];
//					$prodName = str_replace("'", "\'", $row['prodName']);

					$SuppCode = $row['SuppCode'];
					$prodId = $row['prodId'];
					$prodsize = Br_iconv($row['prodsize']);
					$prodUnit = $row['prodUnit'];

					if($row2['prodType']>'0')	{	$stype = $row['prodType'];	}
					else						{ 	$stype = 0;	}
					if($row2['prodType2']>'0')	{	$stype2 = $row['prodType2']; }
					else						{	$stype2 = 0; }

					$prodTax = $row['prodTax'];
					$prodcontenteach = $row['prodcontenteach'];
					$prodDeposit = $row['prodDeposit'];
					$prodSupp = $row['prodSupp'];	//공급처 코드
					$prodBalance = $row['prodBalance'];
					$prodIUprice = $row['prodIUprice'];
					$prodOUprice = $row['prodOUprice'];
					
					$prodNetWeight = number_format($row['prodNetWeight'],2);
					$prodWeight = number_format($row['prodWeight'],2);
					$prodCBM = number_format($row['prodCBM'],4);
					$prodBoxSize = $row['prodBoxSize'];
					$prodDuty = $row['prodDuty'];
					$prodShelfLife = $row['prodShelfLife'];

					$CustomList1 = $row['CustomList1'];
					$CustomList2 = $row['CustomList2'];
					$CustomList3 = $row['CustomList3'];
					$CustomField1 = $row['CustomField1'];
					$CustomField2 = $row['CustomField3'];
					$CustomField3 = $row['CustomField2'];
					$Description = $row['Description'];
					$Currency = $row['Currency'];
*/
		?>
		<tr height="20" class="<?=$doc_field_name?>">
			<td align="left" style="border:0px solid black; border-right:0"><a href="javascript:parent.select_code('<?=$wsCode?>','<?=$ProdOwnCode?>');" style="text-decoration:none;"><?=$row['wsCode'];?></a></td>
			<td align="center"><?=$row['ProdOwnCode'];?></td>
			<td align="left" style="border:0px solid black; border-right:0"><a href="javascript:parent.select_code('<?=$wsCode?>','<?=$ProdOwnCode?>');" style="text-decoration:none;"><?=Br_iconv($row['prodKname']);?></a></td>
			<td align="center"><?=Br_iconv($row['prodsize']);?></td>
			<td align="center"><?=$row['prodUnit'];?></td>
		</tr>
		<?	}
} ?>
	</table>
<?	if($mode == "code") {?>
		<script>parent.document.getElementById("search_code_display").style.display = "";</script>
<?	} else {?>
		<script>parent.document.getElementById("search_name_display").style.display = "";</script>
<?	}
} else if($mode == "upcode" || $mode == "upname") {
	$key = Br_dconv($key);
	$query = "SELECT wsCode,ProdOwnCode,prodId,prodKname,prodName,prodsize,prodUnit ".
			 "FROM Inventory_Item ".
			 "WHERE (wsCode LIKE '%$key%' OR prodKname LIKE '%$key%') AND CID='".$cId."' ".
			 "ORDER BY wsCode,ProdOwnCode ASC";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
?>
	<table width="100%" style="border-collapse:collapse;">
		<tr height="20" style="background-color:#6699cc" class="doc_field">
			<td width="90px" align="center">Item CD</td>
			<td width="30px" align="center">O/CD</td>
			<td width="100px" align="center">Item Name</td>
			<td width="100px" align="center">UPC</td>
			<td width="70px" align="center">Size</td>
			<td width="40px" align="center">Unit</td>
		</tr>
		<? if($row_num == 0) { ?>
		<tr>
			<td align="center" colspan=4><p><b>검색된 결과가 없습니다.</b></p></td>
		</tr>
		<? } else {
				$i = 0;
				while($row = mssql_fetch_array($query_result)) {
					$i++;
					if ($i % 2 == 0)	$doc_field_name = "doc_field_bg";
					else				$doc_field_name = "doc_field";
					
					$prodName = str_replace("'", "\'", $row['prodName']);
				?>
		<tr height="20" class="<?=$doc_field_name?>">
			<td align="left" style="border:0px solid black; border-right:0"><a href="javascript:parent.select_upcode('<?=$row['wsCode'];?>','<?=$row['ProdOwnCode'];?>','<?=$row['prodId'];?>','<?=Br_iconv($row['prodKname']);?>','<?=$prodName;?>','<?=$row['prodUnit'];?>','<?=$mode?>');" style="text-decoration:none;" ><?=$row['wsCode'];?></a></td>
			<td align="center"><?=$row['ProdOwnCode'];?></td>
			<td align="left" style="border:0px solid black; border-right:0"><a href="javascript:parent.select_upcode('<?=$row['wsCode'];?>','<?=$row['ProdOwnCode'];?>','<?=$row['prodId'];?>','<?=Br_iconv($row['prodKname']);?>','<?=$prodName;?>','<?=$row['prodUnit'];?>','<?=$mode?>');" style="text-decoration:none;" ><?=Br_iconv($row['prodKname']);?></a></td>
			<td align="left"><?=$row['prodId'];?></td>
			<td align="center"><?=Br_iconv($row['prodsize']);?></td>
			<td align="center"><?=$row['prodUnit'];?></td>
		</tr>
			<?	}
			} ?>
	</table>
<?
} else if($mode == "Append") {
//	$key=Br_dconv($key);

	$venCode = ($_GET['venCode']) ? $_GET['venCode'] : $_POST['venCode'];
	$InvoiceNo = ($_GET['InvoiceNo']) ? $_GET['InvoiceNo'] : $_POST['InvoiceNo'];
	$sd_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];

	$wsCode = ($_GET['wsCode']) ? $_GET['wsCode'] : $_POST['wsCode'];
	$ProdOwnCode = ($_GET['ProdOwnCode']) ? $_GET['ProdOwnCode'] : $_POST['ProdOwnCode'];
	$SuppCode = ($_GET['SuppCode']) ? $_GET['SuppCode'] : $_POST['SuppCode'];
	$prodId = ($_GET['prodId']) ? $_GET['prodId'] : $_POST['prodId'];
	$prodsize = ($_GET['prodsize']) ? $_GET['prodsize'] : $_POST['prodsize'];
	$prodqty = ($_GET['prodqty']) ? $_GET['prodqty'] : $_POST['prodqty'];
	$vItemCode = ($_GET['vItemCode']) ? $_GET['vItemCode'] : $_POST['vItemCode'];
	$prodUnit = ($_GET['prodUnit']) ? $_GET['prodUnit'] : $_POST['prodUnit'];
	$prodTax = ($_GET['prodTax']) ? $_GET['prodTax'] : $_POST['prodTax'];
	$prodcontenteach = ($_GET['prodcontenteach']) ? $_GET['prodcontenteach'] : $_POST['prodcontenteach'];
	$extendedAmount = ($_GET['extendedAmount']) ? $_GET['extendedAmount'] : $_POST['extendedAmount'];

	if($prodcontenteach == "") $prodcontenteach = 1;
	$prodsize = Br_dconv($prodsize);
	$prodPrice = $extendedAmount / $prodqty;

	$iSeq = get_sdDetail_Seq($sd_date, $cId, $venCode, $InvoiceNo);

	$query = "INSERT INTO stock_detail ".
				"(CID,sd_date,sd_vendor_cd,sd_invoice_no,sd_seq,vItemCode,wsCode,ProdOwnCode,SuppCode,".
					"prodId,prodPrice,prodTax,prodSize,qty,unit,each,extendedAmount ".
			 ") VALUES (".
				 "'$cId','$sd_date',$venCode,'$InvoiceNo',$iSeq,'$vItemCode','$wsCode','$ProdOwnCode','$SuppCode',".
				 "'$prodId',$prodPrice,'$prodTax','$prodsize',$prodqty,'$prodUnit',$prodcontenteach,$extendedAmount )";
	$query_result = mssql_query($query);

	if($query_result) {
		$ProdBal = getInventory($wsCode,$ProdOwnCode,$SuppCode,$cId);
		$query = "UPDATE mfProd SET ".
					"prodBal=".($ProdBal+$prodqty)." ".
				 "WHERE wsCode='$wsCode' AND ProdOwnCode='$ProdOwnCode' AND SuppCode='$SuppCode' AND CID='$cId' ";
		$saveTran_result = mssql_query($query);
		if(!$saveTran_result) {
			$query = "DELETE FROM stock_detail ".
					 "WHERE CID='$cId' AND wsCode='$wsCode' AND ProdOwnCode='$ProdOwnCode' AND SuppCode='$SuppCode' AND sd_seq=$iSeq ";
			$saveTran_delete = mssql_query($query);
		}

		$RegPassword = $_SESSION['staffID'];
		$RegPerson = $_SESSION['staffName'];
		$toTime = date("H:i:s");
		
		$rCnt = CountRecord($sd_date,$venCode,$InvoiceNo,$cId);

		if($rCnt == 0) {
			$query = "INSERT INTO stock_master ".
						"(CID,sm_date,sm_vendor_cd,sm_invoice_no,Total_Amt,RegDate,RegTime,RegPassword,RegPerson ".
					 ") VALUES (".
						 "'$cId','$sd_date',$venCode,'$InvoiceNo',$extendedAmount,'$sd_date','$toTime','$RegPassword','$RegPerson' )";
			$query_result = mssql_query($query);

		} else {
			$TotalAmt = SumStockTotalAmt($sd_date,$venCode,$InvoiceNo,$cId);

			$query = "UPDATE stock_master SET ".
						"Total_Amt=".$TotalAmt." ".
					 "WHERE CID='$cId' AND sm_date='$sd_date' AND sm_invoice_no='$InvoiceNo' AND sm_vendor_cd=$venCode ";
			$query_result = mssql_query($query);
		}
	}
?>
	<script language="javascript">
		top.document.location.reload(); 
	</script>
<?
} else if($mode == "Stock_Delete") {

	$venCode = ($_GET['venCode']) ? $_GET['venCode'] : $_POST['venCode'];
	$InvoiceNo = ($_GET['InvoiceNo']) ? $_GET['InvoiceNo'] : $_POST['InvoiceNo'];
	$sd_date = ($_GET['target_date']) ? $_GET['target_date'] : $_POST['target_date'];


	$wsCode = ($_GET['wsCode']) ? $_GET['wsCode'] : $_POST['wsCode'];
	$ProdOwnCode = ($_GET['ProdOwnCode']) ? $_GET['ProdOwnCode'] : $_POST['ProdOwnCode'];
	$SuppCode = ($_GET['SuppCode']) ? $_GET['SuppCode'] : $_POST['SuppCode'];
	$seq = ($_GET['seq']) ? $_GET['seq'] : $_POST['seq'];

	$query = "DELETE FROM stock_detail ".
			 "WHERE CID='$cId' AND sd_date='$sd_date' AND sd_invoice_no='$InvoiceNo' AND sd_vendor_cd=$venCode AND sd_seq=$seq";
	$query_result = mssql_query($query);

	if($query_result) {
		
		$rCnt = CountRecord($sd_date,$venCode,$InvoiceNo,$cId);

		if($rCnt != 0) {
			$TotalAmt = SumStockTotalAmt($sd_date,$venCode,$InvoiceNo,$cId);
			$query = "UPDATE stock_master SET ".
						"Total_Amt=".$TotalAmt." ".
					 "WHERE CID='$cId' AND sm_date='$sd_date' AND sm_invoice_no='$InvoiceNo' AND sm_vendor_cd=$venCode ";
			$query_result = mssql_query($query);
			echo $query;
		} else {
			$query = "DELETE FROM stock__master ".
					 "WHERE CID='$cId' AND sm_date='$sd_date' AND sm_invoice_no='$InvoiceNo' AND sm_vendor_cd=$venCode ";
			$query_result = mssql_query($query);
		}
	}
?>
	<script language="javascript">
		top.document.location.reload(); 
	</script>
<?
}
?>