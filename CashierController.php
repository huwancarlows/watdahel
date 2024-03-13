<?php

class Cashier{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($name, $price, $customer)
    {
        // Create a new order
        $createQuery = "INSERT INTO orders_table (name, price, customer, status) VALUES (?, ?, ?, 0)";
        $createStmt = $this->conn->prepare($createQuery);
        $createStmt->bind_param("sss", $name, $price, $customer);
        if ($createStmt->execute()) {
            return array("message" => "Order created successfully");
        } else {
            return array("message" => "Failed to create order");
        }
    }

    public function updateOrder($orderid, $name, $price)
    {
        // Update an order
        $updateQuery = "UPDATE orders_table SET name = ?, price = ? WHERE orderid = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $name, $price, $orderid);
        if ($updateStmt->execute()) {
            return array("message" => "Order updated successfully");
        } else {
            return array("message" => "Failed to update order");
        }
    }

    public function cancelOrder($orderid)
    {
        // Cancel an order
        $cancelQuery = "UPDATE orders_table SET status = 2 WHERE orderid = ?";
        $cancelStmt = $this->conn->prepare($cancelQuery);
        $cancelStmt->bind_param("i", $orderid);
        if ($cancelStmt->execute()) {
            return array("message" => "Order canceled successfully");
        } else {
            return array("message" => "Failed to cancel order");
        }
    }

    public function viewOrdersByCustomer($customer)
    {
        // View all orders of a specific customer
        $query = "SELECT * FROM orders_table WHERE customer = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $customer);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $orders = array();
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            return $orders;
        } else {
            return array("message" => "No orders found for this customer");
        }
    }

    public function getTotalAmountByCustomer($customer)
    {
        // Calculate total amount of orders for a customer
        $query = "SELECT SUM(price) AS total_amount FROM orders_table WHERE customer = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $customer);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return array("total_amount" => $row['total_amount']);
        } else {
            return array("message" => "No orders found for this customer");
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