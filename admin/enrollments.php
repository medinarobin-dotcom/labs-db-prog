<?php
// admin/enrollments.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'])) {
    $enrollment_id = (int) $_POST['enrollment_id'];

    if ($enrollment_id > 0) {
        try {
            $repo->cancel($enrollment_id);
            $message = 'Enrollment cancelled successfully.';
        } catch (Throwable $e) {
            $message = 'Unable to cancel enrollment.';
        }
    }
}

$rows = $repo->allWithDetails();
?>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 40px 25px;
        font-family: Arial, sans-serif;
        background: #f1f5f9;
        color: #1e293b;
    }

    h2 {
        max-width: 1400px;
        margin: 0 auto 25px;
        font-size: 28px;
        color: #1e3a8a;
        border-left: 5px solid #2563eb;
        padding-left: 15px;
    }

    /* Table container */
    table {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        border-collapse: collapse;
        background: #ffffff;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
    }

    thead {
        background: #1e3a8a;
        color: #ffffff;
    }

    th {
        padding: 16px 14px;
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    td {
        padding: 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        vertical-align: middle;
    }

    tbody tr {
        transition: background 0.2s;
    }

    tbody tr:hover {
        background: #eff6ff;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status column */
    td:nth-child(8) {
        font-weight: bold;
        text-transform: capitalize;
    }

    /* Cancel form */
    td form {
        margin: 0;
    }

    button {
        padding: 9px 15px;
        border: none;
        border-radius: 6px;
        background: #dc2626;
        color: #ffffff;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }

    button:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    /* Success or notification message */
    body > p {
        max-width: 1400px;
        margin: 0 auto 20px;
        padding: 14px 18px;
        border: 1px solid #86efac;
        border-radius: 7px;
        background: #dcfce7;
        color: #166534;
        font-weight: 600;
    }

    /* Cancelled text */
    td:last-child {
        color: #64748b;
    }

    /* Responsive table on smaller screens */
    @media (max-width: 900px) {
        body {
            padding: 25px 15px;
        }

        h2 {
            font-size: 23px;
        }

        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        th,
        td {
            padding: 12px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding: 20px 10px;
        }

        h2 {
            font-size: 21px;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollments</title>
</head>
<body>
    <h2>All Enrollments</h2>

    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Enrollment ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Class Code</th>
                <th>Course Name</th>
                <th>Schedule</th>
                <th>Instructor</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['enrollment_id'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['class_code'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['course_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['schedule'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['instructor'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? 'active', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (($row['status'] ?? 'active') !== 'cancelled'): ?>
                            <form method="POST" onsubmit="return confirm('Cancel this enrollment?');">
                                <input type="hidden" name="enrollment_id"
                                       value="<?= (int) $row['enrollment_id'] ?>">
                                <button type="submit">Cancel</button>
                            </form>
                        <?php else: ?>
                            Cancelled
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="9">No enrollments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>