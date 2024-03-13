<?php
class RoleController
{
    private $db;
    private $role;

    public function __construct($db)
    {
        $this->db = $db;
        $this->role = new Role($db);
    }

    public function createRole($role, $description)
    {
        return $this->role->createRole($role, $description);
    }

    public function viewAllRoles()
    {
        return $this->role->viewAllRoles();
    }
}
?>
