<?php

class AdminControl{

    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function createUser($username, $password)
    {
        // Check if the username already exists
        $checkQuery = "SELECT * FROM users_table WHERE username = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            return array("message" => "Username already exists");
        }

        // Create a new user
        $createQuery = "INSERT INTO users_table (username, password, roleid, status, is_logged_in) VALUES (?, ?, 1, 1, 0)";
        $createStmt = $this->conn->prepare($createQuery);
        $createStmt->bind_param("ss", $username, $password);
        if ($createStmt->execute()) {
            return array("message" => "User created successfully");
        } else {
            return array("message" => "Failed to create user");
        }
    }

    public function disableUser($userid)
    {
        // Disable a user
        $disableQuery = "UPDATE users_table SET status = 0 WHERE userid = ?";
        $disableStmt = $this->conn->prepare($disableQuery);
        $disableStmt->bind_param("i", $userid);
        if ($disableStmt->execute()) {
            return array("message" => "User disabled successfully");
        } else {
            return array("message" => "Failed to disable user");
        }
    }

    public function createRole($role)
    {
        // Create a new role
        $createQuery = "INSERT INTO roles_table (role) VALUES (?)";
        $createStmt = $this->conn->prepare($createQuery);
        $createStmt->bind_param("s", $role);
        if ($createStmt->execute()) {
            return array("message" => "Role created successfully");
        } else {
            return array("message" => "Failed to create role");
        }
    }

    public function disableRole($roleid)
    {
        // Disable a role
        $disableQuery = "DELETE FROM roles_table WHERE roleid = ?";
        $disableStmt = $this->conn->prepare($disableQuery);
        $disableStmt->bind_param("i", $roleid);
        if ($disableStmt->execute()) {
            return array("message" => "Role disabled successfully");
        } else {
            return array("message" => "Failed to disable role");
        }
    }

    public function viewAllUsers()
    {
        // View all users
        $query = "SELECT * FROM users_table";
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } else {
            return array("message" => "No users found");
        }
    }

    public function updatePassword($userid, $oldPassword, $newPassword)
    {
        // Check if the old password is correct
        $checkQuery = "SELECT * FROM users_table WHERE userid = ? AND password = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("is", $userid, $oldPassword);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 1) {
            // Update the password
            $updateQuery = "UPDATE users_table SET password = ? WHERE userid = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newPassword, $userid);
            if ($updateStmt->execute()) {
                return array("message" => "Password updated successfully");
            } else {
                return array("message" => "Failed to update password");
            }
        } else {
            return array("message" => "Incorrect old password");
        }
    }
}
?>