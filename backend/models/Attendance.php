<?php
class Attendance {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getMonthlyAttendance($userId, $year, $month) {
        $stmt = $this->pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND YEAR(date) = ? AND MONTH(date) = ?");
        $stmt->execute([$userId, $year, $month]);
        return $stmt->fetchAll();
    }

    public function recordAttendance($userId, $date, $checkIn, $checkOut) {
        $stmt = $this->pdo->prepare("INSERT INTO attendance (user_id, date, check_in, check_out) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $date, $checkIn, $checkOut]);
    }
}