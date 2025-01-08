<?php

/**
 * [ここでやりたいこと]
 * 1. クエリパラメータの確認 = GETで取得している内容を確認する
 * 2. select.phpのPHP<?php ?>の中身をコピー、貼り付け
 * 3. SQL部分にwhereを追加
 * 4. データ取得の箇所を修正。
 */

$id = $_GET["id"];

//0. 関数読み込み---------------------------------------
require_once('funcs.php');

//1. DB接続---------------------------------------------
$pdo = db_conn();

//2. GETしたidのデータをSQLから取得
$stmt = $pdo->prepare('SELECT * FROM ledger WHERE id= :id;');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();


//3. 結果をresultに代入
$result = '';
if ($status === false) {
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    $result = $stmt->fetch();
}

?>
<!--
２．HTML
以下にindex.phpのHTMLをまるっと貼り付ける！
(入力項目は「登録/更新」はほぼ同じになるから)
※form要素 input type="hidden" name="id" を１項目追加（非表示項目）
※form要素 action="update.php"に変更
※input要素 value="ここに変数埋め込み"
-->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>データ登録</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        div {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div style="height: 500px; display:flex; justify-content: center; align-items:center;">
        <div id="container" style="width: 450px; height: 300px;display: flex; flex-direction: column; justify-content: center; align-items: center; border:1px solid white; border-radius: 10px;box-shadow: 0 10px 25px 0 rgba(0, 0, 0, .5);">
            <div style="text-align: left; width: 100%; padding-left: 20px;"><a href="index.php">Back to the ledger</a></div>
            <div style="text-align:center; margin-bottom: 5px;margin-top:5px; font-size: 24px;">Record</div>
            <form action="update.php" method="POST">
                <table>
                    <tr>
                        <td style="width:120px;">Date</td>
                        <td style="width:300px;"><input type="date" name="date" value="<?= $result['date'] ?>" style="width:280px;"></td>
                    </tr>
                    <tr>
                        <td>Item</td>
                        <td><input type="text" name="item" value="<?= $result['item'] ?>" style="width:280px;"></td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td>
                            <input type="radio" name="type" value="expense" <?= ($result['type'] == 'expense') ? 'checked' : '' ?> required>Expense
                            <input type="radio" name="type" value="income" <?= ($result['type'] == 'income') ? 'checked' : '' ?> required>Income
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td><input type="number" name="amount" value="<?= $result['amount'] ?>" style="width:280px;"></td>
                    </tr>
                </table>
                <input type="hidden" name="id" value="<?= $result['id'] ?>"></label><br>
                <div style="text-align:center; margin-top: 10px; margin-bottom:10px;">
                    <button type="submit" style="margin: 0 auto;">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>