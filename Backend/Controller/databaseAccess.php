<?php

define("host", "localhost");
define("databaseName", "baskit");
define("connectionString", "mysql:host=" . host . ";dbname=" . databaseName . "");
define("username", "root");
define("password", "");

require_once __DIR__ . '\../Model/Order.php';
require_once __DIR__ . '\../Model/User.php';
require_once __DIR__ . '\../Model/Product.php';
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

    function hashPassword($password)
    {
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
            $result->execute([$order->getUserId(), $order->getTime(), $order->getPaymentMethod(), $order->getOrderType(), $order->getLocation(), $order->getStatus()]);
            return self::$connection->lastInsertId();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function addOrderProduct($orderproduct)
    {

        $query = "INSERT INTO orderproduct (OrderId,ProductId,Quantity,SellingPrice) VALUES (?, ?, ?,?)";

        try {
            echo $query;
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$orderproduct->getOrderId(), $orderproduct->getProductId(), $orderproduct->getQuantity(), $orderproduct->getSellingPrice()]);
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

        $query = 'SELECT * FROM `order` WHERE Status = ? ORDER BY Id DESC';

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

    // Hardware Part - dealing with associative array for now, fix it later

    public function getProductIdByBarcode($barcode)
    {
        $query = "SELECT * FROM Product WHERE Barcode = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$barcode]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($data) && isset($data[0]['Id'])) return $data[0];
            return -1;
        } catch (PDOException $e) {
            return -1;
        }
    }

    public function addToSession($userId, $basketId, $sessionId)
    {
        $query = "INSERT INTO session (userId, session_id, basketId) VALUES (?, ?, ?)";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$userId, $sessionId, $basketId]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return -1;
        }
    }

    public function getShoppingList($userId){

        $query = "SELECT * FROM shoppinglistitem WHERE UserId = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$userId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                return $data;
            }
            return [];
        } catch (PDOException) {
            return null;
        }
    }

    public function addItemToShoppingList($userId, $text)
    {
        $query = "INSERT INTO shoppinglistitem (userId, description) VALUES (?, ?)";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$userId, $text]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return -1;
        }
    }

    public function getSessionId($basketId)
    {
        $query  = "Select session_id FROM session WHERE basketId = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$basketId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($data) > 0) {
                return $data[0]['session_id'];
            }
            return "";
        } catch (PDOException) {
            return null;
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



    ///////////////////// Amir's Part ///////////////////////////////



    public function getAllUsers()
    {
        $query = "SELECT * FROM User WHERE Role = 'Customer'";
        try {
            self::$connection = self::createConnection();
            $stmt = self::$connection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    public function getAllCategories()
    {
        $sql = "SELECT id, name FROM category";
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($productId)
    {
        $query = "SELECT * FROM Product WHERE Id = ?";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);
            $result->execute([$productId]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            if (!$data || count($data) === 0) {
                return null; // No product found
            }

            $tempProduct = new Product(
                $data[0]['Name'],
                $data[0]['Price'],
                $data[0]['Stock'],
                $data[0]['Description'],
                $data[0]['Origin'],
                $data[0]['Barcode'],
                $data[0]['ImageUrl'],
                $data[0]['Weight']
            );
            $tempProduct->setId($data[0]['Id']);
            $tempProduct->setActive($data[0]['Active']);
            return $tempProduct;
        } catch (PDOException $e) {

            return null;
        }
    }


    public function getProductsName()
    {
        $query = "SELECT id, name FROM Product WHERE active = 1";

        try {

            self::$connection = self::createConnection();
            $stmt = self::$connection->prepare($query);
            $stmt->execute();

            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Store only the id and name
                $products[] = [
                    'id' => $row['id'],
                    'name' => $row['name']
                ];
            }


            return $products;
        } catch (PDOException $e) {

            return [];
        }
    }


    public function getAllProducts()
    {
        $query = "SELECT * FROM Product";
        try {
            self::$connection = self::createConnection();
            $stmt = self::$connection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getProductsBySearch($search)
    {

        $query = "SELECT * FROM Product WHERE Name LIKE :search OR Description LIKE :search";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);

            $searchTerm = "%" . $search . "%";
            $result->bindParam(':search', $searchTerm, PDO::PARAM_STR);

            $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);


            if (count($data) > 0) {

                return $data;
            } else {
                // No results found, return an empty array
                return [];
            }
        } catch (PDOException $e) {

            return null;
        }
    }


    public function getProductsByFilter($category, $price)
    {
        $query = "SELECT p.* FROM Product p JOIN Category c ON p.categoryId = c.id WHERE 1=1";
        if (!empty($category)) {
            $query .= " AND p.categoryId = :category";
        }

        if (!empty($price)) {
            if ($price == 'lowest') {
                $query .= " ORDER BY p.price ASC";
            } elseif ($price == 'highest') {
                $query .= " ORDER BY p.price DESC";
            } elseif ($price == '20-50') {
                $query .= " AND p.price BETWEEN 20 AND 50";
            }
        }
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        if (!empty($category)) {
            $stmt->bindValue(':category', $category);
        }
        $stmt->execute();

        // Return associative arrays
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // when the user types , they get suggestions
    public function searchSuggestions()
    {

        if (!isset($_GET['query'])) {
            echo json_encode([]);
            return;
        }

        $query = $_GET['query'];

        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare("SELECT Name FROM Product WHERE Name LIKE ? LIMIT 5");
        $stmt->execute(["%" . $query . "%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $results;
    }


    public function getProductsBySearchAndFilter($search = '', $categoryID = '', $price = '')
    {
        $query = "SELECT * FROM Product WHERE 1=1";
        $params = [];

        // Search condition
        if (!empty($search)) {
            $query .= " AND (Name LIKE :search OR Description LIKE :search)";
            $params[':search'] = "%" . $search . "%";
        }

        // Filter by CategoryID (foreign key)
        if (!empty($categoryID)) {
            $query .= " AND CategoryID = :categoryID";
            $params[':categoryID'] = $categoryID;
        }

        // Filter by Price
        if (!empty($price)) {
            if ($price == 'lowest') {
                $query .= " ORDER BY Price ASC";
            } elseif ($price == 'highest') {
                $query .= " ORDER BY Price DESC";
            } elseif ($price == '20-50') {
                $query .= " AND Price BETWEEN 20 AND 50";
            }
        }

        try {
            self::$connection = self::createConnection();
            $stmt = self::$connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    // Add Product

    public function addProduct($product)
    {
        $query = "INSERT INTO Product (Name, Price, Stock, Description, Origin, Barcode, ImageUrl, Weight, Active) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            self::$connection = self::createConnection();
            $result = self::$connection->prepare($query);

            $result->execute([
                $product->getName(),
                $product->getPrice(),
                $product->getStock(),
                $product->getDescription(),
                $product->getOrigin(),
                $product->getBarcode(),
                $product->getImageUrl(),
                $product->getWeight(),
                $product->isActive()
            ]);

            if ($result->rowCount() > 0) {
                return "Product added successfully!";
            } else {
                return "Product insertion failed!";
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }


    // Update the Product

    public function updateProduct($product)
    {
        $query = "UPDATE Product SET 
                Name = ?, Price = ?, Stock = ?, Description = ?, Origin = ?, 
                Barcode = ?, ImageUrl = ?, Weight = ?, Active = ? 
              WHERE Id = ?";

        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([
            $product->getName(),
            $product->getPrice(),
            $product->getStock(),
            $product->getDescription(),
            $product->getOrigin(),
            $product->getBarcode(),
            $product->getImageUrl(),
            $product->getWeight(),
            $product->isActive(),
            $product->getId()
        ]);
    }


    // delete product

    public function deleteProduct($id)
    {
        $query = "DELETE FROM Product WHERE Id = ?";
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([$id]);
    }

    // Update user 
    public function updateUser($user)
    {
        $query = "UPDATE User SET 
                Name = ?, Email = ?, Phone = ?, Password = ?, Role = ?, 
                Balance = ?, Active = ? WHERE Id = ?";


        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getPassword(),
            $user->getRole(),
            $user->getBalance(),
            $user->isActive(),
            $user->getId()
        ]);
    }

    // delete user

    public function deleteUser($id)
    {
        $query = "DELETE FROM User WHERE Id = ?";
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getUserShoppingListItems($id)
    {
        $query = "SELECT Description FROM shoppinglistitem WHERE UserId = ?";
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([$id]);
    }

    public function deleteShoppingList($id)
    {
        $query = "DELETE FROM shoppinglistitem WHERE UserId = ?";
        self::$connection = self::createConnection();
        $stmt = self::$connection->prepare($query);
        return $stmt->execute([$id]);
    }


    public function getBestsellingProducts()
    {
        $query = " SELECT p.Id AS product_id, p.Name, p.Price, p.ImageUrl, p.Description, 
                   SUM(op.quantity) AS total_quantity_sold
            FROM product p
            JOIN orderProduct op ON p.Id = op.productId
            JOIN `order` o ON op.orderId = o.Id
            WHERE op.quantity > 0
            GROUP BY p.Id
            HAVING total_quantity_sold > 0
            ORDER BY total_quantity_sold DESC
            LIMIT 15";

        try {
            self::$connection = self::createConnection();
            $stmt = self::$connection->prepare($query);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) {
                echo "No rows found"; // Debugging
            }

            $products = [];
            foreach ($rows as $row) {
                $products[] = [
                    'id' => $row['product_id'],
                    'name' => $row['Name'],
                    'price' => $row['Price'],
                    'img' => $row['ImageUrl'],
                    'description' => $row['Description'],
                    'total_quantity_sold' => $row['total_quantity_sold']
                ];
            }

            return $products;
        } catch (Exception $e) {
            return [];
        }
    }





    
}
