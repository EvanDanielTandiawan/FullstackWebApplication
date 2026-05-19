<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Login Akun</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0D9D9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        h2 {
            color: #5A9690;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background-color: #2F5755;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            width: 100%;
        }
        /* MOBILE */
@media (max-width: 768px) {

    header {
        padding: 15px;
    }

    header h2 {
        font-size: 1.2em;
    }

    nav a {
        padding: 8px 12px;
        font-size: 0.9em;
    }

    .management-section,
    .content-container,
    .member-container {
        flex-direction: column;
    }

    .box,
    .form-box,
    .list-box,
    .member-list-box,
    .add-member-box {
        width: 100%;
    }

    .table-container,
    .list-box,
    .member-list-box {
        overflow-x: auto;
    }

    table {
        min-width: 600px;
    }

    .menu-button,
    .submit-button,
    .add-button,
    .remove-button {
        width: 100%;
        text-align: center;
        padding: 12px;
        font-size: 1em;
    }

    input[type="text"],
    input[type="date"],
    textarea,
    select {
        font-size: 1em;
        padding: 10px;
    }
}

/* EXTRA SMALL DEVICE */
@media (max-width: 480px) {
    header h2 {
        font-size: 1em;
    }

    nav a {
        font-size: 0.85em;
    }

    h1, h2, h3 {
        font-size: 1.1em;
    }
}
    </style> -->
</head>

<body>
    <div class="form-container">
        <h2>Login Akun</h2>
        <?php
        session_start();

        if (isset($_SESSION["error_message"])) {
            echo "<script>alert('{$_SESSION['error_message']}')</script>";
            unset($_SESSION['error_message']);
        }
        ?>
        <form method="post" action="login_process.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>