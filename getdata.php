<?php
header("Access-Control-Allow-Origin: *");

//SQL データベースに接続
$connectionInfo = array(
  "UID" => "xx",
  "pwd" => "xx",
  "Database" => "xx",
  "LoginTimeout" => 30,
  "Encrypt" => 1,
  "TrustServerCertificate" => 0
);

$serverName = "xx";

try
{
	//nullで初期化
	$users = null;

	//DBに接続
	$dbh = sqlsrv_connect($serverName, $connectionInfo);

  $sql0="select * from Hack ORDER BY id DESC;";
	$stmt = sqlsrv_query($dbh,$sql0);

	if (!$stmt) {
		die( print_r( sqlsrv_errors(), true));
	}

	//取得したデータを配列に格納
	while($row = sqlsrv_fetch_object($stmt))
	{
	      $users[] = array(
	      'lat'=> $row->lat,
	      'lon'=> $row->lon,
	      'animal'=> $row->animal,
	      'lovepoint'=> $row->lovepoint,
	      'animalLove'=> $row->animalLove,
	      'date'=> $row->date,
	      );

	}


  	//JSON形式で出力する
  	header('Content-Type: application/json; charset=UTF-8');
  	echo json_encode( $users, JSON_UNESCAPED_UNICODE );
  	exit;

}
catch (PDOException $e)
{
	//例外処理
	die('Error:' . $e->getMessage());
}
