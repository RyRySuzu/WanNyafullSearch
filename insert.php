<?php
header("Access-Control-Allow-Origin: *");

//SQL データベースに接続
$connectionInfo = array(
  "UID" => "xx",
  "pwd" => "{xx}",
  "Database" => "xx",
  "LoginTimeout" => 30,
  "Encrypt" => 1,
  "TrustServerCertificate" => 0
);

  $serverName = "xx";

  //android側からPOSTの受け取り
  $postlat = $_POST["lat"];
  $postlon = $_POST["lon"];
  $postimage = $_POST["image"];
  $postanimal= $_POST["animal"];
  $postlovepoint= $_POST["lovepoint"];
  $postranimalLove = $_POST["animalLove"];
  $posta_info1 = $_POST["animal_info1"];
  $posta_info2 = $_POST["animal_info2"];
  $posta_info3 = $_POST["animal_info3"];
  $posta_info4 = $_POST["animal_info4"];
  $posta_info5 = $_POST["animal_info5"];

try
{

$conn = sqlsrv_connect($serverName, $connectionInfo);

//サーバアクセスエラー処理
if(!$conn){
    die( print_r( sqlsrv_errors(), true));
}

  date_default_timezone_set('Asia/Tokyo');

  $time =  date( "Y/m/d h:i:s" ) ;

  $sql1 = "INSERT INTO Hack (lat,lon,animal,lovepoint,date,image,animalLove,a_info1,a_info2,a_info3,a_info4,a_info5)
                  VALUES ('$postlat','$postlon',N'$postanimal','$postlovepoint','$time','$postimage',N'$postranimalLove',N'$posta_info1',N'$posta_info2',N'$posta_info3',N'$posta_info4',N'$posta_info5')";

  $result1 = sqlsrv_query($conn,$sql1);
  if ($result1==false) {
	die( print_r( sqlsrv_errors(), true));
	}

}

catch (PDOException $e)
{
 	//例外処理
 	die('Error:' . $e->getMessage());
}
?>
