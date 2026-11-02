<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: view.php");
    exit();
}

$message = "";
$error = "";


/* Get member */

$stmt = $conn->prepare(
    "SELECT * FROM members WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {

    header("Location: view.php");
    exit();

}

$member = $result->fetch_assoc();

$stmt->close();


/* Get ministries */

$ministries = $conn->query(
    "SELECT id, ministry_name
     FROM ministries
     ORDER BY ministry_name"
);


/* Update member */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $gender = $_POST["gender"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");

    $ministry_id = !empty($_POST["ministry_id"])
        ? intval($_POST["ministry_id"])
        : null;

    $date_joined = !empty($_POST["date_joined"])
        ? $_POST["date_joined"]
        : null;

    $date_of_birth = !empty($_POST["date_of_birth"])
        ? $_POST["date_of_birth"]
        : null;

    $marital_status = $_POST["marital_status"] ?? "";

    $membership_status =
        $_POST["membership_status"] ?? "Active";


    if ($full_name == "") {

        $error = "Full name is required.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE members SET
                full_name = ?,
                gender = ?,
                phone = ?,
                email = ?,
                address = ?,
                ministry_id = ?,
                date_joined = ?,
                date_of_birth = ?,
                marital_status = ?,
                membership_status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssssissssi",
            $full_name,
            $gender,
            $phone,
            $email,
            $address,
            $ministry_id,
            $date_joined,
            $date_of_birth,
            $marital_status,
            $membership_status,
            $id
        );

        if ($stmt->execute()) {

            header("Location: view.php");
            exit();

        } else {

            $error = $stmt->error;

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Member</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include "layout.php"; ?>

<main class="container">

    <h1 class="page-title">
        Edit Member
    </h1>

    <?php if ($error != ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <div class="form-container">

        <form method="POST">

            <label>Full Name</label>

            <input
                type="text"
                name="full_name"
                value="<?php
                    echo htmlspecialchars(
                        $member["full_name"]
                    );
                ?>"
                required
            >

            <label>Gender</label>

            <select name="gender">

                <option value="">
                    Select Gender
                </option>

                <option
                    value="Male"
                    <?php
                    echo $member["gender"] == "Male"
                        ? "selected"
                        : "";
                    ?>
                >
                    Male
                </option>

                <option
                    value="Female"
                    <?php
                    echo $member["gender"] == "Female"
                        ? "selected"
                        : "";
                    ?>
                >
                    Female
                </option>

            </select>

            <label>Phone</label>

            <input
                type="text"
                name="phone"
                value="<?php
                    echo htmlspecialchars(
                        $member["phone"]
                    );
                ?>"
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php
                    echo htmlspecialchars(
                        $member["email"]
                    );
                ?>"
            >

            <label>Address</label>

            <input
                type="text"
                name="address"
                value="<?php
                    echo htmlspecialchars(
                        $member["address"]
                    );
                ?>"
            >

            <label>Ministry</label>

            <select name="ministry_id">

                <option value="">
                    Select Ministry
                </option>

                <?php while (
                    $ministry = $ministries->fetch_assoc()
                ): ?>

                    <option
                        value="<?php
                            echo $ministry["id"];
                        ?>"
                        <?php
                        echo $member["ministry_id"]
                            == $ministry["id"]
                            ? "selected"
                            : "";
                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $ministry["ministry_name"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

            <label>Membership Status</label>

            <select name="membership_status">

                <option
                    value="Active"
                    <?php
                    echo $member["membership_status"]
                        == "Active"
                        ? "selected"
                        : "";
                    ?>
                >
                    Active
                </option>

                <option
                    value="Inactive"
                    <?php
                    echo $member["membership_status"]
                        == "Inactive"
                        ? "selected"
                        : "";
                    ?>
                >
                    Inactive
                </option>

            </select>

            <button type="submit">
                Update Member
            </button>

            <a href="view.php" class="secondary-btn">
                Cancel
            </a>

        </form>

    </div>

</main>

</body>
</html>