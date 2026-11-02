<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

/* Get search value */
$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

/* Search by name OR phone */
if ($search != "") {

    $sql = "
        SELECT 
            members.*,
            ministries.ministry_name
        FROM members
        LEFT JOIN ministries
            ON members.ministry_id = ministries.id
        WHERE members.full_name LIKE ?
           OR members.phone LIKE ?
        ORDER BY members.id ASC
    ";

    $stmt = $conn->prepare($sql);

    $search_value = "%" . $search . "%";

    $stmt->bind_param("ss", $search_value, $search_value);

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    /* Show all members when search box is empty */

    $sql = "
        SELECT 
            members.*,
            ministries.ministry_name
        FROM members
        LEFT JOIN ministries
            ON members.ministry_id = ministries.id
        ORDER BY members.id ASC
    ";

    $result = $conn->query($sql);
}

if (!$result) {
    die("Error loading members: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Members - Church Management System</title>

    <link rel="stylesheet" href="style.css">

    <style>

        body {
            background: #f4f6f9;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .members-container {
            width: 95%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            overflow-x: auto;
        }

        .members-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .top-buttons {
            margin-bottom: 20px;
        }

        .add-button {
            display: inline-block;
            padding: 10px 18px;
            background: #198754;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .add-button:hover {
            background: #146c43;
        }


        /* SEARCH AREA */

        .search-container {
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-container input {
            width: 350px;
            max-width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .search-button {
            padding: 12px 20px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-button:hover {
            background: #0b5ed7;
        }

        .clear-button {
            padding: 12px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .clear-button:hover {
            background: #5c636a;
        }


        /* TABLE */

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        table th {
            background: #343a40;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table tr:hover {
            background: #f5f5f5;
        }


        /* EDIT BUTTON */

        .edit-button {
            background: #198754;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .edit-button:hover {
            background: #146c43;
        }


        /* DELETE BUTTON */

        .delete-button {
            background: #dc3545;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .delete-button:hover {
            background: #bb2d3b;
        }


        .no-members {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #777;
        }

        .search-result {
            margin-bottom: 15px;
            color: #555;
        }

    </style>

</head>


<body>

<div class="members-container">

    <h2>Registered Church Members</h2>


    <!-- REGISTER MEMBER BUTTON -->

    <div class="top-buttons">

        <a href="add.php" class="add-button">
            ➕ Register New Member
        </a>

    </div>


    <!-- SEARCH FORM -->

    <div class="search-container">

        <form method="GET" action="view.php">

            <input
                type="text"
                name="search"
                placeholder="Search name or phone number..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit" class="search-button">
                🔍 Search
            </button>

            <a href="view.php" class="clear-button">
                Clear
            </a>

        </form>

    </div>


    <!-- SEARCH MESSAGE -->

    <?php if ($search != ""): ?>

        <div class="search-result">

            Searching for:
            <strong><?php echo htmlspecialchars($search); ?></strong>

            — <?php echo $result->num_rows; ?> member(s) found.

        </div>

    <?php endif; ?>


    <?php if ($result->num_rows > 0): ?>

        <table>

            <thead>

                <tr>

                    <th>Member No.</th>

                    <th>Full Name</th>

                    <th>Gender</th>

                    <th>Phone</th>

                    <th>Email</th>

                    <th>Address</th>

                    <th>Ministry</th>

                    <th>Date Joined</th>

                    <th>Date of Birth</th>

                    <th>Marital Status</th>

                    <th>Membership Status</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            <?php

            /* Display numbering */

            $member_no = 1;

            while ($row = $result->fetch_assoc()):

            ?>

                <tr>

                    <td>
                        <?php echo $member_no; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['full_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['gender']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['phone']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['email']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['address']); ?>
                    </td>

                    <td>

                        <?php

                        if (!empty($row['ministry_name'])) {

                            echo htmlspecialchars($row['ministry_name']);

                        } else {

                            echo "Not Assigned";

                        }

                        ?>

                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['date_joined']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['date_of_birth']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['marital_status']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['membership_status']); ?>
                    </td>

                    <td>

                        <a
                            href="edit.php?id=<?php echo $row['id']; ?>"
                            class="edit-button">
                            Edit
                        </a>

                        <a
                            href="delete.php?id=<?php echo $row['id']; ?>"
                            class="delete-button"
                            onclick="return confirm('Are you sure you want to delete this member?');">
                            Delete
                        </a>

                    </td>

                </tr>

            <?php

                $member_no++;

            endwhile;

            ?>

            </tbody>

        </table>


    <?php else: ?>

        <div class="no-members">

            <?php if ($search != ""): ?>

                No member found matching
                "<strong><?php echo htmlspecialchars($search); ?></strong>".

            <?php else: ?>

                No members have been registered yet.

            <?php endif; ?>

        </div>

    <?php endif; ?>


</div>


</body>

</html>


<?php

$conn->close();

?>