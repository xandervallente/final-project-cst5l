<?php
// This mimics the table "products" in your database
class Product {
    // Properties
    public $id = "";
    public $name = "";
    public $description = "";
    public $price = "";
    public $quantity = "";
    public $created_at = "";

    function __construct($name, $description, $price, $quantity, $created_at = "", $id = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->created_at = $created_at;
    }
}
