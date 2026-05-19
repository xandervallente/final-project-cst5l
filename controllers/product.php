<?php

class ProductController {
    private $conn;

    public function __construct($server, $username, $password, $dbname) {
        $this->conn = new mysqli($server, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Get all products
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM products ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get a single product by ID
    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Add a new product
    public function add($name, $description, $price, $quantity) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (name, description, price, quantity) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdi", $name, $description, $price, $quantity);
        return $stmt->execute();
    }

    // Edit an existing product
    public function edit($id, $name, $description, $price, $quantity) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?"
        );
        $stmt->bind_param("ssdii", $name, $description, $price, $quantity, $id);
        return $stmt->execute();
    }

    // Delete a product
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Adjust stock quantity — add or subtract
    public function adjustStock($id, $amount, $action) {
        if ($action === "add") {
            $stmt = $this->conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        } else {
            // Prevent going below 0
            $stmt = $this->conn->prepare("UPDATE products SET quantity = GREATEST(0, quantity - ?) WHERE id = ?");
        }
        $stmt->bind_param("ii", $amount, $id);
        return $stmt->execute();
    }

    // Search products by name or description, with optional stock filter
    public function search($keyword, $filter = "all") {
        $keyword = "%" . $keyword . "%";

        if ($filter === "in_stock") {
            $stmt = $this->conn->prepare(
                "SELECT * FROM products WHERE (name LIKE ? OR description LIKE ?) AND quantity > 0 ORDER BY id DESC"
            );
            $stmt->bind_param("ss", $keyword, $keyword);
        } elseif ($filter === "out_of_stock") {
            $stmt = $this->conn->prepare(
                "SELECT * FROM products WHERE (name LIKE ? OR description LIKE ?) AND quantity = 0 ORDER BY id DESC"
            );
            $stmt->bind_param("ss", $keyword, $keyword);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC"
            );
            $stmt->bind_param("ss", $keyword, $keyword);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get summary stats for dashboard
    public function getStats() {
        $result = $this->conn->query("
            SELECT
                COUNT(*) AS total_products,
                SUM(quantity) AS total_items,
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock,
                SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) AS in_stock,
                SUM(price * quantity) AS total_value
            FROM products
        ");
        return $result->fetch_assoc();
    }

    // Get low stock products (quantity <= threshold)
    public function getLowStock($threshold = 5) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM products WHERE quantity > 0 AND quantity <= ? ORDER BY quantity ASC"
        );
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get the most recently added products
    public function getRecent($limit = 5) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM products ORDER BY id DESC LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
