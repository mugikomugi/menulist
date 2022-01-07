<?php
//jsonファイルを取得
$json =file_get_contents('./order.json');
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$json = json_decode($json,true);

//jsonの中に入ったアイテム数、タイトルから取得
$count_menu = count($json[0]);

//var_dump($json);
//PHPではJSON形式のデータをそのまま扱うことができないので配列の形にする必要があり
$order_name =array();
$num = array();
$one_plice = array();
$order_img = array();
//foreachだと値を書き出せなかった
for($i = 0; $i < $count_menu; $i++){
  array_push($order_name,$json[0][$i]);
  array_push($num,$json[1][$i]);
  array_push($one_plice,$json[2][$i]);
  array_push($order_img,$json[3][$i]);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DBテストメニュー変更</title>
  <link rel="stylesheet" href="/menulist/common/sanitize.css">
  <link rel="stylesheet" href="/menulist/common/style.css">
  <!--ファビコン32x32-->
  <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
  <div id="wrapper">

    <div id="orderChangeBox">
      <h1 class="topTitle">注文リスト</h1>
      <p class="center">メニューの追加・変更ができます。追加はメニューに戻って選んでください。<br>メニューが決まりましたら合計ボタンへ、キャンセルしたいメニューは削除を押してください。</p>
      <form method="get" action="">
        <table class="orderMenu" id="changeMenu">
        <?php for($i = 0; $i < $count_menu; $i++): ?>
            <tr>
              <th class="orderName">
                <?php echo $order_name[$i]; ?>
                <input type="hidden" name="title<?php echo $i; ?>" value="<?php echo $order_name[$i]; ?>">
              </th>
              <td class="orderImg">
                <img src="<?php echo $order_img[$i]; ?>" alt="">
                <input type="hidden" name="img<?php echo $i; ?>" value="<?php echo $order_img[$i]; ?>">
              </td>
              <td class="orderOneplice">
                <?php echo $one_plice[$i]; ?>円<span class="smallText">(税込)</span><img src="/menulist/image/icon_x.svg" alt="">
                <input type="hidden" name="plice<?php echo $i; ?>" value="<?php echo $one_plice[$i]; ?>">
              </td>
              <td class="num">
                <input type="number" name="num<?php echo $i; ?>" min="1" max="50" value="<?php echo $num[$i]; ?>">個
              </td>
              <td>
                <p class="deleteMenu">削除<img src="/menulist/image/icon_x.svg" alt=""></p>
              </td>
            </tr>
          <?php endfor; ?>
        </table>

        <input id="count" type="hidden" name="count_menu" value="<?php echo $count_menu-1; ?>">
        <div class="toList"><input id="toMenu" type="submit" value="メニューを見る"></a></div>
        <input id="checkPlice" class="toBtn" type="submit" value="合計を見る">
        <div class="toList"><a href="/menulist/">メニューをリセットする</a></div>
      </form>
    </div>
    <!-- //#orderBox -->

    <footer id="footer">
      <small>DBテストメニューsystem</small>
    </footer>

  </div>
  <!-- //#wrapper -->
<script src="/menulist/common/jquery-3.6.0.min.js"></script>
<script src="/menulist/common/base.js"></script>
<script>
  //メニューに戻る場合、submit先はjsonに格納させてメニューリストへ
  jQuery('#toMenu').on('click',function(){
    jQuery('#orderChangeBox form').attr('action','/menulist/order_count_menu.php');
  });
  //合計のsubmit先はorder.phpへ
  jQuery('#checkPlice').on('click',function(){
    jQuery('#orderChangeBox form').attr('action','/menulist/order.php');
  });
</script>
</body>
</html>