<?php
// classes/ClassSection.php

class ClassSection
{
    private $db;
    public function __construct(PDO $db){$this->db = $db;}

    public function allWithCourse()
    {
        $sql = "SELECT c.*, co.course_name, co.course_code
                FROM classes c
                JOIN courses co ON c.course_id = co.course_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create($course_id, $code, $schedule, $instructor, $slots)
    {
        $sql = "INSERT INTO classes
                (course_id, class_code, schedule, instructor, slots)
                VALUES
                (:course_id, :code, :schedule, :instructor, :slots)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'course_id' => $course_id,
            'code' => $code,
            'schedule' => $schedule,
            'instructor' => $instructor,
            'slots' => $slots
        ]);
    }

    public function getSlots($class_id)
    {
        $sql = "SELECT slots FROM classes WHERE class_id = :class_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['class_id' => $class_id]);

        $result = $stmt->fetch();

        return $result ? $result['slots'] : 0;
    }
}