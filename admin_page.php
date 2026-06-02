<?php

    session_start();
     require_once "config.php"; 

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

//Delete Task
if (isset($_GET['delete'])) {

    $task_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM tasks WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
}

// Add Task
if (isset($_POST['add_task'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare(
        "INSERT INTO tasks (user_id, title, description)
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param("iss", $user_id, $title, $description);
    $stmt->execute();

}

//Get tasks
$stmt = $conn->prepare(
    "SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="style.css"
</head>
<body style="background: #fff;">

    <div class="box">
        <h1>Welcome, <span><?= $_SESSION['name']; ?></span></h1>
        <p>Hi <span>admin</span> user</p>

        <h2>Add Task</h2>

        <form method="POST">
            <input type="text" name="title" placeholder="Task title" required>
            <input type="text" name="description" placeholder="Description" required>
            <button type="submit" name="add_task">Add</button>
        </form>
        <br>
        <br>
        <h2>Your Tasks</h2>

            <?php while($task = $tasks->fetch_assoc()): ?>
                <div class="task">
                    <h3><?= htmlspecialchars($task['title']); ?></h3>
                    <p><?= htmlspecialchars($task['description']); ?></p>

                    <a href="?delete=<?= $task['id']; ?>"onclick="return confirm('Are you sure you want to delete this task?')"class="delete-btn">Delete</a>
                </div>
            <?php endwhile; ?>

            <button onclick="window.location.href='logout.php'">Logout</button>

        
    </div>

</body>
</html>