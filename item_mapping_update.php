<?php
	session_start();
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$sID = $_SESSION['staffID'];
	$coId = $_SESSION['staffCID'];

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$page_no = ($_GET['page_no']) ? $_GET['page_no'] : $_POST['page_no'];

	$Barcode = ($_GET['Barcode']) ? $_GET['Barcode'] : $_POST['Barcode'];
	$Unit = ($_GET['Unit']) ? $_GET['Unit'] : $_POST['Unit'];
	$Up_Barcode = ($_GET['Up_Barcode']) ? $_GET['Up_Barcode'] : $_POST['Up_Barcode'];
	$Up_Unit = ($_GET['Up_Unit']) ? $_GET['Up_Unit'] : $_POST['Up_Unit'];

	$Up_GalCode = ($_GET['Up_GalCode']) ? $_GET['Up_GalCode'] : $_POST['Up_GalCode'];
	$Up_ProdOwnCode = ($_GET['Up_ProdOwnCode']) ? $_GET['Up_ProdOwnCode'] : $_POST['Up_ProdOwnCode'];
	
	$contenteach = ($_GET['contenteach']) ? $_GET['contenteach'] : $_POST['contenteach'];

	$toDay = date("Y-m-d");
	$toTime = date("H:i:s");
	
	if($mode == "append")
	{
		$Query = "INSERT INTO tfBarcodeLink (CID,Barcode,Unit,Up_wsCode,Up_ProdOwnCode,Up_Barcode,Up_Unit,Up_Inside_qty ".
					") VALUES ('".
					$coId."','".
					$Barcode."','".
					$Unit."','".
					$Up_GalCode."','".
					$Up_ProdOwnCode."','".
					$Up_Barcode."','".
					$Up_Unit."',".
					$contenteach." ".
					")";
		mssql_query($Query);
		echo $Query;
	}

	else if($mode == "update")
	{
		$Query = "UPDATE tfBarcodeLink SET ".
			"Unit='".$Unit."',".
			"Up_wsCode='".$Up_GalCode."',".
			"Up_ProdOwnCode='".$Up_ProdOwnCode."',".
			"Up_Barcode='".$Up_Barcode."',".
			"Up_Unit='".$Up_Unit."',".
			"Up_Inside_qty=".$contenteach." ".
			"WHERE CID='".$coId."' AND Barcode='".$Barcode."' ";

//		echo $Query;
//				"WHERE CID='".$sID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' AND SuppCode='".$SuppCode."' 
		mssql_query($Query);
	}
	
	else if($mode == "delete")
	{
			$Query = "DELETE FROM tfBarcodeLink ".
							"WHERE CID='".$coId."' AND Barcode='".$Barcode."' ";
			mssql_query($Query);
//echo $Query;
	}
	
	else
	{
		echo ("<script>alert('작업을 선택해 주세요.');</script>");
		echo ("<script>history.back(-1);</script>");
	}

	if($getOwnCode)	$ProdOwnCode = $getOwnCode;
?>
<script>
	document.location.href="item_mapping.php?mode=1&Barcode=<?=$Barcode?>&page_no=<?=$page_no?>";
</script>