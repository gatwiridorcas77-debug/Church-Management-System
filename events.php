<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$message = "";

/* ADD EVENT */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $event_name = $_POST["event_name"];
    $event_date = $_POST["event_date"];
    $event_time = $_POST["event_time"];
    $location = $_POST["location"];
    $description = $_POST["description"];

    $sql = "INSERT INTO events
            (event_name, event_date, event_time, location, description)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssss",
            $event_name,
            $event_date,
            $event_time,
            $location,
            $description
        );

        if ($stmt->execute()) {
            $message = "Event added successfully!";
        } else {
            $message = "Error adding event: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* DELETE EVENT */
if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: events.php");
        exit();
    }
}


/* GET EVENTS */
$result = $conn->query(
    "SELECT * FROM events ORDER BY event_date ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Church Events</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<?php include "layout.php"; ?>


<div class="container">

    <h1 class="page-title">Church Events</h1>


    <?php if ($message != ""): ?>

        <div class="form-container">
            <strong><?php echo $message; ?></strong>
        </div>

    <?php endif; ?>


    <!-- ADD EVENT FORM -->

    <div class="form-container">

        <h2>Add New Event</h2>

        <form method="POST" action="events.php">

            <label>Event Name</label>

            <input
                type="text"
                name="event_name"
                placeholder="Enter event name"
                required
            >


            <label>Event Date</label>

            <input
                type="date"
                name="event_date"
                required
            >


            <label>Event Time</label>

            <input
                type="time"
                name="event_time"
            >


            <label>Location</label>

            <input
                type="text"
                name="location"
                placeholder="Enter location"
            >


            <label>Description</label>

            <textarea
                name="description"
                rows="4"
                placeholder="Enter event description"
            ></textarea>


            <button type="submit">
                ➕ Add Event
            </button>

        </form>

    </div>


    <!-- EVENTS LIST -->

    <div class="form-container">

        <h2>Upcoming Events</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <tr>

                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Action</th>

                </tr>


                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $row["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["event_name"]); ?>
                        </td>

                        <td>
                            <?php echo $row["event_date"]; ?>
                        </td>

                        <td>
                            <?php echo $row["event_time"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["location"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["description"]); ?>
                        </td>

                        <td>

                            <a
                                href="events.php?delete=<?php echo $row["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this event?');"
                            >
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        <?php else: ?>

            <p>No events have been added yet.</p>

        <?php endif; ?>

    </div>

</div>

</body>

</html>