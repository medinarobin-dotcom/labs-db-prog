<?php
// admin/students.php -- Student recording form
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$classes = new ClassSection($db);
$classList = $classes->allWithCourse();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = (int) ($_POST['class_id'] ?? 0);

    if ($full_name === '') {
        $message = 'Please enter the student name.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif ($class_id <= 0) {
        $message = 'Please select a valid class.';
        $messageType = 'error';
    } else {
        try {
            $repo->recordStudent(
                $full_name,
                $email,
                $phone,
                $class_id
            );

            $message = 'Student recorded and enrolled successfully.';
            $messageType = 'success';
        } catch (Throwable $e) {
            if ($e->getMessage() === 'No available slots.') {
                $message = 'No slots available for the selected class.';
            } else {
                $message = 'Unable to record student. Please check your input and try again.';
            }

            $messageType = 'error';
        }
    }

    $classList = $classes->allWithCourse();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #f4f6f9;
        }

        h2 {
            text-align: center;
        }

        form {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <h2>Student Recording Form</h2>

    <?php if ($message !== ''): ?>
        <div class="<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="full_name">Full Name</label>
        <input
            type="text"
            id="full_name"
            name="full_name"
            value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="phone">Phone</label>
        <input
            type="text"
            id="phone"
            name="phone"
            value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="class_id">Select Class</label>
        <select id="class_id" name="class_id" required>
            <option value="">-- Select a class --</option>

            <?php foreach ($classList as $class): ?>
                <option
                    value="<?= (int) $class['class_id'] ?>"
                    <?= (isset($_POST['class_id']) &&
                         (int) $_POST['class_id'] === (int) $class['class_id'])
                         ? 'selected' : '' ?>
                    <?= (int) $class['slots'] <= 0 ? 'disabled' : '' ?>
                >
                    <?= htmlspecialchars($class['course_name'], ENT_QUOTES, 'UTF-8') ?>
                    - <?= htmlspecialchars($class['course_code'], ENT_QUOTES, 'UTF-8') ?>
                    (<?= (int) $class['slots'] ?> slots available)
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Record Student and Enroll</button>
    </form>

</body>
</html>