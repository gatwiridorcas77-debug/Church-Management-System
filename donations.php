<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";


/* RECORD DONATION */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $donor_name = $_POST["donor_name"];
    $amount = $_POST["amount"];
    $donation_date = $_POST["donation_date"];
    $purpose = $_POST["purpose"];
    $description = $_POST["description"];

    $sql = "INSERT INTO donations
            (donor_name, amount, donation_date, purpose, description)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sdsss",
            $donor_name,
            $amount,
            $donation_date,
            $purpose,
            $description
        );

        if ($stmt->execute()) {

            $message = "Donation recorded successfully!";

        } else {

            $message = "Error: " . $stmt->error;

        }

        $stmt->close();

    } else {

        $message = "Database error: " . $conn->error;

    }
}


/* DELETE DONATION */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare(
        "DELETE FROM donations WHERE id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $stmt->close();
    }

    header("Location: donations.php");

    exit();
}


/* GET DONATIONS */

$result = $conn->query(
    "SELECT * FROM donations ORDER BY donation_date DESC"
);


/* CALCULATE TOTAL */

$total_result = $conn->query(
    "SELECT SUM(amount) AS total FROM donations"
);

$total_row = $total_result->fetch_assoc();

$total_donations = $total_row["total"] ?? 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Donations</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>

<?php include "layout.php"; ?>


<div class="container">

    <h1 class="page-title">
        Church Donations
    </h1>


    <!-- SUCCESS MESSAGE -->

    <?php if ($message != ""): ?>

        <div class="form-container">

            <strong>
                <?php echo htmlspecialchars($message); ?>
            </strong>

        </div>

    <?php endif; ?>


    <!-- TOTAL DONATIONS -->

    <div class="form-container">

        <h2>Total Donations</h2>

        <h1>
            KSh <?php echo number_format($total_donations, 2); ?>
        </h1>

    </div>


    <!-- DONATION FORM -->

    <div class="form-container">

        <h2>Record Donation</h2>


        <form method="POST" action="donations.php">


            <label>Donor Name</label>

            <input
                type="text"
                name="donor_name"
                placeholder="Enter donor name"
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


            <label>Donation Date</label>

            <input
                type="date"
                name="donation_date"
                required
            >


            <label>Purpose</label>

            <input
                type="text"
                name="purpose"
                placeholder="e.g. Tithe, Building, Offering"
            >


            <label>Description</label>

            <textarea
                name="description"
                rows="4"
                placeholder="Enter additional information"
            ></textarea>


            <button type="submit">
                💰 Record Donation
            </button>


        </form>

    </div>


    <!-- DONATION RECORDS -->

    <div class="form-container">

        <h2>Donation Records</h2>


        <?php if ($result && $result->num_rows > 0): ?>


            <table>

                <tr>

                    <th>ID</th>

                    <th>Donor</th>

                    <th>Amount</th>

                    <th>Date</th>

                    <th>Purpose</th>

                    <th>Description</th>

                    <th>Action</th>

                </tr>


                <?php while ($row = $result->fetch_assoc()): ?>


                    <tr>

                        <td>
                            <?php echo $row["id"]; ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["donor_name"]
                            );
                            ?>
                        </td>


                        <td>
                            KSh
                            <?php
                            echo number_format(
                                $row["amount"],
                                2
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo $row["donation_date"];
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["purpose"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["description"]
                            );
                            ?>
                        </td>


                        <td>

                            <a
                                href="donations.php?delete=<?php echo $row["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this donation?');"
                            >
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>


                <?php endwhile; ?>


            </table>


        <?php else: ?>

            <p>
                No donations have been recorded yet.
            </p>

        <?php endif; ?>


    </div>


</div>


</body>

</html>