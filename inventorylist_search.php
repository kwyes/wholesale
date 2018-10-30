<?
session_start();
$cId = $_SESSION['staffCID'];

include "includes/common_class.php";

$mode = ($_REQUEST["mode"]) ? $_REQUEST["mode"] : $_GET['mode'];
$opt = ($_REQUEST["opt"]) ? $_REQUEST["opt"] : $_GET['opt'];
$key = ($_REQUEST["key"]) ? $_REQUEST["key"] : $_GET['key'];
$rowNum = ($_REQUEST["rowNum"]) ? $_REQUEST["rowNum"] : $_GET['rowNum'];
$chk_info = ($_REQUEST["chk_info"]) ? $_REQUEST["chk_info"] : $_GET['chk_info'];

$key = Br_dconv($key);

if($opt == "Starts With")	$key = $key."%";
else						$key = "%".$key."%";

if($mode == "inventory") {

	$inactive = ($_REQUEST["inactive"]) ? $_REQUEST["inactive"] : $_GET['inactive'];

	if($inactive == "yes") $prewhere = "";
	else $prewhere = "AND useYN = 'Y' ";

	include_once "includes/db_configms.php";

	$query = "SELECT CID, wsCode, ProdOwnCode, prodId, prodName, prodKname, prodIUprice, prodUnit, prodsize, CONVERT(char(10),LastModDate,126) AS LastModDate, prodBalance, useYN ".
			 "FROM Inventory_Item ".
			 "WHERE CID = '$cId' AND (wsCode LIKE '$key' OR prodKname LIKE '$key' OR prodName LIKE '$key') ".$prewhere." ";
			 "ORDER BY wsCode, ProdOwnCode";
	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);

	echo $row_num;
	if($row_num != "0"){
		$i = 0;
		while($row = mssql_fetch_array($query_result)) {
			$name = Br_iconv($row['prodKname']);
			if(trim($row['prodName']))
				$name .= " / ".trim($row['prodName']);
			echo "::".$row['wsCode'].";".$row['ProdOwnCode'].";".$row['prodId'].";".$name.";".Br_iconv($row['prodsize']).";".$row['prodBalance'].";".$row['prodIUprice'].";".$row['prodIUprice'].";".$row['useYN'];
			//if($i++ > 50 && $rowNum === "0") break;
		}
	}
}

else if($mode == "retail") {

	$dbs_servername = "184.69.79.114, 2544";
	$dbs_servername2 = "96.53.72.106";
	$dbs_userid = "pos";
	$dbs_password = "pos";
	$dbs_password2 = "pos2006";
	$dbs_dbname = "dbgal";
    define( "DB_SERVER", $dbs_servername );
    define( "DB_USERID", $dbs_userid );
    define( "DB_PASSWORD", $dbs_password );
    define( "DB_NAME", $dbs_dbname ); 
    define( "DB_SERVER2", $dbs_servername2 );
    define( "DB_PASSWORD2", $dbs_password2 );

	if($chk_info == "") $chk_info = "1";
//$chk_info = "2";
	if($chk_info == "1") {
		$conn = mssql_connect( DB_SERVER, DB_USERID, DB_PASSWORD) or die("Database failed to response.");  
		mssql_select_db( DB_NAME, $conn );
	} else if($chk_info == "2") {
		$conn = mssql_connect( DB_SERVER2, DB_USERID, DB_PASSWORD2) or die("Database failed to response.");  
		mssql_select_db( DB_NAME, $conn );
	}

	$IT_field = "a.GalCode, a.ProdOwnCode, a.SuppCode, prodId, prodName, prodKname, prodType, prodOUprice, prodTotQty, prodTax, prodUnit, b.prodsize, prodPromo, promoPrice, LastModDate, LastModTime, a.useYN, convert(varchar(20), promoSdate, 120) as promoSdate, convert(varchar(20), promoEdate, 120) as promoEdate";

	$IT_where = " a.useYN = 'Y' AND b.SuppProdCode = '1' ";

	$query = "SELECT ".$IT_field." ".
			 "FROM mfProd a ".
				"LEFT JOIN tblGalProdMaster b ".
				"ON a.GalCode=b.GalCode AND a.ProdOwnCode=b.ProdOwnCode AND a.SuppCode=b.SuppCode ".
			  "WHERE ".$IT_where;

	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);
	echo $row_num;
	if($row_num != "0"){
		$i = 0;
		while($row = mssql_fetch_array($query_result)) {
			if ($row['prodPromo'] == "1" && $row['promoEdate'] >= $today) {
				$strPromo = "Process";
			} else if ($row['prodPromo'] == "1" && $row['promoEdate'] < $today) {
				$strPromo = "END";
			} else {
				$strPromo = "";
			}
			$prodName = Br_iconv($row['prodKname']);
			if(trim($row['prodName']))
				$prodName .= " / ".trim($row['prodName']);
			echo "::".$row['prodId'].";".$prodName.";".Br_iconv($row['prodsize']).";".$row['prodUnit'].";".$row['prodType'].";".$row['prodIUprice'].";".$row['prodOUprice'].";".$row['prodTotQty'].";".$row['prodTax'].";".$row['prodDeposit'].";".$strPromo.";".$row['useYN'];
			//if($i++ > 50 && $rowNum === "0") break;
		}
	}
	//$row['tInvNo'];$row['tDate'];Br_iconv($row['Name']);$row['tCust'];readPassWord($cId,$row['tPassWord'])
}

else {
	$dbs_servername0 = "192.168.2.60";
	$dbs_userid0 = "wssale";
	$dbs_password0 = "w2015";
	$dbs_dbname0 = "wholesaleDB";
    define( "DB_SERVER0", $dbs_servername0 );
    define( "DB_USERID0", $dbs_userid0 );
    define( "DB_PASSWORD0", $dbs_password0 );
    define( "DB_NAME0", $dbs_dbname0 ); 
	$dbs_servername = "184.69.79.114, 2544";
	$dbs_servername2 = "96.53.72.106";
	$dbs_userid = "pos";
	$dbs_password = "pos";
    define( "DB_SERVER", $dbs_servername );
    define( "DB_USERID", $dbs_userid );
    define( "DB_PASSWORD", $dbs_password );
	$dbs_password2 = "pos2006";
	$dbs_dbname = "dbgal";
    define( "DB_NAME", $dbs_dbname ); 
    define( "DB_SERVER2", $dbs_servername2 );
    define( "DB_PASSWORD2", $dbs_password2 );
	
	if($chk_info == "") $chk_info = "0";

	if($chk_info == "0") {
		$conn = mssql_connect( DB_SERVER0, DB_USERID0, DB_PASSWORD0) or die("Database failed to response.");  
		mssql_select_db( DB_NAME0, $conn );
	} else if($chk_info == "1") {
		$conn = mssql_connect( DB_SERVER, DB_USERID, DB_PASSWORD) or die("Database failed to response.");  
		mssql_select_db( DB_NAME, $conn );
	} else {
		$conn = mssql_connect( DB_SERVER2, DB_USERID, DB_PASSWORD2) or die("Database failed to response.");  
		mssql_select_db( DB_NAME, $conn );
	}

	if($chk_info == 0) { // 도매
		$IT_field = "a.Barcode,a.Unit,a.Up_Barcode,a.Up_Unit,a.Up_Inside_qty,b.prodName,b.prodKname,b.prodsize,b.useYN,c.prodName as EN,c.prodKname as KN,c.prodsize as SZ ";
	} else {
		$IT_field = "a.Barcode,a.Unit,a.Up_Barcode,a.Up_Unit,a.Up_Inside_qty,b.prodKrName,b.prodEnName,b.prodsize,b.useYN,c.prodKrName as KN,c.prodEnName as EN,c.prodsize as SZ ";
	}

	if($chk_info == "0") { // 도매
		$query = "SELECT $IT_field from tfBarcodeLink a LEFT JOIN Inventory_Item b ON a.Barcode=b.prodId LEFT JOIN Inventory_Item c ON Up_Barcode=c.prodId ";
	} else {
		$query = "SELECT $IT_field from tfBarcodeLink a left join tblGalProdMaster b on Barcode=b.ProdBarCode left join tblGalProdMaster c on Up_Barcode=c.ProdBarCode ";
	}

	$query_result = mssql_query($query);
	$row_num = mssql_num_rows($query_result);

	echo $row_num;
	//$start = time();
	//echo " 1:".$start;
	if($row_num != "0"){
		//$i = 0;
		while($row = mssql_fetch_array($query_result)) {
			if($chk_info == "0") {
				$name = Br_iconv($row['prodKname']);
				if(trim($row['prodName']))
					$name .= " / ".$row['prodName'];
				$upname = Br_iconv($row['KN']);
				if(trim($row['EN']))
					$upname .= " / ".$row['EN'];
			} else {
				$name = Br_iconv($row['prodKrName']);
				if(trim($row['prodEnName']))
					$name .= " / ".$row['prodEnName'];
				$upname = Br_iconv($row['KN']);
				if(trim($row['EN']))
					$upname .= " / ".$row['EN'];
			}
			//$i++;
			//if($i < 30) echo " 1-".$i.":".($start - time());
			echo "::".$row['Barcode'].";".$row['Unit'].";".$name.";".Br_iconv($row['prodsize']).";".$row['Up_Barcode'].";".$row['Up_Unit'].";".$upname.";".Br_iconv($row['SZ']).";".$row['Up_Inside_qty'];		}
	}
	//echo " 2:".($start - time());
}
mssql_close();
?>
