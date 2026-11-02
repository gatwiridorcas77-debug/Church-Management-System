<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";


/* =========================
   ADD PRAYER REQUEST
   ========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_request"])) {

    $member_id = !empty($_POST["member_id"])
        ? intval($_POST["member_id"])
        : NULL;

    $member_name = $_POST["member_name"];
    $request = $_POST["request"];
    $request_date = $_POST["request_date"];
    $status = "Pending";
    $notes = $_POST["notes"];

    $sql = "INSERT INTO prayer_requests
            (member_id, member_name, request, request_date, status, notes)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "isssss",
            $member_id,
            $member_name,
            $request,
            $request_date,
            $status,
            $notes
        );

        if ($stmt->execute()) {

            $message = "Prayer request added successfully!";

        } else {

            $message = "Error: " . $stmt->error;

        }

        $stmt->close();

    } else {

        $message = "Database error: " . $conn->error;

    }
}


/* =========================
   UPDATE STATUS
   ========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {

    $id = intval($_POST["id"]);
    $status = $_POST["status"];

    $stmt = $conn->prepare(
        "UPDATE prayer_requests SET status = ? WHERE id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            $message = "Request status updated successfully!";
        }

        $stmt->close();
    }
}


/* =========================
   DELETE REQUEST
   ========================= */

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare(
        "DELETE FROM prayer_requests WHERE id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: requests.php");
    exit();
}


/* =========================
   GET PRAYER REQUESTS
   ========================= */

$result = $conn->query(
    "SELECT * FROM prayer_requests
     ORDER BY request_date DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Prayer Requests</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<?php include "layout.php"; ?>


<div class="container">

    <h1 class="page-title">
        Prayer Requests
    </h1>


    <!-- MESSAGE -->

    <?php if ($message != ""): ?>

        <div class="form-container">

            <strong>
                <?php echo htmlspecialchars($message); ?>
            </strong>

        </div>

    <?php endif; ?>


    <!-- ADD REQUEST FORM -->

    <div class="form-container">

        <h2>Add Prayer Request</h2>


        <form method="POST" action="requests.php">


            <label>Member ID</label>

            <input
                type="number"
                name="member_id"
                placeholder="Enter member ID (optional)"
            >


            <label>Member Name</label>

            <input
                type="text"
                name="member_name"
                placeholder="Enter member name"
                required
            >


            <label>Prayer Request</label>

            <textarea
                name="request"
                rows="5"
                placeholder="Enter prayer request"
                required
            ></textarea>


            <label>Request Date</label>

            <input
                type="date"
                name="request_date"
                required
            >


            <label>Notes</label>

            <textarea
                name="notes"
                rows="4"
                placeholder="Enter additional notes"
            ></textarea>


            <button
                type="submit"
                name="add_request"
            >
                🙏 Add Prayer Request
            </button>


        </form>

    </div>


    <!-- REQUEST RECORDS -->

    <div class="form-container">

        <h2>Prayer Request Records</h2>


        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <tr>

                    <th>ID</th>

                    <th>Member ID</th>

                    <th>Member Name</th>

                    <th>Prayer Request</th>

                    <th>Date</th>

                    <th>Status</th>

                    <th>Notes</th>

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
                                $row["member_id"] ?? ""
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["member_name"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["request"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo $row["request_date"];
                            ?>
                        </td>


                        <td>

                            <form
                                method="POST"
                                action="requests.php"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $row["id"]; ?>"
                                >


                                <select name="status">

                                    <option
                                        value="Pending"
                                        <?php
                                        if ($row["status"] == "Pending")
                                            echo "selected";
                                        ?>
                                    >
                                        Pending
                                    </option>


                                    <option
                                        value="Approved"
                                        <?php
                                        if ($row["status"] == "Approved")
                                            echo "selected";
                                        ?>
                                    >
                                        Approved
                                    </option>


                                    <option
                                        value="Completed"
                                        <?php
                                        if ($row["status"] == "Completed")
                                            echo "selected";
                                        ?>
                                    >
                                        Completed
                                    </option>


                                    <option
                                        value="Rejected"
                                        <?php
                                        if ($row["status"] == "Rejected")
                                            echo "selected";
                                        ?>
                                    >
                                        Rejected
                                    </option>

                                </select>


                                <button
                                    type="submit"
                                    name="update_status"
                                >
                                    Update
                                </button>

                            </form>

                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row["notes"] ?? ""
                            );
                            ?>
                        </td>


                        <td>

                            <a
                                href="requests.php?delete=<?php echo $row["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this prayer request?');"
                            >
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>


        <?php else: ?>

            <p>
                No prayer requests have been recorded yet.
            </p>

        <?php endif; ?>

    </div>

</div>


</body>

</html>