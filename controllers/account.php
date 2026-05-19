<?php

class AccountController {
    private $conn;

    public function __construct($server, $username, $password, $dbname) {
        $this->conn = new mysqli($server, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Log in a user — returns true on success, false if credentials are wrong
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return false;
        }

        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (!password_verify($password, $hashed_password)) {
            return false;
        }

        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        return true;
    }

    // Register a new user — returns true on success, false if username already exists
    public function register($username, $password) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return false; // username already taken
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed);
        return $stmt->execute();
    }

    // Log out the current user
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /");
        exit();
    }
}
