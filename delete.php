<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$id = intval($_GET["id"] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare(
        "DELETE FROM members WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();
}

header("Location: view.php");
exit();

?>