<?php

//0. 関数読み込み---------------------------------------
require_once('funcs.php');

//1. POSTデータ取得-------------------------------------
$date = $_POST['date'];
$item = $_POST['item'];
$type = $_POST['type'];
$amount = $_POST['amount'];
$id = $_POST['id'];

//2. DB接続---------------------------------------------
$pdo = db_conn();

//３．データ更新-----------------------------------------
$update = $pdo->prepare('UPDATE ledger SET date=:date, item=:item, type=:type, amount=:amount WHERE id=:id;');
$update->bindValue(':date', $date, PDO::PARAM_STR);
$update->bindValue(':item', $item, PDO::PARAM_STR);
$update->bindValue(':type', $type, PDO::PARAM_STR);
$update->bindValue(':amount', $amount, PDO::PARAM_INT);
$update->bindValue(':id', $id, PDO::PARAM_INT);

$status = $update->execute();


//４．データ登録処理後-------------------------------------
if ($status === false) {
    $error = $update->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    header('Location: index.php');
    exit();
}
