<?php
class Role
{
    private $conn;
    private $table = 'roles_table';

    public $roleid;
    public $role;
    public $description;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createRole($role, $description)
    {
        $query = 'INSERT INTO ' . $this->table . '
                SET role = ?,
                description = ?';

        $stmt = $this->conn->prepare($query);

        $this->role = htmlspecialchars(strip_tags($role));
        $this->description = htmlspecialchars(strip_tags($description));

        $stmt->bind_param("ss", $this->role, $this->description);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function viewAllRoles()
    {
        $query = 'SELECT * FROM ' . $this->table;
        $result = $this->conn->query($query);
        return $result;
    }
}
?>