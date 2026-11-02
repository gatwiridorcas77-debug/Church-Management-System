<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";


/* MEMBERS */

$members_result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM members"
);

$total_members =
    $members_result->fetch_assoc()["total"];


/* MINISTRIES */

$ministries_result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM ministries"
);

$total_ministries =
    $ministries_result->fetch_assoc()["total"];


/* EVENTS */

$events_result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM events"
);

$total_events =
    $events_result->fetch_assoc()["total"];


/* DONATIONS */

$donations_result = $conn->query(
    "SELECT SUM(amount) AS total
     FROM donations"
);

$total_donations =
    $donations_result->fetch_assoc()["total"] ?? 0;


/* EXPENSES */

$expenses_result = $conn->query(
    "SELECT SUM(amount) AS total
     FROM expenses"
);

$total_expenses =
    $expenses_result->fetch_assoc()["total"] ?? 0;


/* BALANCE */

$balance = $total_donations - $total_expenses;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Reports</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1 class="page-title">
        Church Reports
    </h1>

    <p style="text-align:center;">
        Church Management Summary
    </p>


    <div class="dashboard-cards">


        <div class="dashboard-card">

            <h3>Members</h3>

            <div class="card-number">
                <?php echo $total_members; ?>
            </div>

        </div>


        <div class="dashboard-card">

            <h3>Ministries</h3>

            <div class="card-number">
                <?php echo $total_ministries; ?>
            </div>

        </div>


        <div class="dashboard-card">

            <h3>Events</h3>

            <div class="card-number">
                <?php echo $total_events; ?>
            </div>

        </div>


        <div class="dashboard-card">

            <h3>Total Donations</h3>

            <div class="card-number">

                KSh
                <?php
                echo number_format(
                    $total_donations,
                    2
                );
                ?>

            </div>

        </div>


        <div class="dashboard-card">

            <h3>Total Expenses</h3>

            <div class="card-number">

                KSh
                <?php
                echo number_format(
                    $total_expenses,
                    2
                );
                ?>

            </div>

        </div>


        <div class="dashboard-card">

            <h3>Balance</h3>

            <div class="card-number">

                KSh
                <?php
                echo number_format(
                    $balance,
                    2
                );
                ?>

            </div>

        </div>

    </div>


    <div class="form-container">

        <h2>Financial Summary</h2>

        <table class="members-table">

            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>

            <tr>
                <td>Total Donations</td>

                <td>
                    KSh
                    <?php
                    echo number_format(
                        $total_donations,
                        2
                    );
                    ?>
                </td>
            </tr>

            <tr>
                <td>Total Expenses</td>

                <td>
                    KSh
                    <?php
                    echo number_format(
                        $total_expenses,
                        2
                    );
                    ?>
                </td>
            </tr>

            <tr>
                <td><strong>Balance</strong></td>

                <td>
                    <strong>
                        KSh
                        <?php
                        echo number_format(
                            $balance,
                            2
                        );
                        ?>
                    </strong>
                </td>
            </tr>

        </table>

    </div>

</div>

</body>

</html>