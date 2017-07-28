<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');

//SQL データベースに接続
$connectionInfo = array(
  "UID" => "xx",
  "pwd" => "{xx}",
  "Database" => "xx",
  "LoginTimeout" => 30,
  "Encrypt" => 1,
  "TrustServerCertificate" => 0,
  "CharacterSet"=>"UTF-8"
);

$serverName = "xx";

try
{
　//nullで初期化
  $users = null;
  $pic = null;
  $kind = "0";
  $get_animaldata_0 ="0";
  $get_animaldata_1 ="0";
  $get_animaldata_2 ="0";
  $get_animaldata_3 ="0";
  $get_animaldata_4 ="0";
  $get_animaldata_5 ="0";
  $postpicture = $_POST["image"];
  $postid = $_POST["user_id"];

  //DBに接続
  $dbh = sqlsrv_connect($serverName, $connectionInfo);

  $sql1 = "INSERT INTO Test (image,user_id)
                  VALUES ('$postpicture','$postid')";


  $stmt1 = sqlsrv_query($dbh,$sql1);

  $sql0="select top 1 * from Test where user_id = '$postid' ORDER BY id DESC;";
	$stmt = sqlsrv_query($dbh,$sql0);

	if (!$stmt) {
		die( print_r( sqlsrv_errors(), true));
	}

	//取得したデータを配列に格納
	while($row = sqlsrv_fetch_object($stmt))
	{

    $pic = $row->image;
    $data = base64_decode($pic);
    $im = imagecreatefromstring($data);
    if ($im !== false) {
    	$api_key = "☓☓"; //APIキー

    	$json = json_encode( array( //リクエスト用のJSONを作成
    		"requests" => array(
    			array(
    				"image" => array(
    					"content" => $pic ,
    				) ,
    				"features" => array(
    					array(
    						"type" => "LABEL_DETECTION" ,
    						"maxResults" => 5 ,
    					) ,
    				) ,
    			) ,
    		) ,
    	) ) ;

    	$curl = curl_init() ; //リクエストを実行
    	curl_setopt( $curl, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=" . $api_key ) ;
    	curl_setopt( $curl, CURLOPT_HEADER, true ) ;
    	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" ) ;
    	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ) ) ;
    	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;
    	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;
    	curl_setopt( $curl, CURLOPT_TIMEOUT, 15 ) ;
    	curl_setopt( $curl, CURLOPT_POSTFIELDS, $json ) ;
    	$res1 = curl_exec( $curl ) ;
    	$res2 = curl_getinfo( $curl ) ;
    	curl_close( $curl ) ;

    	$json = substr( $res1, $res2["header_size"] ) ; //取得したJSON
    	$header = substr( $res1, 0, $res2["header_size"] ) ; //レスポンスヘッダー

        $var = json_decode($json,true);

        $get_animaldata_0 = $var["responses"][0]["labelAnnotations"][0]["description"];
        $get_animaldata_1 = $var["responses"][0]["labelAnnotations"][0]["description"];
        $get_animaldata_2 = $var["responses"][0]["labelAnnotations"][1]["description"];
        $get_animaldata_3 = $var["responses"][0]["labelAnnotations"][2]["description"];
        $get_animaldata_4 = $var["responses"][0]["labelAnnotations"][3]["description"];
        $get_animaldata_5 = $var["responses"][0]["labelAnnotations"][4]["description"];

        if (is_null($get_animaldata_1)) { //swichは使わない
        $get_animaldata_1 = "0";
        }

        if (is_null($get_animaldata_2)) {
        $get_animaldata_2 = "0";
        }

        if (is_null($get_animaldata_3)) {
        $get_animaldata_3 = "0";
        }

        if (is_null($get_animaldata_4)) {
        $get_animaldata_4 = "0";
        }

       if (is_null($get_animaldata_5)) {
        $get_animaldata_5 = "0";
       }

        if($get_animaldata_0 == "cat"){
          $kind = "cat";
        }else if($get_animaldata_0 == "dog"){
          $kind = "dog";
        }

	 }

      $users[] = array(
        'animal_kind'=> $kind,
        'animal_info1'=> $get_animaldata_1,
        'animal_info2'=> $get_animaldata_2,
        'animal_info3'=> $get_animaldata_3,
        'animal_info4'=> $get_animaldata_4,
        'anmal_info5'=> $get_animaldata_5
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
