<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
</head>

<body>
<div style="display:flex; justify-content:center; flex-direction: column; text-align:center;">    
<div style="width:500px; margin:0 auto;">
        <div style="margin:20px; font-weight:bold;">Login</div>
        <form name="form1" action="login_act.php" method="POST">
            <div>
                <table style="margin:0 auto;">
                    <tr>
                        <td style="width:100px;">ID:</td>
                        <td><input type="text" name="lid" style="width:280px;"></td>
                    </tr>
                    <tr>
                        <td style="width:100px;">PW:</td>
                        <td><input type="password" name="lpw" style="width:280px;"></td>
                    </tr>
                </table>
            </div>
            <input type="submit" value="LOGIN">
        </form>
        <br>
        <div>
            <a href="registration.php" style="text-decoration:none;">>>> Registration</a>
        </div>
    </div>
    </div>
</body>

</html>