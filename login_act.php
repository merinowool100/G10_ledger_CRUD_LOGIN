<?php
//SESSSION開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
session_start();

//lidとlpwをPOSTで受取 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];

//funcs読み込み >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_once('funcs.php');

//DB接続 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$pdo = db_conn();

//lidがDBに存在するか確認 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$stmt = $pdo->prepare('SELECT * FROM ledger_user WHERE lid=:lid');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$status = $stmt->execute();

//SQL実行のエラーチェック >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($status === false) {
    sql_error($stmt);
}

//SQLからデータ取得 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$val = $stmt->fetch();

//
if($val && password_verify($lpw, $val['lpw'])){
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['lid'] = $val['lid'];
    redirect('index.php');
}else{
    redirect('login.php');
}

exit();
