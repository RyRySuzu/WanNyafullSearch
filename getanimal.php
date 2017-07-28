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

try
{
  //nullで初期化
  $users = null;
  $dog = null;
  $cat = null;
  $dpoint = 0;
  $cpoint = 0;

  //DBに接続
  $dbh = sqlsrv_connect($serverName, $connectionInfo);
  $time_start = microtime(true);

  $sql0="select * from Hack where animal = 'dog'";
  $stmt = sqlsrv_query($dbh,$sql0);

	if (!$stmt) {
		die('error'.mysql_error());
	}

	//取得したデータを配列に格納
	while($row = sqlsrv_fetch_object($stmt))
	{
    $dog = $row->lovepoint;
    $dpoint = $dog + $dpoint;
	}

  $sql1="select * from Hack where animal = 'cat'";
	$stmt1 = sqlsrv_query($dbh,$sql1);

	if (!$stmt1) {
		die( print_r( sqlsrv_errors(), true));
	}

	//取得したデータを配列に格納
	while($row1 = sqlsrv_fetch_object($stmt1))
	{
    $cat = $row1->lovepoint;
    $cpoint= $cat + $cpoint;
	}

    $sum = $dpoint + $cpoint;
    $cats = $cpoint/$sum*100;

    $users[] = array(
      'cat_per'=> $cats
    );


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
