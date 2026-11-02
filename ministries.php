<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";
$error = "";


/* ADD MINISTRY */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ministry_name = trim($_POST["ministry_name"] ?? "");

    if ($ministry_name == "") {

        $error = "Ministry name is required.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO ministries (ministry_name)
             VALUES (?)"
        );

        $stmt->bind_param(
            "s",
            $ministry_name
        );

        if ($stmt->execute()) {

            $message = "Ministry added successfully.";

        } else {

            $error = "Failed to add ministry: " . $stmt->error;

        }

        $stmt->close();
    }
}


/* DELETE MINISTRY */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare(
        "DELETE FROM ministries WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        $message = "Ministry deleted successfully.";

    }

    $stmt->close();
}


/* GET MINISTRIES */

$ministries = $conn->query(
    "SELECT * FROM ministries
     ORDER BY id ASC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Ministries</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1 class="page-title">
        Church Ministries
    </h1>

    <p style="text-align:center;">
        Manage church ministries
    </p>


    <?php if ($message != ""): ?>

        <div class="success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <?php if ($error != ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <!-- ADD MINISTRY -->

    <div class="form-container">

        <h2>Add Ministry</h2>

        <form method="POST">

            <label>
                Ministry Name
            </label>

            <input
                type="text"
                name="ministry_name"
                placeholder="e.g. Youth Ministry"
                required
            >

            <button type="submit">
                Add Ministry
            </button>

        </form>

    </div>


    <!-- MINISTRIES -->

    <div class="form-container">

        <h2>Available Ministries</h2>

        <table class="members-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Ministry Name</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

            <?php while ($ministry = $ministries->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $ministry["id"]; ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $ministry["ministry_name"]
                        );
                        ?>
                    </td>

                    <td>

                        <a
                            href="ministries.php?delete=<?php echo $ministry["id"]; ?>"
                            onclick="return confirm('Delete this ministry?');"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>