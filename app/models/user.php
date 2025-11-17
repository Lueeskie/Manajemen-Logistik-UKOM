<?php

require_once __DIR__ . '/../../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user;
        }
        return false;
    }

    public function register($username, $email, $password, $role = 'user') {
        $checkStmt = $this->db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            return false; 
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }
    
    public function getProfile($userId) {
        $stmt = $this->db->prepare("SELECT id, username, email, telepon, alamat, role, foto FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function updateProfile($userId, $data) {
        try {
            $fields = [
                'username = :username',
                'telepon = :telepon',
                'alamat = :alamat'
            ];

            if (array_key_exists('foto', $data)) {
                $fields[] = 'foto = :foto';
            }

            $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':telepon', $data['telepon']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':id', $userId);

            if (array_key_exists('foto', $data)) {
                $stmt->bindParam(':foto', $data['foto']);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || $currentPassword !== $user['password']) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE users SET password = :new_password WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':new_password', $newPassword);
        return $stmt->execute();
    }
}
