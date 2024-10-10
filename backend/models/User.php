<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT id, firstName, lastName, email, role, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return false;
    }

    public function register($userData) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$userData['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (firstName, middleName, lastName, birthdate, employeeId, mobile, department, role, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $userData['firstName'],
                $userData['middleName'] ?? null,
                $userData['lastName'],
                $userData['birthdate'],
                $userData['employeeId'],
                $userData['mobile'],
                $userData['department'],
                $userData['role'],
                $userData['email'],
                $hashedPassword
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'User registered successfully'];
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("User model: Registration failed: " . json_encode($errorInfo));
                return ['success' => false, 'message' => 'Registration failed: ' . $errorInfo[2]];
            }
        } catch (\PDOException $e) {
            error_log("User model: Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred during registration: ' . $e->getMessage()];
        }
    }

    public function getUserInfo($userId) {
        $stmt = $this->pdo->prepare("SELECT id, firstName, lastName, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("getUserById query result: " . json_encode($user));
        return $user;
    }

    public function isRefreshTokenValid($userId, $refreshToken) {
        $stmt = $this->pdo->prepare("SELECT refresh_token FROM user_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['refresh_token'] === $refreshToken;
    }

    public function updateRefreshToken($userId, $newRefreshToken) {
        $stmt = $this->pdo->prepare("INSERT INTO user_tokens (user_id, refresh_token) VALUES (?, ?) ON DUPLICATE KEY UPDATE refresh_token = ?");
        return $stmt->execute([$userId, $newRefreshToken, $newRefreshToken]);
    }

    public function getUserDashboardData($userId) {
        $query = "SELECT first_name, last_name, avatar_url, 
                  (SELECT COUNT(*) FROM notifications WHERE user_id = ?) as notification_count 
                  FROM " . $this->table . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $userId);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
}