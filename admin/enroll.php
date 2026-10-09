<?php
// admin/enroll.php -- Enroll an EXISTING student into a class
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int) ($_POST['student_id'] ?? 0);
    $class_id = (int) ($_POST['class_id'] ?? 0);

    if ($student_id <= 0 || $class_id <= 0) {
        $message = 'Please select a valid student and class.';
    } else {
        try {
            $repo->enroll($student_id, $class_id);
            $message = 'Student enrolled successfully.';
        } catch (Throwable $e) {
            if ($e->getMessage() === 'No available slots.') {
                $message = 'No slots available.';
            } else {
                $message = 'Enrollment failed.';
            }
        }
    }
}
?>
<style>
    /* General page styling */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 40px 20px;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #e0eafc, #cfdef3);
    color: #333;
}

/* Form container */
h2 {
    max-width: 450px;
    margin: 40px auto 0;
    padding: 25px 30px 10px;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    color: #1e3a8a;
    font-size: 25px;
    text-align: center;
}

/* Form layout */
form {
    max-width: 450px;
    margin: 0 auto;
    padding: 20px 30px 30px;
    background: #ffffff;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Labels */
label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: bold;
    color: #374151;
}

/* Input fields */
input[type="number"] {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 15px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    background: #f9fafb;
    font-size: 15px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

input[type="number"]:focus {
    border-color: #2563eb;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

/* Submit button */
button[type="submit"] {
    width: 100%;
    padding: 13px;
    margin-top: 10px;
    border: none;
    border-radius: 7px;
    background: #2563eb;
    color: #ffffff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}

button[type="submit"]:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

button[type="submit"]:active {
    transform: translateY(0);
}

/* Status message */
body > p {
    max-width: 450px;
    margin: 15px auto;
    padding: 12px 15px;
    border-radius: 7px;
    background: #dcfce7;
    color: #166534;
    text-align: center;
    font-size: 14px;
}

/* Responsive design */
@media (max-width: 480px) {
    body {
        padding: 20px 12px;
    }

    h2 {
        margin-top: 20px;
        padding: 22px 18px 10px;
        font-size: 22px;
    }

    form {
        padding: 20px 18px 25px;
    }
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Student</title>
</head>
<body>
    <h2>Enroll Existing Student</h2>

    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="student_id">Student ID:</label>
        <input type="number" name="student_id" id="student_id" min="1" required>

        <br><br>

        <label for="class_id">Class ID:</label>
        <input type="number" name="class_id" id="class_id" min="1" required>

        <br><br>

        <button type="submit">Enroll Student</button>
    </form>
</body>
</html>