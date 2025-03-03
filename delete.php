<?php
// SESSION開始
session_start();

// 関数読み込み
require_once('funcs.php');

// ログインチェック
loginCheck();

// ユーザーのlidを取得
$lid = $_SESSION['lid'];

// 現在の年と月を取得
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// DB接続
$pdo = db_conn();

// POSTされたデータの取得
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];  // 削除するレコードのID

    // ① 削除対象のレコードを取得
    $stmt = $pdo->prepare('SELECT * FROM ledger WHERE lid=:lid AND id=:id');
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // ② レコードが存在する場合、削除
    if ($record) {
        // 削除処理
        $stmt = $pdo->prepare('DELETE FROM ledger WHERE id=:id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            exit("SQL Error: " . print_r($error, true));
        }

        // 削除後、すべてのデータを取得してtotal_amountを再計算
        $stmt = $pdo->prepare('SELECT * FROM ledger WHERE lid=:lid ORDER BY date ASC');
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->execute();

        // total_amountの再計算
        $runningTotal = 0;
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $result['id'];
            $amount = $result['amount'];
            $runningTotal += $amount;

            // 各レコードのtotal_amountを更新
            $updateStmt = $pdo->prepare('UPDATE ledger SET total_amount=:total_amount WHERE id=:id');
            $updateStmt->bindValue(':total_amount', $runningTotal, PDO::PARAM_INT);
            $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);
            if (!$updateStmt->execute()) {
                $error = $updateStmt->errorInfo();
                exit("SQL Error: " . print_r($error, true));
            }
        }

    } else {
        // レコードが存在しない場合
        exit("Error: レコードが見つかりません。");
    }

    // 削除後にリダイレクト
    header('Location: index.php?year=' . $currentYear . '&month=' . $currentMonth);
    exit;
}

?>
