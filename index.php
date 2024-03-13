<?php
session_start();
require_once 'Admincontroller1.php';
require_once 'CashierController.php';
require_once 'Authentications.php';
require_once 'database.php';

$database = new Database();
$db = $database->conn;
$auth = new Authentications($db);
$admin1 = new AdminControl($db);
$cashier = new Cashier($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'login':
                if (isset($_POST['username']) && isset($_POST['password'])) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $result = $auth->login($username, $password);
                    echo json_encode($result);
                } else {
                    echo json_encode(array("message" => "Username and password are required"));
                }
                break;
            case 'create_user':
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_POST['username']) && isset($_POST['password'])) {
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        $result = $admin1->createUser($username, $password);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Username and password are required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can create users"));
                }
                break;
            case 'create_role':
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_POST['role'])) {
                        $role = $_POST['role'];
                        $result = $admin1->createRole($role);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Role name is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can create roles"));
                }
                break;
            case 'create_order':
                if ($_SESSION['roleid'] === 2) {
                    if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['customer'])) {
                        $name = $_POST['name'];
                        $price = $_POST['price'];
                        $customer = $_POST['customer'];
                        $result = $cashier->createOrder($name, $price, $customer);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Name, price, and customer are required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only CASHIER can create orders"));
                }
                break;
            // Add more cases for other endpoints as needed
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'view_users':
                if ($_SESSION['roleid'] === 1) {
                    $result = $admin1->viewAllUsers();
                    echo json_encode($result);
                } else {
                    echo json_encode(array("message" => "Only ADMIN can view users"));
                }
                break;
            case 'view_orders_by_customer':
                if ($_SESSION['roleid'] === 2) {
                    if (isset($_GET['customer'])) {
                        $customer = $_GET['customer'];
                        $result = $cashier->viewOrdersByCustomer($customer);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Customer is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only CASHIER can view orders by customer"));
                }
                break;
            case 'get_total_amount_by_customer':
                if ($_SESSION['roleid'] === 2) {
                    if (isset($_GET['customer'])) {
                        $customer = $_GET['customer'];
                        $result = $cashier->getTotalAmountByCustomer($customer);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Customer is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only CASHIER can get total amount by customer"));
                }
                break;
            // Add more cases for other GET endpoints as needed
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'update_password':
                if (isset($_POST['userid']) && isset($_POST['oldPassword']) && isset($_POST['newPassword'])) {
                    $userid = $_POST['userid'];
                    $oldPassword = $_POST['oldPassword'];
                    $newPassword = $_POST['newPassword'];
                    $result = $auth->changePassword($userid, $oldPassword, $newPassword);
                    echo json_encode($result);
                } else {
                    echo json_encode(array("message" => "userid, oldPassword, and newPassword are required"));
                }
                break;
            case "update_order":
                if (isset($_POST['orderid']) && isset($_POST['name']) && isset($_POST['price'])){
                    $orderid = $_POST['orderid'];
                    $name = $_POST['name']; 
                    $price = $_POST['price'];
                    $result = $cashier->updateOrder($orderid, $name, $price);
                    echo json_encode($result);
                }   else {
                    echo json_encode(array('message'=> "orderid, name, and price are required"));
                }
                break;
            // Add more cases for other PUT endpoints as needed
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'disable_user':
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_DELETE['userid'])) {
                        $userid = $_DELETE['userid'];
                        $result = $admin1->disableUser($userid);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "userid is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can disable users"));
                }
                break;
            case 'logout':
                $result = $auth->logout();
                echo json_encode($result);
                break;
            case 'disable_role':
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_DELETE['roleid'])) {
                        $roleid = $_DELETE['roleid'];
                        $result = $admin1->disableRole($roleid);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "roleid is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can disable roles"));
                }
                break;
            case 'cancel_order':
                if ($_SESSION['roleid'] === 2) {
                    if (isset($_DELETE['orderid'])) {
                        $orderid = $_DELETE['orderid'];
                        $result = $cashier->cancelOrder($orderid);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "orderid is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only CASHIER can cancel orders"));
                }
                break;
            // Add more cases for other DELETE endpoints as needed
        }
    }
} else {
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
