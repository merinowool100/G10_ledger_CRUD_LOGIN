<?php

//0. 関数読み込み---------------------------------------
require_once('funcs.php');

//1. POSTデータ取得-------------------------------------
$date = $_POST["date"];
$item = $_POST["item"];
$type = $_POST["type"];
$amount = $_POST["amount"];

//2. DB接続---------------------------------------------
$pdo = db_conn();

//３．データ登録 **DB name変える必要---------------
$stmt = $pdo->prepare(
    'INSERT INTO
        ledger(
            date, item, type, amount
        )
    VALUES (
            :date, :item, :type, :amount
        );'
);

// 数値の場合 PDO::PARAM_INT
// 文字の場合 PDO::PARAM_STR
$stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':item', $item, PDO::PARAM_STR);
$stmt->bindValue(':type', $type, PDO::PARAM_STR);
$stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
$status = $stmt->execute(); //実行

//４．データ登録処理後-----------------------------------
if ($status === false) {
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    header('Location: index.php');
    exit();
}
