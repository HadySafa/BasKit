<?php

class Feedback {

    private $id;
    private $userId;
    private $description;
    private $published;
    private $timestamp;

    // Constructor
    public function __construct($userId, $description, $published, $timestamp) {
        $this->userId = $userId;
        $this->description = $description;
        $this->published = $published;
        $this->timestamp = $timestamp;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isPublished() {
        return $this->published;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setPublished($published) {
        $this->published = $published;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
}

?>
