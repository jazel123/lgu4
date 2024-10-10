<?php
require_once __DIR__ . '/../models/Attendance.php';

class AttendanceController {
    private $attendanceModel;

    public function __construct($pdo) {
        $this->attendanceModel = new Attendance($pdo);
    }

    public function getMonthlyAttendance($userId, $year, $month) {
        return $this->attendanceModel->getMonthlyAttendance($userId, $year, $month);
    }

    public function recordAttendance($userId, $date, $checkIn, $checkOut) {
        return $this->attendanceModel->recordAttendance($userId, $date, $checkIn, $checkOut);
    }
}