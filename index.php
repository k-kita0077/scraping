<?php
// phpQueryの読み込み
require_once("phpQuery-onefile.php");
// HTMLの取得
$doc = phpQuery::newDocumentFile("https://tabelog.com/tokyo/A1306/");
 
foreach ($doc[".js-rstlist-info"]->find(".list-rst__body") as $entry){
    //タイトル
    $h4 = pq($entry)->find('h4')->text();
    //内容
    $dat = pq($entry)->find('.list-rst__area-genre')->text();
    //料金
    $pri = pq($entry)->find('.c-rating__val')->text();
    //画像
    $ima = pq($entry)->find('.js-thumbnail-img')->attr('data-original');

    //配列に格納
    $scrapingData[] = ['title' => $h4, 'data' => $dat, 'price' => $pri, 'image' => $ima];
}

//var_dump($scrapingData);

function db_conn() {
    try{
        $pdo = new PDO('mysql:dbname=scraping_DB;charset=utf8;host=localhost','root','');
    } catch (PDOException $e) {
        exit('DbConnectError:'.$e->getMessage());
    }
    return $pdo;
}

$pdo = db_conn();
$stmt = $pdo->prepare("INSERT INTO scraping_table(id,title,data,price,image) VALUES(null,:title,:data,:price,:image)");


//配列から取り出して１店舗づつ追加
foreach($scrapingData as $value) {
    $title = $value["title"];
    $data = $value["data"];
    $price = $value["price"];
    $image = $value["image"];

    $stmt->bindValue(":title", $title, PDO::PARAM_STR);
    $stmt->bindValue(":data", $data, PDO::PARAM_STR);
    $stmt->bindValue(":price", $price, PDO::PARAM_STR);
    $stmt->bindValue(":image", $image, PDO::PARAM_STR);

    $status = $stmt->execute();

    if($status==false){
        $error = $stmt->errorInfo();
        exit("SQLError:".$error[2]);
    }else{
    }
}

?>