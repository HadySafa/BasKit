<?php

use FontLib\Table\Type\head;

require_once __DIR__ . '/databaseAccess.php';
require_once __DIR__ . '\../Model/User.php';
require_once __DIR__ . '\../Model/Feedback.php';

class Controller
{

    private $databaseAccess;

    public function __construct()
    {
        $this->databaseAccess = new databaseAccess();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hady's Part  

    // Functions

    public function startSession($user)
    {
        $_SESSION["Id"] = $user->getId();
        $_SESSION["Name"] = $user->getName();
        $_SESSION["Balance"] = $user->getBalance();
        $_SESSION["Role"] = $user->getRole();
        $_SESSION["OrderType"] = "Online";
    }

    public function getSessionInfo()
    {
        return [$_SESSION["Name"], $_SESSION["Balance"]];
    }

    public function getUserId()
    {
        return $_SESSION["Id"];
    }

    public function isAdmin()
    {
        return $_SESSION["Role"] == 'Admin';
    }

    public function isPending($status)
    {
        return $status['Status'] == 'Pending';
    }

    public function isCustomer()
    {
        return $_SESSION["Role"] == 'Customer';
    }

    public function checkCustomerLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Customer") {
            header("Location: ./Login.php");
        }
    }

    public function isCustomerLogin()
    {
        if (!isset($_SESSION['Id'])) {
            return false;
        }
        return true;
    }

    public function checkLoggedIn()
    {
        if (empty($_SESSION['Id'])) {
            header("Location: ./Login.php");
            exit();
        }
    }

    public function checkManagerLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Admin") {
            header("Location: ./Login.php");
            exit();
        }
    }

    public function checkAdminLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Admin") {
            if ($_SESSION['Role'] == "Customer") {
                header("Location: User.php");
                return;
            }
            header("Location: Login.php");
            exit();
        }
    }

    public function logout()
    {
        // remove all session variables and destroy the session
        session_unset();
        session_destroy();
        header("Location: Login.php");
        exit();
    }

    public function cleanInput($data)
    {
        $data = strip_tags($data); // Remove all HTML tags
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special chars
    }

    public function goToLogin()
    {
        header('Location: ./Login.php');
    }

    private function generateCurrentTimestamp()
    {
        $date = new DateTime("now", new DateTimeZone("Asia/Beirut"));
        return $date->format(DateTime::ATOM);
    }

    /////////////////////////////////////////////////////////

    // User

    public function addUser($submittedInfo)
    {
        if (!$this->isUnique('Email', $submittedInfo['Email'])) {
            return "This email is already registered. Please use a different email address.";
        } elseif (!$this->isUnique('Phone', $submittedInfo['Phone'])) {
            return "This phone number is already associated with an account. Please use a different number.";
        } else {
            // unique
            $tempUser = new User($submittedInfo['Name'], $submittedInfo['Email'], $submittedInfo['Phone'], 'Customer', $submittedInfo['Password'], 0.00);
            $rowCount = $this->databaseAccess->addUser($tempUser);
            if ($rowCount > 0) header('Location: ./Login.php');
            else header('Location: ./Register.php');
            return '';
        }
    }

    private function isUnique($type, $parameter)
    {
        if ($type == 'Email') {
            $tempUser = $this->databaseAccess->getUserByEmail($parameter);
            if (is_array($tempUser) && count($tempUser) == 0) {
                return true;
            }
            return false;
        } elseif ($type == 'Phone') {
            $tempUser = $this->databaseAccess->getUserByPhone($parameter);
            if (is_array($tempUser) && count($tempUser) == 0) {
                return true;
            }
            return false;
        }
    }

    public function validateUser($submittedInfo)
    {
        $tempUser = $this->databaseAccess->getUserByEmail($submittedInfo['Email']);
        if ($tempUser) {
            if ($tempUser->isActive() && password_verify($submittedInfo['Password'], $tempUser->getPassword())) {
                $this->startSession($tempUser);
                header('Location: ./index.php');
                return '';
            } else return "Wrong credentials.";
        } else {
            if (is_array($tempUser) && count($tempUser) == 0) return "Wrong credentials.";
            return "Try again later.";
        }
    }

    private function validateOldPassword($id, $password)
    {
        $tempUser = $this->databaseAccess->getUserById($id);
        if ($tempUser) {
            if ($tempUser->isActive() && password_verify($password, $tempUser->getPassword())) {
                return '';
            } else return "Wrong credentials.";
        } else {
            if (is_array($tempUser) && count($tempUser) == 0) return "Wrong credentials.";
            return "Try again later.";
        }
    }

    private function updatePassword($id, $password)
    {
        $rowCount = $this->databaseAccess->updateUserPassword($id, $password);
        if (is_numeric($rowCount) && $rowCount > 0) {
            // successfully changed
            return '';
        } else return 'Action Failed';
    }

    public function changePassword($submittedInfo)
    {
        $response = self::validateOldPassword($_SESSION["Id"], $submittedInfo['OldPassword']);
        if (!$response) {
            // old password verified
            $response2 = self::updatePassword($_SESSION["Id"], $submittedInfo['NewPassword']);
            if (!$response2) {
                // password changed
                header('Location: ./Login.php');
                return '';
            }
            return $response2;
        } else {
            return $response;
        }
    }

    /////////////////////////////////////////////////////////

    // Order

    public function submitOrder($array)
    {
        $tempOrder = new Order(self::getUserId(), self::generateCurrentTimestamp(), $array['PaymentMethod'], "Online", $array['Location'], "Pending");
        $rowCount = $this->databaseAccess->addOrder($tempOrder);
        if ($rowCount > 0) {
            // add the products that correcsponds to that order
            header('Location: ./Orders.php');
            return '';
            exit();
        }
        return "Failed to perform action, try again later.";
    }

    public function getUserOrders($id, $status)
    {
        $orders = $this->databaseAccess->getUserOrders($id);
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    if ($order->getStatus() == $status) $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No $status order yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function getPendingOrders()
    {
        $orders = $this->databaseAccess->getAllOrders('Pending');
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No pending order yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function getCompletedOrders()
    {
        $orders = $this->databaseAccess->getAllOrders('Completed');
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No completed order yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function getOrder($id)
    {
        $order = $this->databaseAccess->getOrderById($id);
        if (is_array($order)) {
            if (count($order) > 0) {
                return $order[0]->toArray();
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function getOrdersProducts($id)
    {
        $products = $this->databaseAccess->getOrdersProducts($id);
        if (is_array($products)) {
            if (count($products) > 0) {
                return $products;
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function getOrderByIdUserId($id)
    {
        $order = $this->databaseAccess->getOrderByIdUserId($id, self::getUserId());
        if (is_array($order)) {
            if (count($order) > 0) {
                return $order[0]->toArray();
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function deliverOrder($id)
    {
        $rowCount = $this->databaseAccess->updateOrderStatus($id, 'Completed');
        if (is_numeric($rowCount) && $rowCount > 0) {
            // successfully changed
            return '';
        } else return 'Action Failed';
    }

    /////////////////////////////////////////////////////////

    // Feedback

    public function submitFeedback($description)
    {
        $tempFeedback = new Feedback(self::getUserId(), $description, 1, self::generateCurrentTimestamp());
        $rowCount = $this->databaseAccess->addFeedback($tempFeedback);
        if ($rowCount > 0) {
            header('Location: ./Orders.php');
            return '';
            exit();
        }
        return "Failed to perform action, try again later.";
    }

    /////////////////////////////////////////////////////////

}
