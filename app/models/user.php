<?php
require_once __DIR__ . '/../../config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nama_role 
            FROM users u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.email = :email AND u.password = :password
        ");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user['role'] = $user['nama_role'] ?? 'user';
            return $user;
        }
        return false;
    }

    public function register($username, $email, $password, $nama_role = 'user')
    {
        $check = $this->db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $check->execute([':username' => $username, ':email' => $email]);
        if ($check->fetch()) return false;

        $roleStmt = $this->db->prepare("SELECT id_role FROM roles WHERE nama_role = :nama_role");
        $roleStmt->execute([':nama_role' => $nama_role]);
        $id_role = $roleStmt->fetchColumn();

        if (!$id_role) {
            $roleStmt->execute([':nama_role' => 'user']);
            $id_role = $roleStmt->fetchColumn() ?: 4;
        }

        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, id_role) 
            VALUES (:username, :email, :password, :id_role)
        ");
        $stmt->execute([
            ':username' => $username,
            ':email'    => $email,
            ':password' => $password,
            ':id_role'  => $id_role
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getAllUsers()
    {
        $query = "
            SELECT 
                u.*, 
                r.nama_role AS role 
            FROM users u 
            LEFT JOIN roles r ON u.id_role = r.id_role 
            ORDER BY u.created_at ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as &$user) {
            $user['role'] = $user['role'] ?? 'user';
        }
        return $users;
    }

    public function getProfile($userId)
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.email, u.telepon, u.alamat, u.foto, r.nama_role 
            FROM users u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.id = :id
        ");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) $user['role'] = $user['nama_role'] ?? 'user';
        return $user ?: false;
    }

    public function updateProfile($userId, $data)
    {
        $fields = [
            'username = :username',
            'telepon = :telepon',
            'alamat = :alamat'
        ];
        $params = [
            ':username' => $data['username'],
            ':telepon'  => $data['telepon'],
            ':alamat'   => $data['alamat'],
            ':id'       => $userId
        ];

        if (!empty($data['foto'])) {
            $fields[] = 'foto = :foto';
            $params[':foto'] = $data['foto'];
        }

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $currentPassword !== $user['password']) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE users SET password = :new_password WHERE id = :id");
        $stmt->execute([
            ':new_password' => $newPassword,
            ':id' => $userId
        ]);
        return true;
    }

    public function getAllRoles()
    {
        $query = "SELECT id_role, nama_role FROM roles ORDER BY nama_role ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($userId, $data)
    {
        $fields = [];
        $params = [':id' => $userId];

        if (isset($data['username']) && !empty(trim($data['username']))) {
            $fields[] = 'username = :username';
            $params[':username'] = trim($data['username']);
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (isset($data['password'])) {
            $fields[] = 'password = :password';
            $params[':password'] = $data['password'];
        }
        if (isset($data['id_role'])) {
            $fields[] = 'id_role = :id_role';
            $params[':id_role'] = $data['id_role'];
        }
        if (isset($data['telepon'])) {
            $fields[] = 'telepon = :telepon';
            $params[':telepon'] = $data['telepon'];
        }
        if (isset($data['alamat'])) {
            $fields[] = 'alamat = :alamat';
            $params[':alamat'] = $data['alamat'];
        }

        if (empty($fields)) return false;

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
}
