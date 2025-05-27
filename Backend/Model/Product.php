<?php

class Product
{

    private $id;
    private $name;
    private $price;
    private $stock;
    private $description;
    private $origin;
    private $barcode;
    private $imageUrl;
    private $weight;
    private $active;

    // constructor
    public function __construct($name, $price, $stock, $description, $origin, $barcode, $imageUrl, $weight , $active = true)
    {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->description = $description;
        $this->origin = $origin;
        $this->barcode = $barcode;
        $this->imageUrl = $imageUrl;
        $this->weight = $weight;
        $this->active = $active;

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

    public function getPrice()
    {
        return $this->price;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getBarcode()
    {
        return $this->barcode;
    }
    public function getImageUrl()
    {
        return $this->imageUrl;
    }
    public function getWeight()
    {
        return $this->weight;
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

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setBarcode($barcode) {
        $this->barcode = $barcode;
    }

    public function setOrigin($origin) {
        $this->origin = $origin;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function setActive($active) {
        $this->active = $active;
    }

}