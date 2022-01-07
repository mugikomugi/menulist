<?php
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
$num[$i] = (int)$num[$i];

$sub_plise[$i] = $one_plice[$i] * $num[$i];
}

//配列の値の合計を計算
$total_plice = array_sum($sub_plise);

//JSONに入れる為、配列へ
$order = [$order_name,$num,$one_plice,$order_img];
//JSON形式に変換
$json = json_encode($order,JSON_UNESCAPED_UNICODE);
//JSONへ格納
file_put_contents('./order.json',$json);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>注文リスト | DBテストメインメニュー</title>
  <link rel="stylesheet" href="common/sanitize.css">
  <link rel="stylesheet" href="common/style.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">
    <header id="header">
      <h1 class="topTitle">注文リスト</h1>
      <p class="center">確定ボタンで注文完了です。<br>変更される場合は「変更」で戻ってください。メニューを見て追加したい場合は「メニューを見る」ボタンから追加してください。</p>
      </header>

      <main id="main">
        <form method="get" action="thanks.php">
          <table class="orderMenu">
          <?php for($i = 0; $i < $count_menu+1; $i++): ?>
            <tr>
              <th><?php echo $order_name[$i]; ?><input type="hidden" name="order_name<?php echo $i; ?>" value="<?php echo $order_name[$i]; ?>"></th>
              <!-- number_format3桁区切り -->
              <td class="plice"><?php echo number_format($one_plice[$i]); ?>円<span class="smallText">(税込)</span><img src="image/icon_x.svg" alt=""><input type="hidden" name="one_plice<?php echo $i; ?>" value="<?php echo $one_plice[$i]; ?>"></td>
              <td class="num"><?php echo $num[$i]; ?>個<input type="hidden" name="num<?php echo $i; ?>" value="<?php echo $num[$i]; ?>"></td>
              <td class="plice"><?php echo number_format($sub_plise[$i]); ?>円<span class="smallText">(税込)</span><input type="hidden" name="sub_plise<?php echo $i; ?>" value="<?php echo $sub_plise[$i]; ?>"></td>
            </tr>
            <?php endfor; ?>
          </table>
          <div id="total"><span>合計</span><?php echo number_format($total_plice); ?>円<span class="smallText">(税込)</span><input type="hidden" name="total_plice" value="<?php echo $total_plice; ?>"></div>
          <!-- メニューのクリックは無いのでcount-1の調整はいらない -->
          <input id="count" type="hidden" name="count_menu" value="<?php echo $count_menu; ?>">
          <input class="toBtn" type="submit" value="注文確定する">
        </form>
        <div class="toList"><a href="order_change.php">変更する</a></div>
      </main>

      <footer id="footer">
        <small>DBテストメニューsystem</small>
      </footer>
    </div>
    <!-- //#wrapper -->
</body>
</html>