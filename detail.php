<?php
session_start();
$id = $_GET["id"];
require_once('funcs.php');
loginCheck();

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


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>データ登録</title>
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