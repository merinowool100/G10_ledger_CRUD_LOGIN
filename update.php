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
    $id = $_POST['id'];          // 更新するレコードのID
    $amount = $_POST['amount'];  // POSTで送られた金額
    $date = $_POST['date'];      // POSTで送られた日付
    $item = $_POST['item'];      // POSTで送られた項目名

    // ① レコードが存在するか確認
    $stmt = $pdo->prepare('SELECT * FROM ledger WHERE lid=:lid AND id=:id');
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // ② レコードが存在する場合、更新
    if ($record) {
        // 更新処理
        $stmt = $pdo->prepare('UPDATE ledger SET amount=:amount, date=:date, item=:item WHERE id=:id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':item', $item, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            exit("SQL Error: " . print_r($error, true));
        }

        // 更新後のレコードのtotal_amountを再計算
        // まずはそのレコードより前のレコードを取得
        $stmt = $pdo->prepare('SELECT id, amount, total_amount FROM ledger WHERE lid=:lid AND date <= :date ORDER BY date ASC');
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);  // 更新されたレコードの日付より古いデータ
        $stmt->execute();

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

    // 登録後にリダイレクトなど
    header('Location: index.php?year=' . $currentYear . '&month=' . $currentMonth);
    exit;
}

?>
