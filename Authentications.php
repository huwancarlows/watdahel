<?php

class Authentications{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }


    public function login($username, $password)
    {
        // Check if user exists in the database
        $query = "SELECT * FROM users_table WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $_SESSION['loggedin'] = true;
            return array("message" => "Login successful");
        } else {
            return array("message" => "Invalid username or password");
        }
    }

    public function logout()
    {
        // Unset all session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();
        return array("message" => "Logout successful");
    }

    public function changePassword($userid, $oldPassword, $newPassword)
    {
        // Check if the old password is correct
        $query = "SELECT * FROM users_table WHERE userid = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $userid, $oldPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
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