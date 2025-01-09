<?php
//SESSION開始 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
session_start();

//関数読み込み >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_once('funcs.php');

//ログインチェック >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
loginCheck();

//ユーザーのlidを取得 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$lid = $_SESSION['lid'];

//現在の年と月を取得 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

//前月と次月の計算 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$prevYear = $currentYear;
$prevMonth = $currentMonth - 1;
if($prevMonth < 1){
    $prevMonth = 12;
    $prevYear--;
}

$nextYear = $currentYear;
$nextMonth = $currentMonth + 1;
if($nextMonth > 12){
    $nextMonth = 1;
    $nextYear++;
}



//DB接続 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$pdo = db_conn();

//SQLからデータ取得 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$stmt = $pdo->prepare('SELECT * FROM ledger WHERE lid=:lid AND YEAR(date)=:year AND MONTH(date)=:month ORDER BY date ASC;');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':year', $currentYear, PDO::PARAM_INT);
$stmt->bindValue(':month', $currentMonth, PDO::PARAM_INT);
$status = $stmt->execute();

//データ表示-----------------------------------------
$total = 0;

$view = '
    <div style="text-align:center; font-size: 24px; margin: 20px;">Ledger</div>
    
    <div style="text-align:center; font-size: 20px; margin-bottom: 20px;">
        <a href="?year=' . $prevYear . '&month=' . $prevMonth . '" style="font-size: 20px; text-decoration: none; margin-right: 100px;">&lt;&lt; Prev</a>
        <span>' . $currentYear . '年' . $currentMonth . '月</span>
        <a href="?year=' . $nextYear . '&month=' . $nextMonth . '" style="font-size: 20px; text-decoration: none; margin-left: 100px;">Next &gt;&gt;</a>
    </div>
        ';
        if($status === false){
            $error = $stmt->errorInfo();
            exit('SQLError:' . print_r($error, true));
        }else{
            $view .= '
    <table  style="max-width: 1000px; margin:0 auto;border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th style="width:120px; text-align:left;">Date</th>
                <th style="width:200px; text-align:left;">Item</th>
                <th style="width:100px; text-align:left;">Type</th>
                <th style="width:120px; text-align:left;">Amount</th>
                <th style="width:120px; text-align:left;">Total</th>
                <th style="width:150px; text-align:left;">Action</th>
            </tr>
        </thead>
        <tbody>
';
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        $amount = $result['amount'];
        $type = $result['type'];
        if ($type == 'expense') {
            $total -= $amount;
        } elseif ($type == 'income') {
            $total += $amount;
        }

        $formatted_amount = number_format($amount);
        $formatted_total = number_format($total);

        $view .= '<tr>';
        $view .= "<td>" . h($result['date']) . "</td>";
        $view .= "<td>" . h($result['item']) . "</td>";
        $view .= "<td>" . h($result['type']) . "</td>";
        $view .= "<td style='text-align:right;'>" . $formatted_amount . "</td>";
        $view .= "<td style='text-align:right; background-color: lightgray; font-weight: bold;'>" . $formatted_total . "</td>";

        $view .= "<td>";
        $view .= '<a href="detail.php?id=' . $result["id"] . '">';
        $view .= '[Update]';
        $view .= '</a>';

        $view .= ' ';

        $view .= '<a href="delete.php?id=' . $result["id"] . '">';
        $view .= '[Delete]';
        $view .= '</a></td>';

        $view .= "</tr>";
    }
}

$view .= '</tbody></table></div>'

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>一覧</title>
    <style>
        div {
            font-size: 16px;
        }

        table,
        tbody {
            border-collapse: collapse;
            border: 1px solid #000;
            padding: 8px;
        }

        tr {
            border: 1px dotted gray;
            padding: 8px;

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
        <a href="write.php"><button style='border-radius:5px; border:0px; background-color: lightgray; margin:10px;'>+</button></a>
    </div>
    <div style="text-align:center;">
        <a href="logout.php" style="text-decoration:none;">>>> Logout</a>
    </div>
</body>

</html>