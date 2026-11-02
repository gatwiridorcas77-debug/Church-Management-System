<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<header class="header">

    <div class="logo">
        ⛪ Church Management System
    </div>

    <div class="admin-user">
        👤
        <?php
        echo htmlspecialchars(
            $_SESSION["username"] ?? "Administrator"
        );
        ?>
    </div>

</header>

<nav class="navbar">

    <a href="dashboard.php">🏠 Dashboard</a>

    <a href="view.php">👥 Members</a>

    <a href="add.php">➕ Register Member</a>

    <a href="attendances.php">🙏 Attendance</a>

    <a href="events.php">📅 Events</a>

    <a href="donations.php">💰 Donations</a>

    <a href="tithes.php">💵 Tithes</a>

    <a href="requests.php">📋 Requests</a>

    <a href="expenses.php">💳 Expenses</a>

    <a href="reports.php">📊 Reports</a>

    <a href="logout.php">🚪 Logout</a>

</nav>