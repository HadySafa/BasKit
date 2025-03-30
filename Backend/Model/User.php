<?php

// This is done by Hady

class User
{

    private $id;
    private $name;
    private $email;
    private $phone;
    private $role;
    private $active;
    private $balance;
    private $password;

    // constructor
    public function __construct($name, $email, $phone, $role, $password, $balance)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->role = $role;
        $this->balance = $balance;
        $this->active = true;
        $this->password = $password;
    }

    // getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function isActive()
    {
        return $this->active;
    }

    // setters 
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function setBalance($balance) {
        $this->balance = $balance;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

}
