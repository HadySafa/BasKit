<?php

require_once './Backend/Controller/databaseAccess.php';
require_once './Backend/Model/User.php';

class Controller
{

    private $databaseAccess;

    public function __construct()
    {
        $this->databaseAccess = new databaseAccess();
    }

    public function addUser($submittedInfo)
    {
        $tempUser = new User($submittedInfo['Name'], $submittedInfo['Email'], $submittedInfo['Phone'], 'Customer', $submittedInfo['Password'], 0.00);
        $rowCount = $this->databaseAccess->addUser($tempUser);
        if ($rowCount > 0) header('Location: ./index.php');
        else header('Location: ./Register.php');
    }

    public function validateUser($submittedInfo)
    {
        $tempUser = $this->databaseAccess->getUserByEmail($submittedInfo['Email']);
        if ($tempUser) {
            if (password_verify($submittedInfo['Password'], $tempUser->getPassword())) header('Location: ./index.php');
            else header('Location: ./Login.php');
        }
    }
}
