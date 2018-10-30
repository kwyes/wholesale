<?
session_start();
$CID = $_SESSION['staffCID'];

include_once "includes/db_configms.php";
include_once "includes/common_class.php";

$rowNum = ($_REQUEST["rowNum"]) ? $_REQUEST["rowNum"] : $_GET['rowNum'];
$inactive = ($_REQUEST["inactive"]) ? $_REQUEST["inactive"] : $_GET['inactive'];

if($inactive == "yes") $prewhere = "";
else $prewhere = "AND a.Status = 1 ";

$query = "SELECT a.CID, a.CardID, a.CardType, b.Phone1, a.Name, a.cType, a.CurrentBalance, a.Status, b.Location, b.Address, b.City, b.Province, b.PostalCode, b.Country, b.Email ".
		 "FROM Card a LEFT JOIN ".
			"(SELECT CID, CardID, CardType, Address, City, Province, PostalCode, Country, Phone1, Phone2, Phone3, Email, Location ".
				  "FROM CardAddress ".
			  "WHERE Location = 1 AND CID = '$CID') b ".
			  "ON a.CID = b.CID AND a.CardID = b.CardID AND a.CardType = b.CardType ".
		 "WHERE a.CID = '$CID' ".$prewhere.
		 "ORDER BY a.CardID ASC";

$query_result = mssql_query($query);
$row_num = mssql_num_rows($query_result);

echo $row_num;
if($row_num == 0) return;

if($rowNum !== "0")
	mssql_data_seek($query_result,$rowNum);

$i = 0;
while($row = mssql_fetch_array($query_result)){
	echo "::".$row['CardID'].";".$row['CardType'].";".Br_iconv($row['Phone1']).";".Br_iconv($row['Name']).";".$row['cType'].";".$row['CurrentBalance'].";".$row['Email'].";".$row['Address'].";".$row['City'].";".$row['Province'].";".$row['PostalCode'].";".$row['Country'].";".$row['Status'];
	//if($i++ > 50 && $rowNum === "0") break;
}
?>
