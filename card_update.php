<?php
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$cId = ($_GET['cId']) ? $_GET['cId'] : $_POST['cId'];
	$cardid = ($_GET['cardid']) ? $_GET['cardid'] : $_POST['cardid'];
	$name = ($_GET['name']) ? $_GET['name'] : $_POST['name'];
	$phoneno = ($_GET['phoneno']) ? $_GET['phoneno'] : $_POST['phoneno'];
	$stype = ($_GET['stype']) ? $_GET['stype'] : $_POST['stype'];
	$sstatus = ($_GET['sstatus']) ? $_GET['sstatus'] : $_POST['sstatus'];

	if($stype == "Customer")		$strType = 1;
	else if($stype == "Vendor")		$strType = 2;
	else if($stype == "Employee")	$strType = 3;
	else if($stype == "Bank")		$strType = 4;
	else							$strType = 0;

	if($mode == "delete")
	{
		$Query = "DELETE FROM Card ".
				 "WHERE CID='".$cId."' AND CardType =".$strType." AND CardID=".$cardid;
		mssql_query($Query);
	}
	else
	{
		if ($name == "") {
			echo ("<script>alert('상호명 또는 성명을 입력하세요.');</script>");
			echo ("<script>history.back(-1);</script>");
		}
		$terms = ($_GET['terms']) ? $_GET['terms'] : $_POST['terms'];
		$limit = ($_GET['limit']) ? $_GET['limit'] : $_POST['limit'];
		if ($terms == "") $terms = 0;
		if ($limit == "") $limit = 0;

		$CustomList1 = ($_GET['CustomList1']) ? $_GET['CustomList1'] : $_POST['CustomList1'];
		$CustomList2 = ($_GET['CustomList2']) ? $_GET['CustomList2'] : $_POST['CustomList2'];
		$CustomList3 = ($_GET['CustomList3']) ? $_GET['CustomList3'] : $_POST['CustomList3'];
		$CustomField1 = ($_GET['CustomField1']) ? $_GET['CustomField1'] : $_POST['CustomField1'];
		$CustomField2 = ($_GET['CustomField2']) ? $_GET['CustomField2'] : $_POST['CustomField2'];
		$CustomField3 = ($_GET['CustomField3']) ? $_GET['CustomField3'] : $_POST['CustomField3'];

		$today = date("Y-m-d H:i:s");
		
		if($cardid =="" && readCardName($cId,$name,$strType) == "")
		{
			$strCardID = getCardID($cId,$strType);

			$Query = "INSERT INTO Card (CID,CardType,CardID,Name,Phone,cType,Status,cTerm,cLimit,CustomList1,CustomList2,CustomList3,".
						"CustomField1,CustomField2,CustomField3,regDate) VALUES ('".
						$cId."',".
						$strType.",".
						$strCardID.",'".
						Br_dconv(trim($name))."','".
						$phoneno."','".
						$stype."','".
						$sstatus."',".
						trim($terms).",".
						trim($limit).",'".
						Br_dconv($CustomList1)."','".
						Br_dconv($CustomList2)."','".
						Br_dconv($CustomList3)."','".
						Br_dconv($CustomField1)."','".
						Br_dconv($CustomField2)."','".
						Br_dconv($CustomField3)."','".
						$today."')";

			mssql_query($Query);
		}
		else if(readCard($cId,$cardid,$strType))
		{
			$Query = "UPDATE Card SET ".
				"Name='".Br_dconv($name)."',".
				"Phone='".$phoneno."',".
				"cType='".$stype."',".
				"Status='".$sstatus."',".
				"cTerm=".trim($terms).",".
				"cLimit=".trim($limit).",".
				"CustomList1='".Br_dconv($CustomList1)."',".
				"CustomList2='".Br_dconv($CustomList2)."',".
				"CustomList3='".Br_dconv($CustomList3)."',".
				"CustomField1='".Br_dconv($CustomField1)."',".
				"CustomField2='".Br_dconv($CustomField2)."',".
				"CustomField3='".Br_dconv($CustomField3)."',".
				"regDate='".$today."' ".
				"WHERE CID='".$cId."' AND CardType =".$strType." AND CardID=".$cardid;
				mssql_query($Query);
//echo $Query;
		}
	}
?>
<script>
	history.back(-1);
</script>