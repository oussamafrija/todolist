<?php

include "connect.php";

session_start();
if (!isset($_SESSION["username"])) {
    header("location: index.php");
}

$username = $_SESSION["username"];

// Get the user_id from the username
$sql = "SELECT user_id FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row > 0) {
        $user_id = $row["user_id"];
    }
}

// Add the task to the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_task'])) {
        $taskDescription = $_POST["task_description"];
        $sql = "INSERT INTO `tasks` (user_id, task_description) VALUES ($user_id, '$taskDescription')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("location: home.php");
        } else {
            echo "Task not added successfully";
        }
    }

    // Update the task description in the database
    if (isset($_POST['update_task'])) {
        $updateTask = $_POST['update_task'];
        $newTaskDescription = $_POST['new_task_description'];

        $sql = "UPDATE `tasks` SET task_description = '$newTaskDescription' WHERE task_id = $updateTask";

        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("location: home.php");
        } else {
            echo "Error updating task: " . mysqli_error($conn);
        }
    }

    // Delete the task from the table
    if (isset($_POST['delete_task'])) {
        $deleteTask = $_POST['delete_task'];
        $sql = "DELETE FROM `tasks` WHERE task_id = $deleteTask";

        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("location: home.php");
        } else {
            die(mysqli_error($conn));
        }
    }

    // Delete Account of the user
    if (isset($_POST['delete_account'])) {
        $deleteAccount = $_POST['delete_account'];
        $sql = "DELETE FROM `users` WHERE user_id = $user_id";

        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("location: index.php");
        } else {
            die(mysqli_error($conn));
        }
    }

    // Toggle the status of the task (incomplete to complete and vice versa)
    if (isset($_POST['toggle_status'])) {
        $toggleTask = $_POST['toggle_status'];
        $sql = "UPDATE `tasks` SET task_status = CASE WHEN task_status = 'complete' THEN 'incomplete' ELSE 'complete' END WHERE task_id = $toggleTask";

        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("location: home.php");
            exit();
        } else {
            echo "Error updating task status: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Homepage</title>
</head>

<body>
    <h1 class="text-center text-success mt-5">Welcome <?php echo $_SESSION["username"]; ?></h1>

    <div class="container mt-5">
        <form method="post">
            <div class="d-flex justify-content-center">
                <div class="input-group w-50">
                    <input type="text" name="task_description" class="form-control" placeholder="Enter your Task" />
                    <button type="submit" name="add_task" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Toggle Status</th>
                    <th scope="col">Status</th>
                    <th scope="col">Task</th>
                    <th scope="col">Update</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `tasks` WHERE user_id = '$user_id'";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $taskStatus = $row['task_status'];
                        $taskDescription = $row['task_description'];
                        $taskId = $row['task_id'];

                        echo
                        '<tr>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="toggle_status" value="' . $taskId . '">
                                    <button type="submit" class="btn btn-info">Toggle Status</button>
                                </form>
                            </td>
                            <td>' . $taskStatus . '</td>
                            <td>' . $taskDescription . '</td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="update_task" value="' . $taskId . '">
                                    <input type="text" name="new_task_description" class="form-control" placeholder="Edit task description" required>
                                    <button type="submit" class="btn btn-warning mt-2">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="delete_task" value="' . $taskId . '">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <a href="logout.php" class="btn btn-danger mt-5">Logout</a>
    </div>

    <div class="container">
        <form method="post" id="delete-account-form">
            <button type="button" class="btn btn-danger mt-5" data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal">Delete Account</button>
        </form>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete your account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger" name="delete_account" form="delete-account-form">Yes, Delete Account</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
