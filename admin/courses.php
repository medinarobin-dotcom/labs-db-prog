<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$courseModel = new Course($db);
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];

    if ($id > 0) {
        try {
            $courseModel->delete($id);
            $message = 'Course deleted successfully.';
        } catch (PDOException $e) {
            $message = 'Unable to delete course. It may be assigned to existing classes.';
            $messageType = 'error';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($code === '' || $name === '') {
        $message = 'Course code and course name are required.';
        $messageType = 'error';
    } else {
        try {
            if ($id > 0) {
                $courseModel->update($id, $code, $name, $description);
                $message = 'Course updated successfully.';
            } else {
                $courseModel->create($code, $name, $description);
                $message = 'Course created successfully.';
            }
        } catch (PDOException $e) {
            $message = 'Unable to save course. Check for duplicate course codes.';
            $messageType = 'error';
        }
    }
}

$editCourse = null;

if (isset($_GET['edit'])) {
    $editCourse = $courseModel->find((int) $_GET['edit']);
}

$courses = $courseModel->all();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h1>Manage Courses</h1>

    <?php if ($message !== ''): ?>
        <p class="<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2><?= $editCourse ? 'Edit Course' : 'Add New Course' ?></h2>

    <form method="POST">
        <input
            type="hidden"
            name="id"
            value="<?= htmlspecialchars($editCourse['course_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="course_code">Course Code</label>
        <input
            type="text"
            id="course_code"
            name="course_code"
            value="<?= htmlspecialchars($editCourse['course_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="course_name">Course Name</label>
        <input
            type="text"
            id="course_name"
            name="course_name"
            value="<?= htmlspecialchars($editCourse['course_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
        ><?= htmlspecialchars($editCourse['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <button type="submit" name="save_course">
            <?= $editCourse ? 'Update Course' : 'Add Course' ?>
        </button>

        <?php if ($editCourse): ?>
            <a href="courses.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>Course List</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($courses)): ?>
                <tr>
                    <td colspan="5">No courses found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($course['course_code'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($course['course_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($course['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="courses.php?edit=<?= (int) $course['course_id'] ?>">
                                Edit
                            </a>

                            <form method="POST" style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this course?');">
                                <input
                                    type="hidden"
                                    name="delete_id"
                                    value="<?= (int) $course['course_id'] ?>"
                                >
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="../index.php">Back to Home</a></p>
</div>

</body>
</html>