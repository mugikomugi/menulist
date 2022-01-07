<?php
  //DBに接続
    $dsn = 'mysql:dbname=menulist;host=localhost;charset=utf8';
    $user ='root';
    $password = '';
    
    $dbh = new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  
//DBへ格納した画像パス
$img_path = '/menulist/control/img_up/';

//XSS
function html_escape($word){
  return htmlspecialchars($word,ENT_QUOTES,'UTF-8');
}
//エラーチェック
function check_word($word,$length){
  if(mb_strlen($word) === 0 || mb_strlen($word) > $length){
    return FALSE;
  } else{
    return TRUE;
  }
}

//書き出し一覧のカテゴリーループに使う配列
$cat_list = ['メイン','サラダ','スープ','サイドメニュー','麺・パスタ','デザート','ドリンク'];
//一覧の#セレクタ、同一スクロール用配列
$category_id = ['mainmenu','salad','soup','sideMenu','pasta','dessert','drink'];