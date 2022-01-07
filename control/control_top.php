<?php
//ログインしていないとアクセスさせない
session_start();
session_regenerate_id(true);
if(isset($_SESSION['login']) === false){
    header('Location: index.php');
    exit();
}

try{
  include_once(dirname(__FILE__).'/db_join.php');
  //DBより一覧表書き出し
  $sql_list = 'SELECT * FROM menulist order by ID ASC';
  //order byはソート指定デフォはASC昇順 DESCは降順
  $stmt = $dbh->prepare($sql_list);
  //executeにif文使ったらエラーになった
  $stmt->execute();
  
  $data =array();
  $count = $stmt->rowCount();//レコード数取得
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $data[] = $row;//FETCH_ASSOCで配列として書き出して代入
  }

 //https://tatsuno-system.co.jp/2020/07/06/blog_-database-php/
  //DBのカラムに同じ商品名があるかチェック
  //array_column(全配列の入った変数, ‘カラム名’)で商品名だけ配列化
  $itemArray = array_column($data, 'item');
  //var_dump(in_array("カラアゲ",$itemArray,true));

$item = '';
$category='';
$material = '';
$plice = '';
$err = ['item'=>'','cat'=>'','mat'=>'','plice'=>'','imgsize'=>''];
$dberr = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

  $item = html_escape($_POST['stock']);
  $category = html_escape($_POST['category']);
  $material = html_escape($_POST['material']);
  $plice = html_escape($_POST['stockplice']);
  $plice = mb_convert_kana($plice, 'n', 'UTF-8');
  $image = $_FILES['stockimg'];
  $image['name'] = html_escape($image['name']);
  //英小文字に変換
  $image['name'] = strtolower($image['name']);
  $plice = strtolower($plice);

  //ファイル名と拡張子を切り分けて.を除去、ピリオドを重複させない為
  $image_parts = pathinfo($image['name']);
  //$extension = $image_parts['extension'];
  $extension = 'jpg';
  $image_name = $image_parts['filename'];
  $image_name = str_replace('.','',$image_name);
  //base64でencode追加
  $image_name = base64_encode($image_name);

  //エラー対処
  if(!check_word($item,25)){
    $err['item'] = '空文字か入力値が超えています';
  } elseif (in_array($item,$itemArray,true)) {
    //DBに同じ名前を入れさせない
    $err['item']= '同じ名の商品があります';
  } else {
    $err['item']= '';
  }
  if($category === ''){
    $err['cat'] = '選択してください';
  } else {
    $err['cat']= '';
  }
  if($material === ''){
    $err['mat'] = '選択してください';
  } else {
    $err['mat']= '';
  }
  if(!check_word($plice,8)){
    $err['plice'] = '空文字か入力値が超えています';
  } else {
    $err['plice']= '';
  }
  if($image['size'] > 1000000 && $image['size'] == 0){
    $err['imgsize'] = '画像が選択されていないかサイズが1MBを超えています';
  } elseif(file_exists('./img_up/'.$image_name.'.'.$extension) === TRUE) {
    //file_exists関数でディレクトリ内を調べて、同じファイル名があった場合はアップさせない
    $err['imgsize'] = '同名のファイルがあります。違うファイル名にしてください。';
  } else {
    $err['imgsize'] = '';
  }

//empty($err)ではダメで下の書き方でtrueになった
  if($err['item']== '' && $err['cat']== '' && $err['mat']== '' && $err['imgsize'] == '' && $err['plice']== ''){
    //デコードして画像アップ
    $image_name = base64_decode($image_name);
    move_uploaded_file($image['tmp_name'],'./img_up/'.$image_name.'.'.$extension);
    $dberr = '<img class="thumb" src="'.$img_path.$image_name.'.'.$extension.'" alt="">商品「'.$item.'」は正常にUPされました';

  //DBに入力データを入れる
  $sql = "INSERT INTO menulist(item,image,category,material,plice) VALUE(:item,:image,:category,:material,:plice)";
  $stmt_in = $dbh->prepare($sql);
  $stmt_in->bindValue(':item',$item,PDO::PARAM_STR);
  //DBには画像ファイル名のみUP
  $stmt_in->bindValue(':image',$image_name.'.'.$extension,PDO::PARAM_STR);
  $stmt_in->bindValue(':category',$category,PDO::PARAM_STR);
  $stmt_in->bindValue(':material',$material,PDO::PARAM_STR);
  $stmt_in->bindValue(':plice',$plice,PDO::PARAM_INT);
  $stmt_in->execute();
  }
//再読み込みを防ぐ為、同ページだけど飛ばす
  header('Location:/menulist/control/control_top.php');
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
  <title>DBテスト管理画面</title>
  <link rel="stylesheet" href="/menulist/common/sanitize.css">
  <link rel="stylesheet" href="/menulist/common/style.css">
  <link rel="stylesheet" href="/menulist/common/control.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="/menulist/favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">
    <header id="header">
      <p id="logout"><a href="logout.php">ログアウト</a></p>
      <h1 class="topTitle">DBテスト管理画面</h1>
      <p class="center notice1">入力の場合IDの設定は不要、金額は数値のみで<br>ブラウザの再読み込みはしないでください。エラーになります。</p>
      <p class="center notice2">IDで商品を検索してください</p>
    </header>

    <main id="main">
      <ul class="formChange">
        <li data-id="insart" class="act">入力</li>
        <li data-id="update">変更</li>
        <li data-id="deleteForm">削除</li>
      </ul>

      <!-- 追加入力 -->
      <form class="changeBox" id="insart" method="post" action="" enctype="multipart/form-data">
        <div class="formLow">
          <div class="stockId">
            <p>ID</p>
            <p class="formInput">-</p>
          </div>
          <div class="stock">
            <label>商品名</label>
            <input type="text" name="stock">
            <p><?php echo $err['item']; ?></p>
          </div>
          <div class="stockCategory">
            <label>カテゴリー</label>
            <select name="category">
              <option value="" selected="selected">選択する</option>
              <!-- selectはインクルード -->
              <?php include_once(dirname(__FILE__).'/select_cat.php'); ?>

            </select>
            <p><?php echo $err['cat']; ?></p>
          </div>
          <div class="materialForm">
            <label>素材</label>
            <select name="material">
                <option value="" selected="selected">選択する</option>
                <!-- selectはインクルード -->
                <?php include_once(dirname(__FILE__).'/select_mt.php'); ?>

            </select>
            <p><?php echo $err['mat']; ?></p>
          </div>
          <div class="stockplice">
            <label>金額</label>
            <input type="text" name="stockplice">
            <p><?php echo $err['plice']; ?></p>
          </div>
        </div>
        <!-- //.formLow -->
        <div class="center">
          <label>商品画像:サイズ横640px縦420px</label><br>
          <input type="file" name="stockimg">
          <p><?php echo $err['imgsize']; ?></p>
          <?php echo $dberr; ?>
        </div>
        <input class="toBtn" type="submit" value="追加">
      </form>
      <!-- //#insartBox -->

      <!-- 変更入力 -->
      <!-- 検索 -->
      <div id="update" class="changeBox">
        <form method="post" action="/menulist/control/change.php">
          <div class="formLow">
            <label class="sarchId">ID</label>
            <input class="inputId" type="text" name="stockid">
          </div>
          <input class="toBtn" type="submit" value="検索">
        </form>
      </div>
      <!-- //#updateId -->

      <!-- 削除 -->
      <div id="deleteForm" class="changeBox">
        <form method="post" action="/menulist/control/delete.php">
          <div class="formLow">
            <label class="sarchId">ID</label>
            <input class="inputId" type="text" name="stockid">
          </div>
          <input class="toBtn" type="submit" value="検索">
        </form>

      </div>
      <!-- //#deleteForm -->

      <!-- メニュー一覧データ -->
      <div class="stockBox">
        <!-- カテゴリー絞り込みタグ -->
        <ul class="topNav">
        <?php for($i = 0; $i < count($cat_list); $i++): ?>
          <li id="<?php echo 'cat_select_'.$i; ?>"><?php echo $cat_list[$i]; ?></li>
        <?php endfor; ?>
        <li id="all">ALL</li>
        </ul>
        <p class="totalStock"><span>全登録数</span><?php echo $count; ?>項目</p>
        <p id="catShow"></p>
        <table id="stockList">
          <tr>
            <th class="stocklistId">ID</th><th class="stocklist">商品名</th><th class="stockImg">商品画像</th><th class="stocklisutoCat">カテゴリー</th><th class="stocklistMat">素材</th><th class="stocklistPlice">金額</th>
          </tr>
          <!-- foreachの外にforで囲み、カテゴリー数をループ -->
          <!-- foreachの中に$cat_list[$i]を入れるとエラーになる -->
          <?php for($i = 0; $i < count($cat_list); $i++): ?>
            <?php 
              $cat_item = $cat_list[$i];
              $cat_select = 'cat_select_'.$i;
               ?>
            <?php foreach($data as $row): ?>
              <?php if($row['category'] == $cat_item): ?>
              <tr class="<?php echo $cat_select; ?>">
                <td class="stocklistId"><?php echo $row['ID']; ?></td>
                <td class="stocklist"><?php echo $row['item']; ?></td>
                <td class="stockImg"><img src="<?php echo $img_path.$row['image']; ?>" alt=""></td>
                <td class="stocklisutoCat"><?php echo $row['category']; ?></td>
                <td class="stocklistMat"><?php echo $row['material']; ?></td>
                <td class="stocklistPlice"><?php echo number_format($row['plice']); ?>円</td>
              </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endfor; ?>

        </table>
      </div>
      <!-- //.stockBox -->
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
