<?php

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserDashboardData($userId) {
        // Fetch user data from the database
        $stmt = $this->pdo->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User not found");
        }

        // You can add more data here as needed
        return [
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'userAvatar' => '', // Add logic to fetch user avatar
            'notificationCount' => 0 // Add logic to fetch notification count
        ];
    }
}
