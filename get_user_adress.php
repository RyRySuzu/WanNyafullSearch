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
  $lat = 0;
  $lon = 0;
  $latadress = 0;
  $lonadress = 0;

  $postanimal_kind = $_POST["animal"];
  $postadress = $_POST["adress"];
  $postatime = $_POST["time"];

  $year =  date('Y', strtotime($postatime));
  $day =  date('d', strtotime($postatime));

	//DBに接続
	$dbh = sqlsrv_connect($serverName, $connectionInfo);

  $sql0="select top 12 * from Hack where animal = '$postanimal_kind' and date like  '%$year%' and date like  '%$day%' ORDER BY id DESC;";
	$stmt = sqlsrv_query($dbh,$sql0);

	if (!$stmt) {
		die( print_r( sqlsrv_errors(), true));
	}

  //住所
  $p = urlencode($postadress);
  $xml = simplexml_load_file("http://www.geocoding.jp/api/?q=$p");

  $latadress = $xml->coordinate->lat;
  $lonadress = $xml->coordinate->lng;

	//取得したデータを配列に格納
	while($row = sqlsrv_fetch_object($stmt))
	{

    $lat = $row->lat;
    $lon = $row->lon;

    $distance = 0;
    $lat1 = $latadress;
    $lng1 = $lonadress;
    $lat2 = $lat;
    $lng2 = $lon;

    //距離計算
    if ((abs($lat1 - $lat2) < 0.00001) && (abs($lng1 - $lng2) < 0.00001)) {
        $distance = 0;
        } else {
        $x = ($lat2 - $lat1);
        $y = ($lng2 - $lng1);

        $E = $x * 10000 * 11.32;
        $T = $y * 10000 * 9.229;

        $U =pow($E, 2);
        $I =pow($T, 2);


        $distance = sqrt($U + $I);

      }

      if($distance<100000){

        // jsonデータ取得
        $GMADs = array();
        $GMADs[] = json_decode(@file_get_contents('http://maps.google.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ja'), true);

        //住所取得
        $addresses = array();

        foreach ($GMADs as $GMAD) {
        $addresses[] = $GMAD['results'][0]['formatted_address'];
        }

        $users[] = array(
          'lat'=> $row->lat,
          'lon'=> $row->lon,
          'animal'=> $row->animal,
          'lovepoint'=> $row->lovepoint,
          'adress'=> $addresses[0],
          'image'=> $row->image
          );
        }

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
