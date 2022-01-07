<?php
try{
  include_once(dirname(__FILE__).'/control/db_join.php');
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

} catch (PDOException $e){
  print($e->getMessage());
  die();
}

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

  //検索フォーム
  $cat_name = '';
  $mt_name = '';
  $sarch_plice = '';

  $sarch_cat = ' none';
  $sarch_mt = ' none';
  $sarch_cat_mt = ' none';
  $sarch_class = 'none';

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  if(isset($_GET['cat']) || $_GET['material']){
    $cat_name = html_escape($_GET['cat']);
    $mt_name =html_escape($_GET['material']);
    $sarch_class = '';
  }

    if($cat_name && $mt_name== ''){
      $sarch_cat = '';
      $sarch_mt = ' none';
      $sarch_cat_mt = ' none';
    } elseif($mt_name && $cat_name == '') {
      $sarch_mt = '';
      $sarch_cat = ' none';
      $sarch_cat_mt = ' none';
    } elseif($cat_name !== '' && $mt_name !== ''){
      $sarch_cat_mt = '';
      $sarch_mt = ' none';
      $sarch_cat = ' none';
    } else {
      $sarch_cat_mt = ' none';
      $sarch_mt = ' none';
      $sarch_cat = ' none';
    }
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
    <header id="header">
      <h1 class="topTitle">DBテスト変更メニュー</h1>
      <p class="center">ブラウザの再読み込みボタンは使わないでください。エラーになります。</p>
      <nav>
        <ul class="topNav">
          <!-- $cat_list配列はdb_join.phpに記述 -->
          <!-- href=#だと検索の時、挙動が怪しいのでdata属性にした -->
          <?php for($i = 0; $i < count($cat_list); $i++): ?>
          <li>
            <a data-id="<?php echo $category_id[$i]; ?>"><?php echo $cat_list[$i]; ?></a>
          </li>
          <?php endfor; ?>
        </ul>
      </nav>
<!-- 検索ボタン -->
<div id="sarch" class="btn"><a href="dummy.html">検索BOX</a></div>

<!-- 検索BOX -->
<div id="sarchBox">
  <h3>AND検索BOX</h3>
  <p class="center">カテゴリーと素材の片方だけ、もしくは両方の選択ができます</p>
  <form id="andSarch" method="get" action="">
    <div id="ones">
      <div id="sarchCat" class="ones__div">
        <p class="onesText">カテゴリー</p>
        <select name="cat">
          <option value="" selected="selected">選択する</option>
          <!-- selectはインクルード -->
          <?php include_once(dirname(__FILE__).'/control/select_cat.php'); ?>
        </select>
      </div>
      <!-- //.ones__div -->
      <img src="image/icon_plus.svg" alt="">
      <div id="sarchMaterial" class="ones__div">
        <p class="onesText">素材</p>
        <select name="material">
          <option value="" selected="selected">選択する</option>
          <!-- selectはインクルード -->
          <?php include_once(dirname(__FILE__).'/control/select_mt.php'); ?>
        </select>
      </div>
      <!-- //.ones__div -->
    </div>
    <!-- //#ones -->
    <div id="btn"><input id="sbmBtn" type="submit" value="この条件で検索する"></div>
  </form>


  <!-- 検索結果 -->
  <div id="mixdata_response">
  <!-- 結果を出力する -->
      <section id="sarchMenu" class="<?php echo $sarch_class; ?>">
        <h2 class="catTitle"><span>SARCH</span>検索結果</h2>
        <p id="sarchCount"></p>
        <!-- カテゴリーの検索結果 -->
        <ul class="menuBox<?php echo $sarch_cat; ?>">
        <?php foreach($data as $row): ?>
          <?php if($row['category'] == $cat_name): ?>
            <li class="selectOrder">
              <div class="img">
                <img src="<?php echo $img_path.$row['image']; ?>" alt="">
                <p class="cart">注文する</p>
              </div>
              <div class="details">
                <p class="material"><?php echo $row['material']; ?></p>
                <p class="menuName">商品名<span><?php echo $row['item']; ?></span></p>
                <dl>
                  <dt>金額</dt>
                  <dd><span class="orderPlic"><?php echo number_format($row['plice']); ?></span>円<span class="smallText">(税込)</span></dd>
                </dl>
              </div>
            </li>
          <?php endif; ?>
          <?php endforeach; ?>
        </ul>

        <!-- 素材の検索結果 -->
        <ul class="menuBox<?php echo $sarch_mt; ?>">
        <?php foreach($data as $row): ?>
            <?php if($row['material'] == $mt_name): ?>
              <li class="selectOrder">
                <div class="img">
                  <img src="<?php echo $img_path.$row['image']; ?>" alt="">
                  <p class="cart">注文する</p>
                </div>
                <div class="details">
                  <p class="material"><?php echo $row['material']; ?></p>
                  <p class="menuName">商品名<span><?php echo $row['item']; ?></span></p>
                  <dl>
                    <dt>金額</dt>
                    <dd><span class="orderPlic"><?php echo number_format($row['plice']); ?></span>円<span class="smallText">(税込)</span></dd>
                  </dl>
                </div>
              </li>
              <!-- //.selectOrder -->
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
        
      <!-- カテゴリーand素材の検索結果 -->
        <ul class="menuBox<?php echo $sarch_cat_mt; ?>">
        <?php foreach($data as $row): ?>
        <?php if($row['category'] == $cat_name && $row['material'] == $mt_name): ?>
          <li class="selectOrder">
            <div class="img">
              <img src="<?php echo $img_path.$row['image']; ?>" alt="">
              <p class="cart">注文する</p>
            </div>
            <div class="details">
              <p class="material"><?php echo $row['material']; ?></p>
              <p class="menuName">商品名<span><?php echo $row['item']; ?></span></p>
              <dl>
                <dt>金額</dt>
                <dd><span class="orderPlic"><?php echo number_format($row['plice']); ?></span>円<span class="smallText">(税込)</span></dd>
              </dl>
            </div>
          </li>
          <!-- //.selectOrder -->
        <?php endif; ?>
        <?php endforeach; ?>
        </ul>
    </section>
    <!-- // #sarchMenu-->
    </div>
    <!-- //#mixdata_response -->
</div>
<!-- // #sarchBox-->

</header>

    <main id="main">
      <!-- $cat_list配列はdb_join.phpに記述 -->
      <?php for($i = 0; $i < count($cat_list); $i++): ?>
        <?php $cat_item = $cat_list[$i]; ?>
      <section id="<?php echo $category_id[$i]; ?>">
        <h2 class="catTitle"><span>Category</span><?php echo $cat_list[$i]; ?></h2>
        <ul class="menuBox">
        <?php foreach($data as $row): ?>
          <?php if($row['category'] == $cat_item): ?>
            <li class="selectOrder">
              <div class="img">
                <img src="<?php echo $img_path.$row['image']; ?>" alt="">
                <p class="cart">注文する</p>
              </div>
              <div class="details">
                <p class="material"><?php echo $row['material']; ?></p>
                <p class="menuName">商品名<span><?php echo $row['item']; ?></span></p>
                <dl>
                  <dt>金額</dt>
                  <dd><span class="orderPlic"><?php echo number_format($row['plice']); ?></span>円<span class="smallText">(税込)</span></dd>
                </dl>
              </div>
            </li>
            <!-- //.selectOrder -->
          <?php endif; ?>
        <?php endforeach; ?>
        </ul>
      </section>
    <?php endfor; ?>

    </main>

    <div id="orderChangeBox">
      <h2 class="topTitle">注文追加</h2>
      <p class="center">メニューの個数の変更ができます。選び直したい場合は取消ボタンでメニュー画面に戻ります。<br>決まりましたら注文リストボタンを押してください。<br>キャンセルは注文リストの削除ボタンでもキャンセルできます。</p>
      <form method="get" action="/menulist/order_count.php">
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
            </tr>
          <?php endfor; ?>
        </table>

        <input id="count" type="hidden" name="count_menu" value="<?php echo $count_menu-1; ?>">
        <!-- <div id="toList">メニューを見る</div> -->
        <input class="toBtn" type="submit" value="注文リストに入れる">
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
    jQuery(function(){
    //このページのフォームを初期値非表示
    jQuery('#orderChangeBox').css('display','none');
    //全記事非表示でもラストだけ表示されるのは、base.jsでappendによって書き出されているから
    //非表示のメニューを持っていくのはJSONを残す為
    jQuery('table tr').css('display','none');
    
    jQuery(document).on('click', '.cancel', function(){
      //デリートボタンを押したtrのみを削除
      jQuery(this).parents('tr').remove();
      //現在のカウントを取得
      count = jQuery('#count').attr('value');

      //削除によって揃わなくなった個数と順番の連動を合わせる
      for(let i=0; i < count; ++i){
        jQuery('input[name^="title"]').eq(i).attr('name','title'+i);
        jQuery('input[name^="plice"]').eq(i).attr('name','plice'+i);
        jQuery('input[name^="num"]').eq(i).attr('name','num'+i);
        jQuery('input[name^="img"]').eq(i).attr('name','img'+i);
        }

      //1個ずつ持っていく数を減らす
      --count; 
      jQuery('#count').attr('value',count);

      jQuery('#header,#main').css('display','block');
      jQuery('#orderChangeBox').css('display','none');

    });
  })
</script>
</body>
</html>