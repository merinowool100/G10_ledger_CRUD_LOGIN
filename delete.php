<?php

$id = $_GET["id"];

//関数読み込み---------------------------------------
require_once('funcs.php');

//DB接続---------------------------------------------
$pdo = db_conn();

//３．データ登録SQL作成
$update = $pdo->prepare("DELETE FROM ledger WHERE id=:id;");

$update->bindValue(':id', $id, PDO::PARAM_INT);

$status = $update = $update->execute();


//４．データ登録処理後
if ($status === false) {
    //*** function化する！******\
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    //*** function化する！*****************
    header('Location: index.php');
    exit();
}
