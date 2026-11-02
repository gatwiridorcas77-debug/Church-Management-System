<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";


/* ADD TITHE */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $member_name = $_POST["member_name"];
    $amount = $_POST["amount"];
    $tithe_date = $_POST["tithe_date"];
    $payment_method = $_POST["payment_method"];
    $description = $_POST["description"];

    $sql = "INSERT INTO tithes
            (member_name, amount, tithe_date, payment_method, description)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sdsss",
            $member_name,
            $amount,
            $tithe_date,
            $payment_method,
            $description
        );

        if ($stmt->execute()) {
            $message = "Tithe recorded successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* DELETE TITHE */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare(
        "DELETE FROM tithes WHERE id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

    }

    header("Location: tithes.php");
    exit();
}


/* GET TITHES */

$result = $conn->query(
    "SELECT * FROM tithes ORDER BY tithe_date DESC"
);


/* TOTAL TITHES */

$total_result = $conn->query(
    "SELECT SUM(amount) AS total FROM tithes"
);

$total_row = $total_result->fetch_assoc();

$total_tithes = $total_row["total"] ?? 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Tithes</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<?php include "layout.php"; ?>

<div class="container">

    <h1 class="page-title">
        Church Tithes
    </h1>


    <?php if ($message != ""): ?>

        <div class="form-container">

            <strong>
                <?php echo htmlspecialchars($message); ?>
            </strong>

        </div>

    <?php endif; ?>


    <!-- TOTAL -->

    <div class="form-container">

        <h2>Total Tithes</h2>

        <h1>
            KSh <?php echo number_format($total_tithes, 2); ?>
        </h1>

    </div>


    <!-- FORM -->

    <div class="form-container">

        <h2>Record Tithe</h2>

        <form method="POST" action="tithes.php">

            <label>Member Name</label>

            <input
                type="text"
                name="member_name"
                placeholder="Enter member name"
                required
            >


            <label>Amount (KSh)</label>

            <input
                type="number"
                name="amount"
                step="0.01"
                min="0"
                placeholder="Enter amount"
                required
            >


            <label>Tithe Date</label>

            <input
                type="date"
                name="tithe_date"
                required
            >


            <label>Payment Method</label>

            <select name="payment_method">

                <option value="">Select method</option>

                <option value="Cash">Cash</option>

                <option value="M-Pesa">M-Pesa</option>

                <option value="Bank">Bank</option>

            </select>


            <label>Description</label>

            <textarea
                name="description"
                rows="4"
                placeholder="Enter description"
            ></textarea>


            <button type="submit">
                💵 Record Tithe
            </button>

        </form>

    </div>


    <!-- RECORDS -->

    <div class="form-container">

        <h2>Tithe Records</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <tr>

                    <th>ID</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Description</th>
                    <th>Action</th>

                </tr>


                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $row["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["member_name"]); ?>
                        </td>

                        <td>
                            KSh
                            <?php echo number_format($row["amount"], 2); ?>
                        </td>

                        <td>
                            <?php echo $row["tithe_date"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["payment_method"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["description"]); ?>
                        </td>

                        <td>

                            <a
                                href="tithes.php?delete=<?php echo $row["id"]; ?>"
                                onclick="return confirm('Delete this tithe?');"
                            >
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        <?php else: ?>

            <p>No tithes have been recorded yet.</p>

        <?php endif; ?>

    </div>

</div>

</body>

</html>