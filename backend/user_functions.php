<?php
require_once 'db_connection.php';

function authenticateUser($email, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    } catch (PDOException $e) {
        error_log('Database error in authenticateUser: ' . $e->getMessage());
        throw new Exception('Database error occurred');
    }
}

function generateJWTToken($userId) {
    // Implement JWT token generation here
    // For simplicity, we'll just return a dummy token
    return "dummy_jwt_token_" . $userId;
}

function generateRefreshToken($userId) {
    // Implement refresh token generation here
    // For simplicity, we'll just return a dummy token
    return "dummy_refresh_token_" . $userId;
}

function verifyJWTToken() {
    // Implement proper JWT verification here
    // For now, we'll just return a mock user ID
    return 1;
}

function getUserDashboardData($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception('User not found');
        }

        return [
            'userName' => $user['name'],
            'userAvatar' => 'https://example.com/avatar.jpg', // You might want to store this in the database
            'notificationCount' => getNotificationCount($userId)
        ];
    } catch (PDOException $e) {
        error_log('Database error in getUserDashboardData: ' . $e->getMessage());
        throw new Exception('Database error occurred');
    }
}

function getNotificationCount($userId) {
    // Implement this to get actual notification count from the database
    // For now, we'll return a random number
    return rand(0, 5);
}