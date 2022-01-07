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
  $id = mb_convert_kana($id, 'n', 'UTF-8');

  //DBより一覧表書き出し ID照合用
  $sql_list = 'SELECT * FROM menulist';
  $stmt = $dbh->prepare($sql_list);
  $stmt->execute();
  $data =array();
  $count = $stmt->rowCount();//レコード数取得
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $data[] = $row;//FETCH_ASSOCで配列として書き出して代入
  }
  //IDのカラムだけ配列にする
  $id_Array = array_column($data, 'ID');

  //DBとの検索IDを照合する
  if(in_array($id,$id_Array,true)){
    $sql_id = "SELECT * FROM menulist WHERE id = :id";
    $stmt_id = $dbh->prepare($sql_id);
    $stmt_id->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_id->execute();
    //配列にする
    if($stmt_id) {
      $data = $stmt_id->fetch(PDO::FETCH_ASSOC);
    }
    
    $item = $data['item'];
    $plice = number_format($data['plice']);
    $select_cat = $data['category'];
    $select_mt = $data['material'];
    $change_img = $data['image'];
    $submit_btn = '<input class="toBtn" type="submit" value="削除">';
  } else {
    $item = '';
    $select_cat = '';
    $select_mt = '';
    $plice = '';
    $change_img = '';
    $submit_btn = '<p class="errComent">※DBにないIDです。一覧に戻って選び直してください。</p>';
  }

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //isset置かないとNoticeが出る
    if(isset($_POST['deleteid'])){
      $id = html_escape($_POST['deleteid']);
      $image = html_escape($_POST['deleteimg']);

      //DBより削除
      $sql_delete = "DELETE FROM menulist WHERE id = :id";
      $stmt_delete = $dbh->prepare($sql_delete);
      $stmt_delete->bindParam( ':id', $id, PDO::PARAM_INT);
      $stmt_delete->execute();

      //画像フォルダからも削除
      unlink('./img_up/'.$image);
      //実行されたらトップに飛ばす
      header('Location:/menulist/control/control_top.php');
    }
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
      <h1 class="topTitle">DBテスト管理画面・削除</h1>
      <p class="center notice1">以下のメニューを削除します</p>
    </header>

    <main id="main">
      <!-- 変更 -->
      <form method="post" action="">
          <div class="stockBox">
            <table>
              <tr>
                <th>ID</th><th>商品名</th><th>商品画像</th><th>カテゴリー</th><th>素材</th><th>金額</th>
              </tr>
              <tr>
                <td class="stocklistId"><?php echo $id; ?><input type="hidden" name="deleteid" value="<?php echo $id; ?>"></td>
                <td class="stocklist"><?php echo $item; ?></td>
                <td class="stockImg"><img src="<?php echo $img_path.$change_img; ?>" alt=""><input type="hidden" name="deleteimg" value="<?php echo $change_img; ?>"></td>
                <td class="stocklisutoCat"><?php echo $select_cat; ?></td>
                <td class="stocklistMat"><?php echo $select_mt; ?></td>
                <td class="stocklistPlice"><?php echo $plice ; ?>円</td>
              </tr>
            </table>
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