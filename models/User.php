<?php
class User
{
    private $conn;
    private $table = 'users_table';
    public $username;
    public $password;
    public $role_id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createUser($username, $password, $role_id)
    {
        $query = 'INSERT INTO ' . $this->table . '
            SET username = ?,
                password = ?,
                roleid = ?';

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($username));
        $this->password = htmlspecialchars(strip_tags($password));
        $this->role_id = htmlspecialchars(strip_tags($role_id));

        $stmt->bind_param("ssi", $this->username, $this->password, $this->role_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function viewAllUsers()
    {
        $query = 'SELECT * FROM ' . $this->table;
        $result = $this->conn->query($query);
        return $result;
    }

    public function getUserById($id)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE userid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updatePassword($user_id, $newPassword)
    {
        $query = 'UPDATE ' . $this->table . ' SET password = ? WHERE userid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $newPassword, $user_id);
        return $stmt->execute();
    }

    public function getUserByUsername($username)
    {
        $query = 'SELECT userid, username, roleid FROM ' . $this->table . ' WHERE username = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function getPasswordByUsername($username)
    {
        $query = 'SELECT password FROM ' . $this->table . ' WHERE username = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['password'] ?? null;
    }
}
?>