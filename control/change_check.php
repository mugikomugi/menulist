<?php
//ログインしていないとアクセスさせない
session_start();
session_regenerate_id(true);
if(isset($_SESSION['login']) === false){
    header('Location: index.php');
    exit();
}

try {
include_once(dirname(__FILE__).'/db_join.php');

$err = ['item'=>'','plice'=>'','imgsize'=>''];

$id = html_escape($_POST['stockid']);
$item = html_escape($_POST['stock']);
$category = html_escape($_POST['category']);
$material = html_escape($_POST['material']);
$plice = html_escape($_POST['stockplice']);
$plice = mb_convert_kana($plice, 'n', 'UTF-8');
$old_img = html_escape($_POST['old_img']);

if(isset($_FILES['stockimg'])){
  $image = $_FILES['stockimg'];
  $image['name'] = html_escape($image['name']);
  //英小文字に変換
  $image['name'] = strtolower($image['name']);

  //ファイル名と拡張子を切り分けて.を除去、ピリオドを重複させない為
  $image_parts = pathinfo($image['name']);
  $extension = 'jpg';
  $image_name = $image_parts['filename'];
  $image_name = str_replace('.','',$image_name);

  if($image['size'] > 1000000){
    $err['imgsize'] = '画像が1MBを超えています';
  } elseif(file_exists('./img_up/'.$image_name.'.'.$extension) === TRUE) {
    //file_exists関数でディレクトリ内を調べて、同じファイル名があった場合はアップさせない
    $err['imgsize'] = '同名のファイルがあります。違うファイル名にしてください。';
  } else {
    $err['imgsize'] = '';
  }
}

//var_dump(is_number($plice));
  //エラー対処
  if(!check_word($item,25)){
    $err['item'] = '空文字か入力値が超えています';
  } else {
    $err['item']= '';
  }

  if(!check_word($plice,8)){
    $err['plice'] = '空文字か入力値が超えています';
  } elseif(is_numeric($plice) == FALSE) {
    //金額が数字じゃなかったらNG
    $err['plice'] = '数字を入力してください';
    //金額変更でNGが出ると下記の変数に値が無くなるので対処
    $prev_img = '';
  } else {
    $err['plice']= '';
    $plice = number_format($plice);
  }
  //(int)$image_nameで画像の有無を数値化
  //var_dump((int)$image_name);

//エラーが無かったら更新へ
if($err['item']== '' && $err['imgsize'] == '' && $err['plice']== ''){
//ファイルアップがあった時の対処、0じゃなっかたらディレクトリにアップ、ここは苦労した
  if((int)$image_name !== 0){
    move_uploaded_file($image['tmp_name'],'./img_up/'.$image_name.'.'.$extension);
    //画像が変更されたら古い画像はフォルダより削除
    unlink('./img_up/'.$old_img);
    //DBに持っていくファイル名
    $image = $image_name.'.'.$extension;
    //見本表示
    $prev_img = '<p class="center">変更画像<br><img class="thumb12" src="'.$img_path.$image_name.'.'.$extension.'" alt=""></p>';
  } else {
    //DBに持っていくファイル名を置かないとDBからファイル名が消えてしまう
    $image = $old_img;
    $prev_img = '<p>変更画像無し</p>';
  }

  //指定のidのDBを更新
  $sql_up = 'UPDATE menulist SET item=:item,image=:image,category=:category,material=:material,plice=:plice WHERE id = :id';
  $stmt_up = $dbh->prepare($sql_up);
  $stmt_up->bindParam( ':id', $id, PDO::PARAM_INT);
  $stmt_up->bindValue(':item',$item,PDO::PARAM_STR);
  $stmt_up->bindValue(':image',$image,PDO::PARAM_STR);
  $stmt_up->bindValue(':category',$category,PDO::PARAM_STR);
  $stmt_up->bindValue(':material',$material,PDO::PARAM_STR);
  $stmt_up->bindValue(':plice',$plice,PDO::PARAM_INT);
  $stmt_up->execute();

  $notice = '以下の内容で更新しました';
  $back_btn = '<div id="toList"><a href="/menulist/control/control_top.php">管理画面トップへ</a></div>';
} else {
  $notice = 'エラーがあります。戻って修正してください。';
  //お手軽にhistory.back使いましたが「フォーム再送信の確認」が出る可能性有り
  //かといってa hrefだと入力値が消える。手間だけどもう一つ入力値を持っていった再入力用ページを作る?
  $back_btn = '<div id="toList"><a onclick="history.back()">修正する</a></div>';
}

} catch (PDOException $e){
  print($e->getMessage());
  die();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DBテスト管理画面・変更確認</title>
  <link rel="stylesheet" href="/menulist/common/sanitize.css">
  <link rel="stylesheet" href="/menulist/common/style.css">
  <link rel="stylesheet" href="/menulist/common/control.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="/menulist/favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">
    <header id="header">
      <h1 class="topTitle">DBテスト管理画面・変更確認</h1>
      <p class="center notice1"><?php echo $notice; ?></p>
    </header>

    <main id="main">
      <div class="formLow">
          <div class="stockId">
            <p>ID</p>
            <p class="formInput"><?php echo $id; ?></p>
          </div>
          <div class="stock">
            <label>商品名</label>
            <p class="formInput"><?php echo $item; ?></p>
            <p><?php echo $err['item']; ?></p>
          </div>
          <div class="stockCategory">
            <label>カテゴリー</label>
            <p class="formInput"><?php echo $category; ?></p>
          </div>
          <div class="materialForm">
            <label>素材</label>
            <p class="formInput"><?php echo $material; ?></p>
          </div>

          <div class="stockplice">
            <label>金額</label>
            <p class="formInput"><?php echo $plice; ?></p>
            <p><?php echo $err['plice']; ?></p>
          </div>
        </div>
        <!-- //.formLow -->
        <div class="center">
          <?php echo $prev_img; ?>
          <p class="center"><?php echo $err['imgsize']; ?></p>
        </div>

      <?php echo $back_btn; ?>
    </main>
  </div>
  <!-- //#wrapper -->
<footer id="footer">
  <small>DBテストメニューsystem</small>
</footer>
</div>
<!-- //# wrapper-->
<script src="/menulist/common/jquery-3.6.0.min.js"></script>
<script src="/menulist/common/control.js"></script>
</body>
</html>