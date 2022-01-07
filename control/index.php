<?php
//XSS
function html_escape($word){
    return htmlspecialchars($word,ENT_QUOTES,'UTF-8');
}

//トークン生成
function getCSRFToken(){
  $nonce = base64_encode(openssl_random_pseudo_bytes(48));
  setcookie('XSRF-TOKEN', $nonce);
 return $nonce;
 }
$token = getCSRFToken();
$token = html_escape($token);

$logid = '';
$pass = '';
$messege = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  //isset入れると空文字条件が効かない
  $logid = $_POST['logid'];
  $pass = $_POST['pass'];
  $logid = html_escape($logid);
  $pass = html_escape($pass);

  //IDとパスワード設定
  if($logid === 'sample' && $pass === 'sample'){
    session_start();
    $_SESSION['login'] = 1;

    //postトークン追加
    function validateCSRFToken ($post_token){
      return isset($_COOKIE['XSRF-TOKEN']) && $_COOKIE['XSRF-TOKEN'] === $post_token;
      }
      if(isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])){
          echo '';
      } else {
          echo 'トークンが不正です。';
          exit();
      }
      header('Access-Control-Allow-Origin: sample_url');
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header('X-Frame-Options: SAMEORIGIN');
      //postトークンここまで
        
    //ファイル一覧へリロード
    header('Location: control_top.php');
    exit();
    } elseif ($logid === '' || $pass === ''){
        $messege = '<p class="center">IDとパスワードを空文字にしないで入力してください</p>';
    } else {
        $messege = '<p class="center">IDかパスワード、もしくは両方違います</p>';
  }
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
      <h1 class="topTitle">DBテストログイン画面</h1>
      <p class="center notice1">テストメニュー入力</p>
    </header>

    <main id="main">
      <div id="sarchBox">
        <form id="andSarch" method="post" action="">
          <div id="ones">
            <!-- トークン追加 -->
            <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
            <div class="ones__div">
              <label class="onesText">ID</label>
              <input type="text" name="logid">
            </div>
            <div class="ones__div">
              <label class="onesText">パスワード</label>
              <input type="password" name="pass">
            </div>
        </div>
        <div id="login"><input type="submit" value="ログイン"></div>
      </form>
      <?php echo $messege; ?>
      </div>
    </main>

    <footer id="footer">
      <small>DBテストメニューsystem</small>
    </footer>

  </div>
  <!-- //#wrapper -->
<script src="/menulist/common/jquery-3.6.0.min.js"></script>
<script src="/menulist/common/base.js"></script>
</body>
</html>