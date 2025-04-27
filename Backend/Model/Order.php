<?php

// This is done by Hady

class Order
{

    private $id;
    private $userId;
    private $time;
    private $paymentMethod;
    private $orderType;
    private $location;
    private $status;

    // constructor
    public function __construct($userId, $time, $paymentMethod, $orderType, $location, $status)
    {
        $this->userId = $userId;
        $this->time = $time;
        $this->paymentMethod = $paymentMethod;
        $this->orderType = $orderType;
        $this->location = $location;
        $this->status = $status;
    }

    // getters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function getTime() {
        return $this->time;
    }
    
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    
    public function getOrderType() {
        return $this->orderType;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function getStatus() {
        return $this->status;
    }    
    

    // setters 
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function setTime($time) {
        $this->time = $time;
    }
    
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    
    public function setOrderType($orderType) {
        $this->orderType = $orderType;
    }
    
    public function setLocation($location) {
        $this->location = $location;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function toArray(){
        return [ 'Id' => $this->id, 'UserId' => $this->userId, 'Timestamp' => $this->time,'PaymentMethod' => $this->paymentMethod,'OrderType' => $this->orderType,'Location'=>$this->location,'Status' => $this->status];
    }
    

}
