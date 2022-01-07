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

$id = html_escape($_POST['stockid']);
//idを全角もOKにする
$id = mb_convert_kana($id, 'n', 'UTF-8');

 //DBより一覧表書き出し ID照合用
 $sql_list = 'SELECT * FROM menulist';
 $stmt = $dbh->prepare($sql_list);
 //executeにif文使ったらエラーになった
 $stmt->execute();
 $data =array();
 $count = $stmt->rowCount();//レコード数取得
 //FETCH_ASSOCで配列として書き出して代入
 while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
   $data[] = $row;
 }
//IDのカラムだけ配列にする
 $id_Array = array_column($data, 'ID');

 //DBとの検索IDを照合する、DBのID以外は全て弾くので空文字やテキストのバリデーションにもなる
if(in_array($id,$id_Array,true)){
  //DBよりID書き出し
  $sql_id = "SELECT * FROM menulist WHERE id = :id";
  $stmt_id = $dbh->prepare($sql_id);
  $stmt_id->bindParam( ':id', $id, PDO::PARAM_INT);
  $stmt_id->execute();
  //配列にする
  if($stmt_id) {
    $data = $stmt_id->fetch(PDO::FETCH_ASSOC);
    }
    $item = $data['item'];
    $select_cat = $data['category'];
    $select_mt = $data['material'];
    $plice = number_format($data['plice']);
    $change_img = $data['image'];
    $submit_btn = '<input class="toBtn" type="submit" value="変更">';
} else {
    $item = '';
    $select_cat = '';
    $select_mt = '';
    $plice = '';
    $change_img = '';
    $submit_btn = '<p class="errComent">※ID欄が空かDBにないIDです。一覧に戻って選び直してください。</p>';
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
  <title>DBテスト管理画面・変更</title>
  <link rel="stylesheet" href="/menulist/common/sanitize.css">
  <link rel="stylesheet" href="/menulist/common/style.css">
  <link rel="stylesheet" href="/menulist/common/control.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="/menulist/favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">
    <header id="header">
      <h1 class="topTitle">DBテスト管理画面・変更</h1>
      <p class="center notice1">変更箇所のみ書き換えてください</p>
    </header>

    <main id="main">
      <!-- 変更 -->
      <form method="post" action="change_check.php" enctype="multipart/form-data">
        <div class="formLow">
          <div class="stockId">
            <p>ID</p>
            <p class="formInput"><?php echo $id; ?></p>
            <input type="hidden" name="stockid" value="<?php echo $id; ?>">
          </div>
          <div class="stock">
            <label>商品名</label>
            <input type="text" name="stock" value="<?php echo $item; ?>">
          </div>
          <div class="stockCategory">
            <label>カテゴリー</label>
            <select name="category" value="<?php echo $select_cat; ?>">
            <!-- selectはインクルード -->
            <?php include_once(dirname(__FILE__).'/select_cat.php'); ?>
            </select>
          </div>

          <div class="materialForm">
            <label>素材</label>
            <select name="material" value="<?php echo $select_mt; ?>">
            <!-- selectはインクルード -->
            <?php include_once(dirname(__FILE__).'/select_mt.php'); ?>
            </select>
          </div>

          <div class="stockplice">
            <label>金額</label>
            <input type="text" name="stockplice" value="<?php echo $plice; ?>">
          </div>
        </div>
        <!-- //.formLow -->
        <div class="center">
          <label>商品画像:サイズ横640px縦420px<br><span class="text12">※変更のある場合のみUP</span></label><br>
          <input type="file" name="stockimg">
          <p class="center">
            <img class="thumb12" src="<?php echo $img_path.$change_img; ?>" alt="">
            <!-- 削除のある場合に古い画像も持っていく -->
            <input type="hidden" name="old_img" value="<?php echo $change_img; ?>">
          </p>
          <p class="text12 center mg0">変更前の画像です</p>
        </div>
        <?php echo $submit_btn; ?>
      </form>
      <div id="toList"><a href="/menulist/control/control_top.php">管理画面トップへ</a></div>
  </main>

<footer id="footer">
  <small>DBテストメニューsystem</small>
</footer>
</div>
<!-- //# wrapper-->
<script src="/menulist/common/jquery-3.6.0.min.js"></script>
<script src="/menulist/common/control.js"></script>
</body>
</html>