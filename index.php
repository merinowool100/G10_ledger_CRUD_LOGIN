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

// 前月と次月の計算
$prevYear = $currentYear;
$prevMonth = $currentMonth - 1;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextYear = $currentYear;
$nextMonth = $currentMonth + 1;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// DB接続
$pdo = db_conn();

// SQLからデータ取得
$stmt = $pdo->prepare('SELECT * FROM ledger WHERE lid=:lid AND YEAR(date)=:year AND MONTH(date)=:month ORDER BY date ASC');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':year', $currentYear, PDO::PARAM_INT);
$stmt->bindValue(':month', $currentMonth, PDO::PARAM_INT);
$status = $stmt->execute();

// データ表示
$view = '
    <div style="text-align:center; font-size: 48px; margin: 20px; font-family:Sour Gummy;">Ledger</div>
    
    <div style="text-align:center; font-size: 20px; margin-bottom: 20px;">
        <a href="?year=' . $prevYear . '&month=' . $prevMonth . '" style="font-size: 20px; text-decoration: none; margin-right: 100px;font-family:Sour Gummy;">&lt;&lt; Prev</a>
        <span>' . $currentYear . '年' . $currentMonth . '月</span>
        <a href="?year=' . $nextYear . '&month=' . $nextMonth . '" style="font-size: 20px; text-decoration: none; margin-left: 100px;font-family:Sour Gummy;">Next &gt;&gt;</a>
    </div>
';

if ($status === false) {
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    $view .= '
    <table style="max-width: 1000px; margin:0 auto;border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th style="width:120px; text-align:center;">Date</th>
                <th style="width:200px; text-align:center;">Item</th>
                <th style="width:150px; text-align:center;">Amount</th>
                <th style="width:150px; text-align:center;">Balance</th>
                <th style="width:150px; text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amount = $result['amount'];
        $total_amount = $result['total_amount']; // DBから取得したtotal_amountをそのまま使用

        // 桁区切り
        $formatted_amount = number_format($amount);
        $formatted_total = number_format($total_amount);

        // 負の値に赤色を付けるスタイル
        $amount_style = ($amount < 0) ? "color: red;" : "";
        $total_style = ($total_amount < 0) ? "color: red;" : "";

        $view .= '<tr>';
        $view .= "<td>" . h($result['date']) . "</td>";
        $view .= "<td>" . h($result['item']) . "</td>";
        $view .= "<td style='text-align:right; $amount_style'>" . $formatted_amount . "</td>";
        $view .= "<td style='text-align:right; background-color: lightgray; font-weight: bold; $total_style'>" . $formatted_total . "</td>";
        $view .= "<td style='text-align:center;'>";
        $view .= '<a href="detail.php?id=' . $result["id"] . '">[Update]</a>';
        $view .= ' ';
        $view .= '<a href="delete.php?id=' . $result["id"] . '">[Delete]</a>';
        $view .= "</td>";
        $view .= "</tr>";
    }

    $view .= '</tbody></table>';
}

$view .= '</div>';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <title>Ledger</title>
    <style>
        div {
            font-size: 16px;
        }

        table,
        tbody {
            border-collapse: collapse;
            border: 1px solid #000;
            padding: 10px;
        }

        tr {
            border: 1px dotted gray;
            padding: 5px;
        }

        td,
        th {
            padding: 5px;
        }
    </style>
</head>

<body id="main">
    <div>
        <?= $view ?>
    </div>
    <div style="text-align:center;">
        <a href="write.php"><button style="border-radius:5px; border:0px; background-color: lightgray; margin:10px;">+</button></a>
    </div>
    <div style="text-align:center;font-family:'Sour Gummy';">
        <a href="logout.php" style="text-decoration:none;">>>> Logout</a>
    </div>
</body>

</html>