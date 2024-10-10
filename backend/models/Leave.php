<?php
class Leave {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getLeaveBalance($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM leave_balance WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getLeaveHistory($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM leave_history WHERE user_id = ? ORDER BY start_date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function requestLeave($userId, $leaveType, $startDate, $endDate, $reason) {
        $stmt = $this->pdo->prepare("INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        return $stmt->execute([$userId, $leaveType, $startDate, $endDate, $reason]);
    }
}