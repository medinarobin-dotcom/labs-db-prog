<?php
// classes/Student.php
class Student {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function find($id) {
        $sql = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    // TODO: return one student by id (prepared statement)
    // FILL IN CODE HERE
    }

    public function all() {
        $sql = "SELECT * FROM students ORDER BY full_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    // TODO: return all students ordered by full_name
    // FILL IN CODE HERE
    }
}