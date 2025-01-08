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
            <div style="text-align:center; margin-bottom: 5px;margin-top:5px; font-size: 24px;">New record</div>
            <form action="insert.php" method="POST">
                <table>
                    <tr>
                        <td style="width:120px;">Date</td>
                        <td style="width:300px;"><input type="date" name="date" style="width:280px;"></td>
                    </tr>
                    <tr>
                        <td>Item</td>
                        <td><input type="text" name="item" style="width:280px;"></td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td>
                            <input type="radio" name="type" value="expense" required>Expense
                            <input type="radio" name="type" value="income" required>Income
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td><input type="number" name="amount" style="width:280px;"></td>
                    </tr>
                </table>
                <div style="text-align:center; margin-top: 20px; margin-bottom:10px;">
                    <button type="submit" style="margin: 0 auto;">POST</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>