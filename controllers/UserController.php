<?php
class UserController
{
    private $db;
    private $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function createUser($username, $password, $role_id)
    {
        return $this->user->createUser($username, $password, $role_id);
    }

    public function viewAllUsers()
    {
        return $this->user->viewAllUsers();
    }

    public function getUserById($id)
    {
        return $this->user->getUserById($id);
    }

    public function updatePassword($user_id, $newPassword)
    {
        return $this->user->updatePassword($user_id, $newPassword);
    }
}
?>