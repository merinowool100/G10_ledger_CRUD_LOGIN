<?php
// セッション開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
session_start();

// POSTで送信されたデータを受け取る >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$lid = $_POST['lid']; 
$lpw = $_POST['lpw']; 

// funcs読み込み >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_once('funcs.php');

// DB接続 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$pdo = db_conn();

// 入力されたユーザーIDがすでに存在しないか確認する >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$stmt = $pdo->prepare('SELECT * FROM ledger_user WHERE lid = :lid');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$status = $stmt->execute();

// SQL実行のエラーチェック >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($status === false) {
    sql_error($stmt);
}

// すでにIDが存在する場合はエラーメッセージを表示 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$val = $stmt->fetch();
if ($val['id'] != '') {
    echo 'このユーザーIDはすでに使用されています。';
    exit();
}

// パスワードをハッシュ化 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$hashed_password = password_hash($lpw, PASSWORD_DEFAULT);

// ユーザーをDBに登録 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$stmt = $pdo->prepare('INSERT INTO ledger_user (lid, lpw) VALUES (:lid, :lpw)');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
$status = $stmt->execute();

// SQL実行のエラーチェック
if ($status === false) {
    sql_error($stmt);
} else {
    // 登録成功後、ログインページにリダイレクト
    redirect('login.php');
}

// 終了
exit();
?>
