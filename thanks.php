<?php
//XSS
function html_escape($word){
  return htmlspecialchars($word,ENT_QUOTES,'UTF-8');
}

$count_menu = isset($_GET['count_menu']);
$count_menu = html_escape($_GET['count_menu']);
$count_menu = (int)$count_menu;
//var_dump($count_menu);1個足りない

$order_name = [];
$one_plice = [];
$num = [];
$sub_plise = [];

//設定したカウント数でname値を書き出し配列へ入れる
for($i = 0; $i < $count_menu+1; $i++){
$order_name[$i] = html_escape($_GET['order_name'.$i]);
$one_plice[$i] = html_escape($_GET['one_plice'.$i]);
//文字列内にある”,”を""に変換
$one_plice[$i] = str_replace(',', '',$one_plice[$i]);
$one_plice[$i] = (int)$one_plice[$i];
$num[$i] = html_escape($_GET['num'.$i]);
$num[$i] = (int)$num[$i];

$sub_plise[$i] = $one_plice[$i] * $num[$i];
}

//配列の値の合計を計算
$total_plice = array_sum($sub_plise);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DBテストメニュー変更</title>
  <link rel="stylesheet" href="common/sanitize.css">
  <link rel="stylesheet" href="common/style.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">
    <header id="header">
      <h1 class="topTitle">DBテスト完了メニュー</h1>
    </header>
    <main id="main">
      <p class="messege">ご注文を受け付けました。内容は以下の通りです。</p>
      <p class="center">テストなので表示のみです。</p>
      <table class="orderMenu">
          <?php for($i = 0; $i < $count_menu+1; $i++): ?>
            <tr>
              <th><?php echo $order_name[$i]; ?></th>
              <!-- number_format3桁区切り -->
              <td class="plice"><?php echo number_format($one_plice[$i]); ?>円<span class="smallText">(税込)</span><img src="image/icon_x.svg" alt=""></td>
              <td class="num"><?php echo $num[$i]; ?>個</td>
              <td class="plice"><?php echo number_format($sub_plise[$i]); ?>円<span class="smallText">(税込)</span></td>
            </tr>
            <?php endfor; ?>
          </table>
          <div id="total"><span>合計</span><?php echo number_format($total_plice); ?>円<span class="smallText">(税込)</span></div>

          <div id="toList"><a href="index.php">メニューに戻る</a></div>
    </main>

    <footer id="footer">
      <small>DBテストメニューsystem</small>
    </footer>
    </div>
  <!-- //#wrapper -->
<script src="common/jquery-3.6.0.min.js"></script>
<script src="common/base.js"></script>
</body>
</html>