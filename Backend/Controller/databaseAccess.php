<?php

define("host", "localhost");
define("databaseName", "market-name");
define("connectionString", "mysql:host=" . host . ";dbname=" . databaseName . "");
define("username", "root");
define("password", "");

class databaseAccess
{
    public static $connection = null;

    public function __construct()
    {
        self::$connection = self::createConnection();
    }

    // Hady's Part

    // check security!!
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

    public function addUser($user)
    {

        $query = "INSERT INTO user (Name, Email, Phone, Password ,Role,Balance,Active) VALUES (?, ?, ?, ?, ?,?,?)";

        try {
            $result = self::$connection->prepare($query);
            $result->execute([$user->getName(), $user->getEmail(), $user->getPhone(), $this->hashPassword($user->getPassword()), $user->getRole(), $user->getBalance(), $user->isActive()]);
            return $result->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT Password FROM User WHERE Email = ?";

        try {
            $result = self::$connection->prepare($query);
            $result->execute([$email]);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            $tempUser = new User($data[0]['Name'],$data[0]['Email'],$data[0]['Phone'],$data[0]['Role'],$data[0]['Password'],$data[0]['Balance']);
            $tempUser->setId($data[0]['Id']);
            $tempUser->setActive($data[0]['Active']);
            return $tempUser;
        } catch (PDOException) {
            return null;
        }
    }

    public function hashPassword($password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashedPassword;
    }

    // Amir's Part
}
