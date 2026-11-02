<?php

session_start();

include "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username == "" || $password == "") {

        $error = "Please enter username and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password FROM users WHERE username = ?"
        );

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if ($password === $user["password"]) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Incorrect username or password.";

            }

        } else {

            $error = "Incorrect username or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Church Management System</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            padding: 0;

            min-height: 100vh;

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            background: linear-gradient(
                135deg,
                #1e3c72,
                #2a5298
            );

        }


        /* LOGIN CARD */

        .login-container {

            width: 400px;

            max-width: 90%;

            background: white;

            padding: 40px;

            border-radius: 15px;

            box-shadow:
                0 10px 35px rgba(0,0,0,0.25);

            text-align: center;

        }


        /* CHURCH ICON */

        .church-icon {

            width: 80px;

            height: 80px;

            margin: 0 auto 15px;

            border-radius: 50%;

            background: #1e3c72;

            display: flex;

            justify-content: center;

            align-items: center;

            font-size: 40px;

        }


        /* TITLE */

        h1 {

            margin: 10px 0 5px;

            color: #1e3c72;

            font-size: 25px;

        }


        .subtitle {

            color: #777;

            font-size: 14px;

            margin-bottom: 30px;

        }


        /* INPUT GROUP */

        .input-group {

            text-align: left;

            margin-bottom: 20px;

        }


        .input-group label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

            color: #333;

        }


        .input-group input {

            width: 100%;

            padding: 13px;

            border: 1px solid #ccc;

            border-radius: 7px;

            font-size: 15px;

            outline: none;

        }


        .input-group input:focus {

            border-color: #1e3c72;

            box-shadow:
                0 0 5px rgba(30,60,114,0.25);

        }


        /* PASSWORD CONTAINER */

        .password-container {

            position: relative;

        }


        .password-container input {

            padding-right: 75px;

        }


        /* SHOW PASSWORD BUTTON */

        .show-password {

            position: absolute;

            right: 10px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: none;

            color: #1e3c72;

            font-size: 13px;

            font-weight: bold;

            cursor: pointer;

        }


        .show-password:hover {

            color: #16305c;

        }


        /* LOGIN BUTTON */

        .login-button {

            width: 100%;

            padding: 13px;

            margin-top: 10px;

            border: none;

            border-radius: 7px;

            background: #1e3c72;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

        }


        .login-button:hover {

            background: #16305c;

        }


        /* ERROR */

        .error {

            background: #f8d7da;

            color: #842029;

            padding: 10px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;

        }


        /* FOOTER */

        .footer {

            margin-top: 25px;

            font-size: 13px;

            color: #888;

        }


    </style>

</head>


<body>


<div class="login-container">


    <!-- CHURCH ICON -->

    <div class="church-icon">

        ⛪

    </div>


    <!-- TITLE -->

    <h1>

        Church Management System

    </h1>


    <div class="subtitle">

        Welcome! Please login to continue.

    </div>


    <!-- ERROR MESSAGE -->

    <?php if ($error != ""): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- LOGIN FORM -->

    <form method="POST" action="">


        <!-- USERNAME -->

        <div class="input-group">

            <label for="username">

                Username

            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="input-group">

            <label for="password">

                Password

            </label>


            <div class="password-container">

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword()"
                    id="showButton"
                >
                    Show
                </button>

            </div>

        </div>


        <!-- LOGIN BUTTON -->

        <button
            type="submit"
            class="login-button"
        >

            Login

        </button>


    </form>


    <!-- FOOTER -->

    <div class="footer">

        Church Management System

    </div>


</div>


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.getElementById("showButton");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "Hide";

    } else {

        password.type = "password";

        button.textContent = "Show";

    }

}

</script>


</body>

</html>