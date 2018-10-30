<?php
	include_once "includes/db_configms.php";

	$sDate = ($_GET['sDate']) ? $_GET['sDate'] : $_POST['sDate'];
	$sInvoNo = ($_GET['sInvoNo']) ? $_GET['sInvoNo'] : $_POST['sInvoNo'];
	$cId = ($_GET['cId']) ? $_GET['cId'] : $_POST['cId'];
	$tID = ($_GET['tID']) ? $_GET['tID'] : $_POST['tID'];
	$sPrice = ($_GET['sPrice']) ? $_GET['sPrice'] : $_POST['sPrice'];

	$Query = "Select tQty, tTax FROM tfTemp ".
			"WHERE CID='".$cId."' AND tDate = '".$sDate."' AND tInvNo=".$sInvoNo." AND tID=".$tID;
	$rst = mssql_query($Query);
	$row = mssql_fetch_array($rst);
	
	$tQty = $row['tQty'];
	$tTax = $row['tTax'];
	$tAmt = sprintf("%.2f", $tQty * $sPrice);

	if ($tTax == "B") {
		$tGst = sprintf("%.2f", $tAmt * 0.05);
		$tPst = sprintf("%.2f", $tAmt * 0.07);
	} else if ($tTax == "G") {
		$tGst = sprintf("%.2f", $tAmt * 0.05);
		$tPst = 0;
	} else {
		$tGst = 0;
		$tPst = 0;
	}

	$Query = "UPDATE tfTemp SET ".
				"tOuprice = ".$sPrice.", ".
				"tGst = ".$tGst.", ".
				"tPst = ".$tPst.", ".
				"tAmt = ".$tAmt." ".
			 "WHERE CID='".$cId."' AND tDate = '".$sDate."' AND tInvNo=".$sInvoNo." AND tID=".$tID;
	mssql_query($Query);
?>