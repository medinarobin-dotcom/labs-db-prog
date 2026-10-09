<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Course.php';

$classModel = new ClassSection($db);
$courseModel = new Course($db);

$message = '';
$messageType = 'success';

// Create class section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_class'])) {
    $course_id = (int) ($_POST['course_id'] ?? 0);
    $code = trim($_POST['code'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots = filter_var($_POST['slots'] ?? null, FILTER_VALIDATE_INT);

    if (
        $course_id <= 0 ||
        $code === '' ||
        $schedule === '' ||
        $instructor === '' ||
        $slots === false ||
        $slots === null ||
        $slots < 0
    ) {
        $message = 'Please provide valid information for all fields.';
        $messageType = 'error';
    } else {
        try {
            $classModel->create(
                $course_id,
                $code,
                $schedule,
                $instructor,
                $slots
            );

            $message = 'Class section created successfully.';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$courses = $courseModel->all();
$classList = $classModel->allWithCourse();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class Schedules and Slots</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <h1>Manage Class Schedules and Slots</h1>

    <?php if ($message !== ''): ?>
        <p class="<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>Add Class Section</h2>

    <form method="POST">
        <label for="course_id">Course</label>
        <select name="course_id" id="course_id" required>
            <option value="">-- Select Course --</option>

            <?php foreach ($courses as $course): ?>
                <option value="<?= (int) $course['course_id'] ?>">
                    <?= htmlspecialchars($course['course_code'] . ' - ' . $course['course_name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="code">Class Code</label>
        <input
            type="text"
            name="code"
            id="code"
            required
        >

        <label for="schedule">Schedule</label>
        <input
            type="text"
            name="schedule"
            id="schedule"
            placeholder="e.g. Monday 8:00 AM - 10:00 AM"
            required
        >

        <label for="instructor">Instructor</label>
        <input
            type="text"
            name="instructor"
            id="instructor"
            required
        >

        <label for="slots">Available Slots</label>
        <input
            type="number"
            name="slots"
            id="slots"
            min="0"
            required
        >

        <button type="submit" name="save_class">Add Class Section</button>
    </form>

    <h2>Class Sections</h2>

    <table>
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Course</th>
                <th>Class Code</th>
                <th>Schedule</th>
                <th>Instructor</th>
                <th>Available Slots</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($classList)): ?>
                <tr>
                    <td colspan="6">No class sections found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($classList as $class): ?>
                    <tr>
                        <td><?= (int) $class['class_id'] ?></td>
                        <td><?= htmlspecialchars($class['course_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($class['class_code'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($class['schedule'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($class['instructor'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $class['slots'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="../index.php">Back to Home</a></p>

</div>

</body>
</html>