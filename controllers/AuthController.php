<?php
class AuthController
{
    private $db;
    private $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function login($username, $password)
    {
        $user = $this->user->getUserByUsername($username);

        if (!$user) {
            return array("success" => false, "message" => "User not found.");
        }

        $storedPassword = $this->user->getPasswordByUsername($username);

        // Compare plain text passwords
        if ($password === $storedPassword) {
            // Password is correct, create session or token
            // Return user data, token, etc.
            $role_id = $user['roleid'] ?? null; // Get role_id from user data
            return array("success" => true, "message" => "Login successful.", "roleid" => $role_id);
        } else {
            return array("success" => false, "message" => "Invalid password.");
        }
    }


    public function logout()
    {
        session_unset();
        session_destroy();
        return array("success" => true, "message" => "Logout successful.");
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['userid']);
    }

    public function getCurrentUserRole()
    {
        return $_SESSION['roleid'] ?? null;
    }

    public function isStudent()
    {
        $role_id = $_SESSION['roleid'] ?? null;
        return $role_id === 3; // Assuming STUDENT role_id is 3
    }

    public function isFaculty()
    {
        $role_id = $_SESSION['roleid'] ?? null;
        return $role_id === 2; // Assuming FACULTY role_id is 2
    }

    public function isAdmin()
    {
        $role_id = $_SESSION['roleid'] ?? null;
        return $role_id === 1; // Assuming ADMIN role_id is 1
    }

    public function changePassword($oldPassword, $newPassword)
    {
        if (!$this->isLoggedIn()) {
            return array("success" => false, "message" => "User not logged in.");
        }

        $user_id = $_SESSION['userid'];
        $user = $this->user->getUserById($user_id);

        if (!$user) {
            return array("success" => false, "message" => "User not found.");
        }

        if ($oldPassword !== $user['password']) {
            return array("success" => false, "message" => "Invalid old password.");
        }

        $result = $this->user->updatePassword($user_id, $newPassword);

        if ($result) {
            return array("success" => true, "message" => "Password updated successfully.");
        } else {
            return array("success" => false, "message" => "Failed to update password.");
        }
    }
}
?>
