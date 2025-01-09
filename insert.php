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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];  // POSTで送られた金額
    $date = $_POST['date'];      // POSTで送られた日付
    $item = $_POST['item'];      // POSTで送られた項目名

    // ① DBにレコードがあるか確認
    $stmt = $pdo->prepare('SELECT 1 FROM ledger WHERE lid=:lid AND YEAR(date)=:year AND MONTH(date)=:month LIMIT 1');
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':year', $currentYear, PDO::PARAM_INT);
    $stmt->bindValue(':month', $currentMonth, PDO::PARAM_INT);
    $stmt->execute();
    $recordExists = $stmt->fetchColumn();

    // ② レコードがない場合、新規レコードを追加
    if (!$recordExists) {
        // 新しいレコードを追加 (total_amount = amount)
        $stmt = $pdo->prepare('INSERT INTO ledger (lid, date, amount, item, total_amount) VALUES (:lid, :date, :amount, :item, :total_amount)');
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindValue(':item', $item, PDO::PARAM_STR);
        $stmt->bindValue(':total_amount', $amount, PDO::PARAM_INT);  // total_amountはamountと同じ
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            exit("SQL Error: " . print_r($error, true));
        }
    } else {
        // ③ レコードがすでにある場合、新規レコードを追加 (total_amountは初期値0)
        // 新規レコードを追加 (total_amountは0)
        $stmt = $pdo->prepare('INSERT INTO ledger (lid, date, amount, item, total_amount) VALUES (:lid, :date, :amount, :item, :total_amount)');
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindValue(':item', $item, PDO::PARAM_STR);
        $stmt->bindValue(':total_amount', 0, PDO::PARAM_INT);  // 新規レコードはtotal_amountを0で保存
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            exit("SQL Error: " . print_r($error, true));
        }

        // 新規レコードが追加された後、全レコードを取得してtotal_amountを再計算
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
    }

    // 登録後にリダイレクトなど
    header('Location: index.php?year=' . $currentYear . '&month=' . $currentMonth);
    exit;
}

?>
