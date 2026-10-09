# Training Enrollment System

## 1. Project Description

The Training Enrollment System is a web-based application developed using PHP and MySQL. It manages students, courses, classes, and enrollments.

**Main Features:**

* Student and course management
* Class scheduling and slot management
* Student enrollment and cancellation
* Enrollment record tracking

**Technologies:** PHP, MySQL, HTML, CSS, and XAMPP

<br>

## 2. Project Structure

training_enrollment/
├── admin/       # Management pages
├── classes/     # Models and repository classes
├── config/      # Database configuration
├── css/         # Stylesheets
├── includes/    # Reusable templates
├── sql/         # Database scripts
├── index.php    # Homepage
└── README.md

<br>

## 3. Setup Instructions

1. Install XAMPP and start Apache and MySQL.
2. Place the project in `C:\xampp\htdocs\training_enrollment`.
3. Open `http://localhost/phpmyadmin/`.
4. Create the `training_db` database and the four tables: `students`, `courses`, `classes`, and `enrollments`.
5. Configure the database credentials in `config/db.php`.
6. Open the application at:`http://localhost/training_enrollment/`

<br>

## 4. Design Patterns Used

**Repository Pattern:** The `EnrollmentRepository.php` class manages enrollment operations, separating database queries from the user interface and improving maintainability.

**Singleton Pattern:** Ensures a single database connection is reused, provided the implementation explicitly supports it.

**Separation of Concerns:** Separates database configuration, enrollment operations, and user interface pages into different files.

<br>

## 5. Testing

* Verify that Apache and MySQL are running.
* Confirm that the database and tables exist.
* Add sample students, courses, and classes.
* Test enrollment, record viewing, and cancellation.
* Check how the system handles invalid IDs and unavailable class slots.

<br>

## 6. Conclusion

The Training Enrollment System uses PHP and MySQL to manage training enrollments. Its design patterns and organized structure help make the application easier to maintain and extend.