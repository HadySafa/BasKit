<?php

class OrderProduct {
    private $orderId;
    private $productId;
    private $quantity;
    private $sellingPrice;

   
    public function __construct($orderId, $productId, $quantity, $sellingPrice) {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->sellingPrice = $sellingPrice;
    }

    
    public function getOrderId() {
        return $this->orderId;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getSellingPrice() {
        return $this->sellingPrice;
    }
}
?>