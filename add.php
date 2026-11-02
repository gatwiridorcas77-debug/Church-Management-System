<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";
$error = "";

$ministries = $conn->query(
    "SELECT id, ministry_name
     FROM ministries
     ORDER BY ministry_name ASC"
);

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
            "INSERT INTO members
            (
                full_name,
                gender,
                phone,
                email,
                address,
                ministry_id,
                date_joined,
                date_of_birth,
                marital_status,
                membership_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssssissss",
            $full_name,
            $gender,
            $phone,
            $email,
            $address,
            $ministry_id,
            $date_joined,
            $date_of_birth,
            $marital_status,
            $membership_status
        );

        if ($stmt->execute()) {

            $message = "Member registered successfully.";

        } else {

            $error = "Registration failed: "
                   . $stmt->error;
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

    <title>Register Member</title>

    <link rel="stylesheet" href="style.css">
<style>
.form-container {
    width: 700px;
    max-width: 90%;
    margin: 30px auto;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.form-container form {
    display: flex !important;
    flex-direction: column !important;
}

.form-container label {
    display: block !important;
    margin-top: 15px;
    margin-bottom: 6px;
    font-weight: bold;
}

.form-container input,
.form-container select {
    display: block !important;
    width: 100% !important;
    box-sizing: border-box;
    padding: 12px;
    margin-bottom: 10px;
}

.form-container button {
    width: 100% !important;
    padding: 14px;
    margin-top: 20px;
}
</style>
</head>



<body>

<?php include "layout.php"; ?>

<main class="container">

    <div class="form-header">

        <h1 class="page-title">
            Register New Member
        </h1>

        <p>
            Add a new church member
        </p>

    </div>


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

        <form method="POST" action="">

            <label>Full Name *</label>

            <input
                type="text"
                name="full_name"
                placeholder="Enter full name"
                required
            >


            <label>Gender</label>

            <select name="gender">

                <option value="">
                    Select Gender
                </option>

                <option value="Male">
                    Male
                </option>

                <option value="Female">
                    Female
                </option>

            </select>


            <label>Date of Birth</label>

            <input
                type="date"
                name="date_of_birth"
            >


            <label>Phone Number</label>

            <input
                type="text"
                name="phone"
                placeholder="0712345678"
            >


            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="example@gmail.com"
            >


            <label>Address</label>

            <input
                type="text"
                name="address"
                placeholder="Enter address"
            >


            <label>Ministry</label>

            <select name="ministry_id">

                <option value="">
                    Select Ministry
                </option>

                <?php while (
                    $ministry = $ministries->fetch_assoc()
                ): ?>

                    <option value="<?php
                        echo $ministry["id"];
                    ?>">

                        <?php
                        echo htmlspecialchars(
                            $ministry["ministry_name"]
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>


            <label>Marital Status</label>

            <select name="marital_status">

                <option value="">
                    Select Status
                </option>

                <option value="Single">
                    Single
                </option>

                <option value="Married">
                    Married
                </option>

                <option value="Widowed">
                    Widowed
                </option>

                <option value="Divorced">
                    Divorced
                </option>

            </select>


            <label>Membership Status</label>

            <select name="membership_status">

                <option value="Active">
                    Active
                </option>

                <option value="Inactive">
                    Inactive
                </option>

            </select>


            <label>Date Joined</label>

            <input
                type="date"
                name="date_joined"
            >


            <button type="submit">
                Register Member
            </button>

            <a href="view.php" class="secondary-btn">
                View Members
            </a>

        </form>

    </div>

</main>

</body>
</html>