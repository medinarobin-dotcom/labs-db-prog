<?php
// index.php
require_once __DIR__ . '/includes/header.php';
?>
<h1>Training Enrollment System</h1>
<p>
    Welcome to the Training Enrollment System. Manage courses, class sections,
    student records, and enrollments in one place.
</p>

<h2>Admin Pages</h2>
<ul>
    <li><a href="admin/students.php">Register a New Student</a></li>
    <li><a href="admin/enroll.php">Enroll an Existing Student</a></li>
    <li><a href="admin/enrollments.php">View and Manage Enrollments</a></li>
</ul>

<?php require_once __DIR__ . '/includes/footer.php'; ?>