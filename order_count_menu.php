<?php
//メニューリストからメニュー一覧へ移行する際、jsonにデータを格納させる
//メニューリストで個数変更・削除が入った場合、hrefではデータが維持できない為

//XSS
function html_escape($word){
  return htmlspecialchars($word,ENT_QUOTES,'UTF-8');
}

$count_menu = isset($_GET['count_menu']);
$count_menu = html_escape($_GET['count_menu']);
$count_menu = (int)$count_menu;

$order_name = [];
$one_plice = [];
$num = [];
$sub_plise = [];
$order_img = [];

//設定したカウント数でname値を書き出し配列へ入れる
for($i = 0; $i < $count_menu+1; $i++){
$order_name[$i] = html_escape($_GET['title'.$i]);
$one_plice[$i] = html_escape($_GET['plice'.$i]);
$order_img[$i] = html_escape($_GET['img'.$i]);
//文字列内にある”,”を""に変換
$one_plice[$i] = str_replace(',', '',$one_plice[$i]);
$one_plice[$i] = (int)$one_plice[$i];
$num[$i] = html_escape($_GET['num'.$i]);

}

//JSONに入れる為、配列へ
$order = [$order_name,$num,$one_plice,$order_img];
//JSON形式に変換
$json = json_encode($order,JSON_UNESCAPED_UNICODE);
//JSONへ格納
file_put_contents('./order.json',$json);

header('Location: order_change_menu.php');