<?php
// classes/Course.php

class Course
{
    private $db;
    public function __construct(PDO $db){$this->db = $db;}

    public function all()
    {
        $sql = "SELECT * FROM courses ORDER BY course_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $sql = "SELECT * FROM courses WHERE course_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function create($code, $name, $description)
    {
        $sql = "INSERT INTO courses (course_code, course_name, description)
                VALUES (:code, :name, :description)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'code' => $code,
            'name' => $name,
            'description' => $description
        ]);
    }

    public function update($id, $code, $name, $description)
    {
        $sql = "UPDATE courses
                SET course_code = :code,
                    course_name = :name,
                    description = :description
                WHERE course_id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'code' => $code,
            'name' => $name,
            'description' => $description
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM courses WHERE course_id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}