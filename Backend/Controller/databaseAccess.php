<?php

define("host", "localhost");
define("databaseName", "market-name");
define("connectionString", "mysql:host=" . host . ";dbname=" . databaseName . "");
define("username", "root");
define("password", "");

require_once __DIR__ . '\../Model/Order.php';
require_once __DIR__ . '\../Model/User.php';
require_once __DIR__ . '\../Model/Feedback.php';

class databaseAccess
{
    public static $connection = null;

    public function __construct()
    {
        //self::$connection = self::createConnection();
    }

    // Hady's Part

    private static function createConnection()
    {
        if (self::$connection == null) {

            self::$connection = new PDO(connectionString, username, password);

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return self::$connection;
        } elseif (self::$connection !== null) {
            return self::$connection;
        }
    }

    // User

    public function addUser($user)
    {

        $query = "INSERT INTO user (Name, Email, Phone, Password ,Role,Balance,Active) VALUES (?, ?, ?, ?, ?,?,?)";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$user->getName(), $user->getEmail(), $user->getPhone(), $this->hashPassword($user->getPassword()), $user->getRole(), $user->getBalance(), $user->isActive()]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }

    }

    public function updateUserPassword($id, $password)
    {
        $query = "UPDATE User SET Password = ? WHERE Id = ?";
        self::$connection = self::createConnection();
        try {
            $result = self::$connection->prepare($query);
            $result->execute([$this->hashPassword($password), $id]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getUserByEmail($email)
    {

        $query = "SELECT * FROM User WHERE Email = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$email]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $tempUser = new User($data[0]['Name'], $data[0]['Email'], $data[0]['Phone'], $data[0]['Role'], $data[0]['Password'], $data[0]['Balance']);
                $tempUser->setId($data[0]['Id']);
                $tempUser->setActive($data[0]['Active']);
                return $tempUser;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function getUserById($id)
    {

        $query = "SELECT * FROM User WHERE Id = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $tempUser = new User($data[0]['Name'], $data[0]['Email'], $data[0]['Phone'], $data[0]['Role'], $data[0]['Password'], $data[0]['Balance']);
                $tempUser->setId($data[0]['Id']);
                $tempUser->setActive($data[0]['Active']);
                return $tempUser;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function getUserByPhone($phone)
    {

        $query = "SELECT * FROM User WHERE Phone = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$phone]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $tempUser = new User($data[0]['Name'], $data[0]['Email'], $data[0]['Phone'], $data[0]['Role'], $data[0]['Password'], $data[0]['Balance']);
                $tempUser->setId($data[0]['Id']);
                $tempUser->setActive($data[0]['Active']);
                return $tempUser;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    function hashPassword($password){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashedPassword;
    }

    ///////////////////////////////////////////////////

    // Order

    public function addOrder($order)
    {

        $query = "INSERT INTO `order` (UserId, Timestamp, PaymentMethod, OrderType ,Location,Status) VALUES (?, ?, ?, ?, ?,?)";

        try {
            echo $query;
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$order->getUserId(), $order->getTime(),$order->getPaymentMethod(),$order->getOrderType(),$order->getLocation(),$order->getStatus()]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function updateOrderStatus($id, $status)
    {
        $query = "UPDATE `ORDER` SET Status = ? WHERE Id = ?";
        self::$connection = self::createConnection();
        try {
            $result = self::$connection->prepare($query);
            $result->execute([$status, $id]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getUserOrders($userId)
    {

        $query = 'SELECT * FROM `order` WHERE UserId = ?';

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$userId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $orders = [];
                foreach ($data as $order) {
                    $orderObj = new Order($order['UserId'], $order['Timestamp'], $order['PaymentMethod'], $order['OrderType'], $order['Location'], $order['Status']);
                    $orderObj->setId($order['Id']);
                    $orders[] = $orderObj;
                }
                return $orders;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function getAllOrders($status)
    {

        $query = 'SELECT * FROM `order` WHERE Status = ?';

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$status]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $orders = [];
                foreach ($data as $order) {
                    $orderObj = new Order($order['UserId'], $order['Timestamp'], $order['PaymentMethod'], $order['OrderType'], $order['Location'], $order['Status']);
                    $orderObj->setId($order['Id']);
                    $orders[] = $orderObj;
                }
                return $orders;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function getOrderById($id)
    {
        $query = "SELECT * FROM `ORDER` WHERE Id = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $orders = [];
                foreach ($data as $order) {
                    $orderObj = new Order($order['UserId'], $order['Timestamp'], $order['PaymentMethod'], $order['OrderType'], $order['Location'], $order['Status']);
                    $orderObj->setId($order['Id']);
                    $orders[] = $orderObj;
                }
                return $orders;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function getOrderByIdUserId($id, $userId)
    {
        $query = "SELECT * FROM `ORDER` WHERE Id = ? AND UserId = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$id, $userId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                $orders = [];
                foreach ($data as $order) {
                    $orderObj = new Order($order['UserId'], $order['Timestamp'], $order['PaymentMethod'], $order['OrderType'], $order['Location'], $order['Status']);
                    $orderObj->setId($order['Id']);
                    $orders[] = $orderObj;
                }
                return $orders;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    ///////////////////////////////////////////////////

    // Feedback

    public function addFeedback($feedback)
    {
        $query = "INSERT INTO Feedback (UserId, Description, Published, Timestamp) VALUES (?, ?, ?, ?)";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$feedback->getUserId(), $feedback->getDescription(), $feedback->isPublished(), $feedback->getTimeStamp()]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    ///////////////////////////////////////////////////

    // Temporary method
    public function getOrdersProducts($id)
    {
        $query = "SELECT * FROM 
        (SELECT * FROM ORDERPRODUCT WHERE OrderId = ?) AS ORDERPRODUCTS 
        NATURAL JOIN 
        (SELECT ID AS ProductId, NAME AS PRODUCTNAME FROM PRODUCT) AS PRODUCTS    
        ";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$id]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                return $data;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

}
