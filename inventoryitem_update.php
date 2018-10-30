<?php
	session_start();
	include_once "includes/db_configms.php";
	include_once "includes/common_class.php";

	$sID = $_SESSION['staffID'];
	$cID = $_SESSION['staffCID'];
	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
//echo "mode : ".$mode."</br>";
	$wsCode = ($_GET['wsCode']) ? $_GET['wsCode'] : $_POST['wsCode'];
	$ProdOwnCode = ($_GET['ProdOwnCode']) ? $_GET['ProdOwnCode'] : $_POST['ProdOwnCode'];
	$SuppCode = ($_GET['SuppCode']) ? $_GET['SuppCode'] : $_POST['SuppCode'];
	$prodId = ($_GET['prodId']) ? $_GET['prodId'] : $_POST['prodId'];
	$prodSupp = ($_GET['prodSupp']) ? $_GET['prodSupp'] : $_POST['prodSupp'];
	$prodKname = ($_GET['prodKname']) ? $_GET['prodKname'] : $_POST['prodKname'];
	$prodName = ($_GET['prodName']) ? $_GET['prodName'] : $_POST['prodName'];
	$stype = ($_GET['stype']) ? $_GET['stype'] : $_POST['stype'];
	$stype2 = ($_GET['stype2']) ? $_GET['stype2'] : $_POST['stype2'];
	$prodTax = ($_GET['prodTax']) ? $_GET['prodTax'] : $_POST['prodTax'];
	$prodUnit = ($_GET['prodUnit']) ? $_GET['prodUnit'] : $_POST['prodUnit'];
	$prodIUprice = ($_GET['prodIUprice']) ? $_GET['prodIUprice'] : $_POST['prodIUprice'];
	$prodOUprice = ($_GET['prodOUprice']) ? $_GET['prodOUprice'] : $_POST['prodOUprice'];
	$prodsize = ($_GET['prodsize']) ? $_GET['prodsize'] : $_POST['prodsize'];
	$prodcontenteach = ($_GET['prodcontenteach']) ? $_GET['prodcontenteach'] : $_POST['prodcontenteach'];
//	$prodBalance = ($_GET['prodBalance']) ? $_GET['prodBalance'] : $_POST['prodBalance'];

	$prodDeposit = ($_GET['prodDeposit']) ? $_GET['prodDeposit'] : $_POST['prodDeposit'];
	$useYN = ($_GET['useYN']) ? $_GET['useYN'] : $_POST['useYN'];
	if(trim($useYN) == "") $useYN = 'Y';
	$prodNetWeight = ($_GET['prodNetWeight']) ? $_GET['prodNetWeight'] : $_POST['prodNetWeight'];
	$prodWeight = ($_GET['prodWeight']) ? $_GET['prodWeight'] : $_POST['prodWeight'];
	$prodCBM = ($_GET['prodCBM']) ? $_GET['prodCBM'] : $_POST['prodCBM'];
	$prodDuty = ($_GET['prodDuty']) ? $_GET['prodDuty'] : $_POST['prodDuty'];
	$prodBoxSize = ($_GET['prodBoxSize']) ? $_GET['prodBoxSize'] : $_POST['prodBoxSize'];
	$prodShelfLife = ($_GET['prodShelfLife']) ? $_GET['prodShelfLife'] : $_POST['prodShelfLife'];

	$CustomList1 = ($_GET['CustomList1']) ? $_GET['CustomList1'] : $_POST['CustomList1'];
	$CustomList2 = ($_GET['CustomList2']) ? $_GET['CustomList2'] : $_POST['CustomList2'];
	$CustomList3 = ($_GET['CustomList3']) ? $_GET['CustomList3'] : $_POST['CustomList3'];
	$CustomField1 = ($_GET['CustomField1']) ? $_GET['CustomField1'] : $_POST['CustomField1'];
	$CustomField2 = ($_GET['CustomField2']) ? $_GET['CustomField2'] : $_POST['CustomField2'];
	$CustomField3 = ($_GET['CustomField3']) ? $_GET['CustomField3'] : $_POST['CustomField3'];
	$prodCurrency = ($_GET['prodCurrency']) ? $_GET['prodCurrency'] : $_POST['prodCurrency'];
	$Description = ($_GET['Description']) ? $_GET['Description'] : $_POST['Description'];

	if($prodSupp == "")			$prodSupp = 0;
	if($prodIUprice == "")		$prodIUprice = 0;
	if($prodOUprice == "")		$prodOUprice = 0;
//	if($prodBalance == "")		$prodBalance = 0;
	if($prodcontenteach == "")	$prodcontenteach = 0;
	if($prodNetWeight == "")	$prodNetWeight = 0;
	if($prodWeight == "")		$prodWeight = 0;
	if($prodCBM == "")			$prodCBM = 0;
	if($prodDuty == "")			$prodDuty = 0;
	if($prodShelfLife == "")	$prodShelfLife = 0;

	$prodNetWeight = trim($prodNetWeight);
	$prodWeight = trim($prodWeight);
	$prodCBM = trim($prodCBM);
	$prodName = str_replace("\'", "''", $prodName);

	$toDay = date("Y-m-d");
	$toTime = date("H:i:s");
	
	$currentTab = "tab1";

	if($prodDeposit == "") $prodDeposit = 0;

	if($mode == "append")
	{
		$getProdType2 = getProdType2($cID,$stype,$stype2);
		$getOwnCode = getProdOwnCode($cID,$wsCode);

		$Query = "INSERT INTO Inventory_Item (CID,wsCode,ProdOwnCode,SuppCode,prodId,prodKname,prodName,prodType,prodType2,".
					"prodSupp,prodIUprice,prodOUprice,prodUnit,prodsize,prodcontenteach,prodTax,prodDeposit,useYN,".
					"prodNetWeight,prodWeight,prodCBM,prodDuty,prodBoxSize,prodShelfLife,prodCurrency,".
					"CustomList1,CustomList2,CustomList3,CustomField1,CustomField2,CustomField3,Description, ".
					"LastModPerson,LastModDate,LastModTime,prodBalance,OnHand,myobOnHand,myobAvgCost) VALUES ('".
					$cID."','".
					$wsCode."','".
					$getOwnCode."','".
					trim($SuppCode)."','".
					trim($prodId)."','".
					trim(Br_dconv($prodKname))."','".
					trim($prodName)."','".
					$stype."','".
					$getProdType2."',".
					$prodSupp.",".
					$prodIUprice.",".
					$prodOUprice.",'".
					$prodUnit."','".
					trim(Br_dconv($prodsize))."',".
					$prodcontenteach.",'".
					$prodTax."',".
					$prodDeposit.",'".
					$useYN."',".
					$prodNetWeight.",".
					$prodWeight.",".
					$prodCBM.",".
					$prodDuty.",'".
					$prodBoxSize."',".
					$prodShelfLife.",'".
					$prodCurrency."','".
					$CustomList1."','".
					$CustomList2."','".
					$CustomList3."','".
					$CustomField1."','".
					$CustomField2."','".
					$CustomField3."','".
					$Description."','".
					$sID."','".
					$toDay."','".
					$toTime."',0,0,0,0 )";
		mssql_query($Query);
	}

	else if($mode == "update")
	{
		$getProdType2 = getProdType2($cID,$stype,$stype2);
		if($useYN == "") $useYN = "Y";

		$Query = "UPDATE Inventory_Item SET ".
			"SuppCode='".trim($SuppCode)."',".
			"prodId='".trim($prodId)."',".
			"prodKname='".trim(Br_dconv($prodKname))."',".
			"prodName='".trim($prodName)."',".
			"prodType='".$stype."',".
			"prodType2='".$getProdType2."',".
			"prodSupp=".$prodSupp.",".
			"prodIUprice=".$prodIUprice.",".
			"prodOUprice=".$prodOUprice.",".
			"prodUnit='".$prodUnit."',".
			"prodsize='".trim(Br_dconv($prodsize))."',".
			"prodcontenteach='".$prodcontenteach."',".
			"prodTax='".$prodTax."',".
			"prodDeposit=".$prodDeposit.",".
			"useYN='".$useYN."',".
			"prodNetWeight=".$prodNetWeight.",".
			"prodWeight=".$prodWeight.",".
			"prodCBM=".$prodCBM.",".
			"prodDuty=".$prodDuty.",".
			"prodBoxSize='".$prodBoxSize."',".
			"prodShelfLife=".$prodShelfLife.",".
			"prodCurrency='".$prodCurrency."',".
			"CustomList1='".$CustomList1."',".
			"CustomList2='".$CustomList2."',".
			"CustomList3='".$CustomList3."',".
			"CustomField1='".$CustomField1."',".
			"CustomField2='".$CustomField2."',".
			"CustomField3='".$CustomField3."',".
			"Description='".$Description."',".
			"LastModPerson='".$sID."',".
			"LastModDate='".$toDay."',".
			"LastModTime='".$toTime."' ".
			"WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";

		mssql_query($Query);
	}
	
	else if($mode == "delete")
	{
			$Query = "DELETE FROM Inventory_Item ".
			"WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
			mssql_query($Query);
?>
			<script>
				document.location.href="inventoryitem.php";
			</script>
<?
	}
	else if($mode == "delete_image")
	{
		$currentTab = "tab2";
		$imgNo = ($_GET['currentImage']) ? $_GET['currentImage'] : $_POST['currentImage'];
		// 저장된 이미지를 가져옴.
		$Query_type = "SELECT image1,image2,image3 FROM Inventory_Item WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
		$rst = mssql_query($Query_type);
		$row = mssql_fetch_array($rst);
		if($imgNo == '1') { // 첫번째 이미지
			rename('upload/ItemImages/'.$row['image1'],'upload/ItemImages/DeleteImages/'.$row['image1']);
			$Query_type = "UPDATE Inventory_Item SET image1='' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
			$rst = mssql_query($Query_type);
		} else if($imgNo == '2') { // 두번째 이미지
			rename('upload/ItemImages/'.$row['image2'],'upload/ItemImages/DeleteImages/'.$row['image2']);
			$Query_type = "UPDATE Inventory_Item SET image2='' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
//echo $Query_type;
			$rst = mssql_query($Query_type);
		} else if($imgNo == '3') { // 세번째 이미지
			rename('upload/ItemImages/'.$row['image3'],'upload/ItemImages/DeleteImages/'.$row['image3']);
			$Query_type = "UPDATE Inventory_Item SET image3='' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
//echo $Query_type;
			$rst = mssql_query($Query_type);
		}
//echo "<script> alert('pause'); </script>";
	}
	else if($mode == "upload")
	{
		$currentTab = "tab2";
		$imgNo = ($_GET['currentImage']) ? $_GET['currentImage'] : $_POST['currentImage'];

		
		if(isset($_FILES["uploadimg"])){ // image upload
			if($_FILES["uploadimg"]["error"] > 0) { // error check
				die ("Upload failed with error code ".$_FILES["uploadimg"]["error"]);
			} else if($_FILES["uploadimg"]["size"] > 5000000) { // file size check < 5M
				echo "<script> alert('File size(".$_FILES["uploadimg"]["size"].") exceeded the limit(5MB)'); </script>";
			} else {
				// 파일이름 변경
				$_FILES["uploadimg"]["name"] = $wsCode."_".$ProdOwnCode."_image".$imgNo."_".$_FILES["uploadimg"]["name"];

				// 저장된 이미지를 가져옴.
				$Query_type = "SELECT image1,image2,image3 FROM Inventory_Item WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
				$rst = mssql_query($Query_type);
				$row = mssql_fetch_array($rst);
				
				if($imgNo == '1') { // 첫번째 이미지
					if(trim($row['image1']) != ""){ // 기존 이미지가 있을 경우 먼저 기존이미지 폴더옮김.
						rename('upload/ItemImages/'.$row['image1'],'upload/ItemImages/DeleteImages/'.$row['image1']);
					}
					// 업로드된 이미지 저장
					move_uploaded_file($_FILES["uploadimg"]["tmp_name"],"upload/ItemImages/".$_FILES["uploadimg"]["name"]);
					// 업로드된 이미지로 DB 업데이트
					$Query_type = "UPDATE Inventory_Item SET image1='".$_FILES["uploadimg"]["name"]."' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
//echo $Query_type;
					$rst = mssql_query($Query_type);

				} else if ($imgNo == '2') { // 두번째 이미지

					if(trim($row['image2']) != ""){ // 기존 이미지가 있을 경우 먼저 기존이미지 폴더옮김.
						rename('upload/ItemImages/'.$row['image2'],'upload/ItemImages/DeleteImages/'.$row['image1']);
					}
					// 업로드된 이미지 저장
					move_uploaded_file($_FILES["uploadimg"]["tmp_name"],"upload/ItemImages/".$_FILES["uploadimg"]["name"]);
					// 업로드된 이미지로 DB 업데이트
					$Query_type = "UPDATE Inventory_Item SET image2='".$_FILES["uploadimg"]["name"]."' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
//echo $Query_type;
					$rst = mssql_query($Query_type);

				} else if ($imgNo == '3') { // 세번째 이미지

					if(trim($row['image3']) != ""){ // 기존 이미지가 있을 경우 먼저 기존이미지 폴더옮김.
						rename('upload/ItemImages/'.$row['image3'],'upload/ItemImages/DeleteImages/'.$row['image3']);
					}
					// 업로드된 이미지 저장
					move_uploaded_file($_FILES["uploadimg"]["tmp_name"],"upload/ItemImages/".$_FILES["uploadimg"]["name"]);
					// 업로드된 이미지로 DB 업데이트
					$Query_type = "UPDATE Inventory_Item SET image3='".$_FILES["uploadimg"]["name"]."' WHERE CID='".$cID."' AND wsCode='".$wsCode."' AND ProdOwnCode='".$ProdOwnCode."' ";
//echo $Query_type;
					$rst = mssql_query($Query_type);

				} else { // error
					echo "<script> alert('Abnormal usage!!'); </script>";
				}
			}
		}
	}
	else if($mode == "uploaddoc")
	{
		$currentTab = "tab2";
		if(isset($_FILES["uploaddocfile"])){ // doc upload
			$doc = ($_GET['currentDoc']) ? $_GET['currentDoc'] : $_POST['currentDoc'];

			if($_FILES["uploaddocfile"]["error"] > 0) { // error check
				die ("Upload failed with error code ".$_FILES["uploaddoc"]["error"]);
			} else if($_FILES["uploaddocfile"]["size"] > 5000000) { // file size check < 5M
				echo "<script> alert('File size(".$_FILES["uploaddocfile"]["size"].") exceeded the limit(5MB)'); </script>";
			} else {
				// 1.파일이름 변경
				$fname = $wsCode."_".$ProdOwnCode."_Doc_".$_FILES["uploaddocfile"]["name"];
				// 2.동일 이름이 존재하는지 확인후 있으면 2-1, 없으면 2-2
				$found = false;
				foreach(scandir("./upload/ItemDocs/") as $filename)
				{
					if($filename == $fname) {
						$found = true;
						break;;
					}
				}
			
				if($found)
				{
					// 2-1. javascript로 동일 이름이 있음을 알리고 종료
					echo "<script> alert('동일 이름이 존재합니다.'); </script>";
				} else {
					// 2-2. 파일을 저장위치에 저장하고 종료
					move_uploaded_file($_FILES["uploaddocfile"]["tmp_name"],"upload/ItemDocs/".$fname);
				}
			}
		}
	}
	else if($mode == "deletedoc")
	{
		$currentTab = "tab2";
		$doc = ($_GET['currentDoc']) ? $_GET['currentDoc'] : $_POST['currentDoc'];
		if(trim($doc) == "") return;

		if(file_exists("./upload/ItemDocs/".$doc))
		{
			unlink('upload/ItemDocs/'.$doc);
		} else {
			echo "<script> alert('파일이 존재하지 않습니다.'); </script>";
		}
	}
	else if($mode == "downdoc")
	{
		$currentTab = "tab2";
		$doc = ($_GET['currentDoc']) ? $_GET['currentDoc'] : $_POST['currentDoc'];
		$filepath = "./upload/ItemDocs/".$doc;
		$filesize = filesize($filepath);
		$filename = Br_iconv($filepath);

		header("Pragma: public");
		header("Expires: 0");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Length: $filesize");

		readfile($filepath);
	}
	else
	{
		echo ("<script>alert('작업을 선택해 주세요.($mode)');</script>");
		echo ("<script>history.back(-1);</script>");
	}

	if($getOwnCode)	$ProdOwnCode = $getOwnCode;
?>
<script>
	//alert("pause");
	window.opener.location.reload(true);
	document.location.href="inventoryitem.php?wsCode=<?=$wsCode?>&ProdOwnCode=<?=$ProdOwnCode?>&currentTab=<?=$currentTab?>";
//	history.back(-1);
</script>
