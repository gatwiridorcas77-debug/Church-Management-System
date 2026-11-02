<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";


/* =========================
   GET DASHBOARD INFORMATION
   ========================= */

$total_members = 0;
$total_ministries = 0;
$total_events = 0;
$total_donations = 0;


/* Total Members */

$result = $conn->query("SELECT COUNT(*) AS total FROM members");

if ($result) {
    $row = $result->fetch_assoc();
    $total_members = $row["total"];
}


/* Total Ministries */

$result = $conn->query("SELECT COUNT(*) AS total FROM ministries");

if ($result) {
    $row = $result->fetch_assoc();
    $total_ministries = $row["total"];
}


/* Check whether events table exists */

$check_events = $conn->query("SHOW TABLES LIKE 'events'");

if ($check_events && $check_events->num_rows > 0) {

    $result = $conn->query("SELECT COUNT(*) AS total FROM events");

    if ($result) {
        $row = $result->fetch_assoc();
        $total_events = $row["total"];
    }
}


/* Check whether donations table exists */

$check_donations = $conn->query("SHOW TABLES LIKE 'donations'");

if ($check_donations && $check_donations->num_rows > 0) {

    $result = $conn->query(
        "SELECT COALESCE(SUM(amount), 0) AS total FROM donations"
    );

    if ($result) {
        $row = $result->fetch_assoc();
        $total_donations = $row["total"];
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Church CMS</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<!-- =========================
     HEADER
     ========================= -->

<header class="site-header">

    <div class="church-brand">

        <div class="church-icon">
            ⛪
        </div>

        <div>

            <h1>
                Church Management System
            </h1>

            <p>
                East Africa Pentecostal Church
            </p>

        </div>

    </div>


    <div class="header-user">

        <span>
            👤
            Welcome, Admin
        </span>

        <a href="logout.php">
            Logout
        </a>

    </div>

</header>


<!-- =========================
     NAVIGATION
     ========================= -->

<nav class="navbar">

    <a
        href="dashboard.php"
        class="active"
    >
        🏠 Dashboard
    </a>

    <a href="add.php">
        👤➕ Register Member
    </a>

    <a href="view.php">
        👥 View Members
    </a>

    <a href="#">
        ⛪ Ministries
    </a>

    <a href="#">
        📅 Events
    </a>

    <a href="#">
        📊 Reports
    </a>

    <a href="#">
        ❤️ Donations
    </a>

    <a href="#">
        💼 Expenses
    </a>

    <a href="logout.php">
        🚪 Logout
    </a>

</nav>


<!-- =========================
     MAIN DASHBOARD
     ========================= -->

<main class="dashboard-container">


    <!-- WELCOME -->

    <section class="welcome-section">

        <h2>
            Welcome to the Church Management System
        </h2>

        <p>
            East Africa Pentecostal Church
        </p>

        <div class="cross-line">
            ───── ✝ ─────
        </div>

    </section>


    <!-- =========================
         DASHBOARD CARDS
         ========================= -->

    <section class="dashboard-cards">


        <!-- MEMBERS -->

        <div class="dashboard-card members-card">

            <div class="card-icon">
                👥
            </div>

            <h3>
                Total Members
            </h3>

            <strong>
                <?php echo $total_members; ?>
            </strong>

            <a href="view.php">
                View Members →
            </a>

        </div>


        <!-- MINISTRIES -->

        <div class="dashboard-card ministries-card">

            <div class="card-icon">
                ⛪
            </div>

            <h3>
                Ministries
            </h3>

            <strong>
                <?php echo $total_ministries; ?>
            </strong>

            <a href="#">
                View Ministries →
            </a>

        </div>


        <!-- EVENTS -->

        <div class="dashboard-card events-card">

            <div class="card-icon">
                📅
            </div>

            <h3>
                Upcoming Events
            </h3>

            <strong>
                <?php echo $total_events; ?>
            </strong>

            <a href="#">
                View Events →
            </a>

        </div>


        <!-- DONATIONS -->

        <div class="dashboard-card donations-card">

            <div class="card-icon">
                ❤️
            </div>

            <h3>
                Total Donations
            </h3>

            <strong>
                KSh <?php echo number_format($total_donations, 2); ?>
            </strong>

            <a href="#">
                View Donations →
            </a>

        </div>

    </section>


    <!-- =========================
         LOWER SECTION
         ========================= -->

    <section class="dashboard-lower">


        <!-- RECENT ACTIVITIES -->

        <div class="activity-box">

            <h2>
                🕐 Recent Activities
            </h2>

            <div class="activity">

                <span class="activity-icon">
                    👤➕
                </span>

                <div>

                    <strong>
                        Member Registration
                    </strong>

                    <p>
                        New members can be registered
                        from the Members section.
                    </p>

                </div>

            </div>


            <div class="activity">

                <span class="activity-icon">
                    ⛪
                </span>

                <div>

                    <strong>
                        Church Ministries
                    </strong>

                    <p>
                        Manage church ministries
                        and departments.
                    </p>

                </div>

            </div>


            <div class="activity">

                <span class="activity-icon">
                    📅
                </span>

                <div>

                    <strong>
                        Church Events
                    </strong>

                    <p>
                        Keep track of upcoming
                        church activities.
                    </p>

                </div>

            </div>

        </div>


        <!-- MOTTO -->

        <div class="motto-box">

            <div class="motto-icon">
                📖
            </div>

            <h2>
                Our Motto
            </h2>

            <p class="verse">

                "For where two or three gather
                in my name, there am I with them."

            </p>

            <p class="reference">
                Matthew 18:20
            </p>

            <hr>

            <p class="motto-text">

                Together in Christ,<br>
                we build a stronger church.

            </p>

            <div class="big-cross">
                ✝
            </div>

        </div>

    </section>


</main>


<!-- =========================
     FOOTER
     ========================= -->

<footer class="footer">

    <p>
        © <?php echo date("Y"); ?>
        East Africa Pentecostal Church
    </p>

    <p>
        Church Management System
    </p>

</footer>


</body>

</html>