<?php
session_start(); // Start the session

include_once 'config/database.php';
include_once 'models/User.php';
include_once 'models/Role.php';
include_once 'controllers/AuthController.php';
include_once 'controllers/UserController.php';
include_once 'controllers/RoleController.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize controllers
$userController = new UserController($db);
$roleController = new RoleController($db);
$authController = new AuthController($db);

// Check request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Handle GET requests
        $endpoint = $_GET['endpoint'] ?? '';

        switch ($endpoint) {
            case 'users':
                // Check if user is ADMIN or FACULTY
                if (!$authController->isLoggedIn() || (!$authController->isAdmin() && !$authController->isFaculty())) {
                    http_response_code(403);
                    echo json_encode(array("message" => "Forbidden"));
                    break;
                }

                // View all users
                $result = $userController->viewAllUsers();
                // Output JSON response
                echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
                break;

            case 'roles':
                // Check if user is ADMIN
                if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
                    http_response_code(403);
                    echo json_encode(array("message" => "Forbidden"));
                    break;
                }

                // View all roles
                $result = $roleController->viewAllRoles();
                // Output JSON response
                echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Invalid endpoint"));
        }
        break;

    case 'POST':
        // Handle POST requests
        $data = json_decode(file_get_contents("php://input"));

        // Check the endpoint for login, logout, or change_password
        $endpoint = $_GET['endpoint'] ?? '';

        switch ($endpoint) {
            case 'login':
                // Handle login
                $result = $authController->login($data->username, $data->password);
                // Output JSON response
                echo json_encode($result);
                break;

            case 'logout':
                // Handle logout
                $result = $authController->logout();
                // Output JSON response
                echo json_encode($result);
                break;

            case 'change_password':
                // Check if user is logged in
                if (!$authController->isLoggedIn()) {
                    http_response_code(403);
                    echo json_encode(array("message" => "Forbidden"));
                    break;
                }

                // Handle change password
                $result = $authController->changePassword($data->old_password, $data->new_password);
                // Output JSON response
                echo json_encode($result);
                break;

            case 'create_user':
                // Check if user is ADMIN
                if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
                    http_response_code(403);
                    echo json_encode(array("message" => "Forbidden"));
                    break;
                }

                // Create user
                $result = $userController->createUser($data->username, $data->password, $data->role_id);
                // Output JSON response
                if ($result) {
                    echo json_encode(array("message" => "User created successfully"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Failed to create user"));
                }
                break;

            case 'create_role':
                // Check if user is ADMIN
                if (!$authController->isLoggedIn() || !$authController->isAdmin()) {
                    http_response_code(403);
                    echo json_encode(array("message" => "Forbidden"));
                    break;
                }

                // Create role
                $result = $roleController->createRole($data->role, $data->description);
                // Output JSON response
                if ($result) {
                    echo json_encode(array("message" => "Role created successfully"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Failed to create role"));
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(array("message" => "Invalid endpoint"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
}

?>