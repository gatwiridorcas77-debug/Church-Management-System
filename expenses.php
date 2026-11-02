<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";
$error = "";


/* ADD EXPENSE */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $expense_name = trim($_POST["expense_name"] ?? "");
    $amount = floatval($_POST["amount"] ?? 0);
    $expense_date = $_POST["expense_date"] ?? "";
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($expense_name == "" || $amount <= 0 || $expense_date == "") {

        $error = "Please enter expense name, amount and date.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO expenses
            (expense_name, amount, expense_date, category, description)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sdsss",
            $expense_name,
            $amount,
            $expense_date,
            $category,
            $description
        );

        if ($stmt->execute()) {

            $message = "Expense recorded successfully.";

        } else {

            $error = "Failed to record expense.";

        }

        $stmt->close();
    }
}


/* DELETE EXPENSE */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare(
        "DELETE FROM expenses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->close();
}


/* GET EXPENSES */

$expenses = $conn->query(
    "SELECT * FROM expenses
     ORDER BY expense_date DESC"
);


/* TOTAL EXPENSES */

$total_result = $conn->query(
    "SELECT SUM(amount) AS total
     FROM expenses"
);

$total = $total_result->fetch_assoc()["total"] ?? 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Expenses</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1 class="page-title">
        Church Expenses
    </h1>

    <h2 style="text-align:center;">
        Total Expenses:
        KSh <?php echo number_format($total, 2); ?>
    </h2>


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


    <div class="form-container">

        <h2>Record Expense</h2>

        <form method="POST">

            <label>Expense Name</label>

            <input
                type="text"
                name="expense_name"
                placeholder="e.g. Electricity Bill"
                required
            >

            <label>Amount</label>

            <input
                type="number"
                name="amount"
                step="0.01"
                placeholder="Enter amount"
                required
            >

            <label>Expense Date</label>

            <input
                type="date"
                name="expense_date"
                required
            >

            <label>Category</label>

            <input
                type="text"
                name="category"
                placeholder="e.g. Utilities"
            >

            <label>Description</label>

            <textarea
                name="description"
                rows="4"
            ></textarea>

            <button type="submit">
                Record Expense
            </button>

        </form>

    </div>


    <div class="form-container">

        <h2>Expense Records</h2>

        <table class="members-table">

            <tr>
                <th>ID</th>
                <th>Expense</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Category</th>
                <th>Action</th>
            </tr>

            <?php while ($expense = $expenses->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $expense["id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($expense["expense_name"]); ?>
                    </td>

                    <td>
                        KSh
                        <?php echo number_format($expense["amount"], 2); ?>
                    </td>

                    <td>
                        <?php echo $expense["expense_date"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($expense["category"]); ?>
                    </td>

                    <td>

                        <a
                            href="expenses.php?delete=<?php echo $expense["id"]; ?>"
                            onclick="return confirm('Delete this expense?');"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>

</html>