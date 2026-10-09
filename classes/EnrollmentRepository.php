<?php
// classes/EnrollmentRepository.php
// Data-access class that records students, enrollments and slot updates
// inside a single database transaction.

class EnrollmentRepository
{
    private $db;
    public function __construct(PDO $db){$this->db = $db;}

    public function recordStudent($full_name, $email, $phone, $class_id)
    {
        try {$this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE"
            );
            $stmt->execute(['class_id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || $class['slots'] <= 0) {
                throw new Exception("No available slots.");
            }

            $stmt = $this->db->prepare(
                "INSERT INTO students (full_name, email, phone)
                 VALUES (:full_name, :email, :phone)"
            );

            $stmt->execute([
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone
            ]);

            $student_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id)
                 VALUES (:student_id, :class_id)"
            );

            $stmt->execute([
                'student_id' => $student_id,
                'class_id' => $class_id
            ]);

            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1
                 WHERE class_id = :class_id"
            );
            $stmt->execute(['class_id' => $class_id]);

            $this->db->commit();

            return $student_id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function enroll($student_id, $class_id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE"
            );
            $stmt->execute(['class_id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || $class['slots'] <= 0) {
                throw new Exception("No available slots.");
            }

            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id)
                 VALUES (:student_id, :class_id)"
            );

            $stmt->execute([
                'student_id' => $student_id,
                'class_id' => $class_id
            ]);

            $enrollment_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1
                 WHERE class_id = :class_id"
            );
            $stmt->execute(['class_id' => $class_id]);

            $this->db->commit();

            return $enrollment_id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function cancel($enrollment_id)
    {
        try {$this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT class_id, status
                 FROM enrollments
                 WHERE enrollment_id = :enrollment_id
                 FOR UPDATE"
            );
            $stmt->execute(['enrollment_id' => $enrollment_id]);
            $enrollment = $stmt->fetch();

            if (!$enrollment) {
                throw new Exception("Enrollment not found.");
            }

            if ($enrollment['status'] === 'cancelled') {
                throw new Exception("Enrollment is already cancelled.");
            }

            $stmt = $this->db->prepare(
                "UPDATE enrollments
                 SET status = 'cancelled'
                 WHERE enrollment_id = :enrollment_id"
            );
            $stmt->execute(['enrollment_id' => $enrollment_id]);

            $stmt = $this->db->prepare(
                "UPDATE classes
                 SET slots = slots + 1
                 WHERE class_id = :class_id"
            );
            $stmt->execute(['class_id' => $enrollment['class_id']]);

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function allWithDetails()
{
    $sql = "SELECT
                e.*,
                s.full_name,
                s.email,
                s.phone,
                c.class_code AS class_code,
                c.schedule,
                c.instructor,
                co.course_name
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN classes c ON e.class_id = c.class_id
            JOIN courses co ON c.course_id = co.course_id
            ORDER BY s.full_name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}
}