<?php
//セッション破棄
session_start();
$_SESSION = array();
if(isset($_COOKIE[session_name()]) === true){
    setcookie(session_name(),'',time()-4200,'/');
}
session_destroy();

header('Location: index.php');