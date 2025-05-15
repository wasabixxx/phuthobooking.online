<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ADMIN</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png"/>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,700');

        body {
        background: #c0c0c0; 
        font-family: Raleway, sans-serif;
        color: #666;
        }

        .login {
        margin: 20px auto;
        padding: 40px 50px;
        max-width: 300px;
        border-radius: 5px;
        background: #fff;
        box-shadow: 1px 1px 1px #666;
        }
        .login input {
            width: 100%;
            display: block;
            box-sizing: border-box;
            margin: 10px 0;
            padding: 14px 12px;
            font-size: 16px;
            border-radius: 2px; 
            font-family: Raleway, sans-serif;
        }

        .login input[type=text],
        .login input[type=password] {
        border: 1px solid #c0c0c0;
        transition: .2s;
        }

        .login input[type=text]:hover {
        border-color:rgb(54, 244, 111);
        outline: none;
        transition: all .2s ease-in-out;
        } 

        .login input[type=submit] {
        border: none;
        background:rgb(80, 239, 128);
        color: white;
        font-weight: bold;  
        transition: 0.2s;
        margin: 20px 0px;
        }

        .login input[type=submit]:hover {
        background:rgb(0, 221, 118);  
        }

        .login h2 {
            margin: 20px 0 0; 
            color:rgb(80, 239, 114);
            font-size: 28px;
        }

        .login p {
        margin-bottom: 40px;
        }

        .links {
        display: table;
        width: 100%;  
        box-sizing: border-box;
        border-top: 1px solid #c0c0c0;
        margin-bottom: 10px;
        }

        .links a {
        display: table-cell;
        padding-top: 10px;
        }

        .links a:first-child {
        text-align: left;
        }

        .links a:last-child {
        text-align: right;
        }

        .login h2,
        .login p,
        .login a {
            text-align: center;    
        }

        .login a {
        text-decoration: none;  
        font-size: .8em;
        }

        .login a:visited {
        color: inherit;
        }

        .login a:hover {
        text-decoration: underline;
        }
        
    </style>
</head>
<body>
    <div class="login">
        <h2>PHUTHOBOOKING  - admin</h2>
        <p>Mời ngài đăng nhập</p>
        <form action="action/login.php" method="post">
            <div>
                <label for="username" >Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password"required>
            </div>
            <input type="submit" value="Log In" />
        </form>
    </div>
</body>



</html>

