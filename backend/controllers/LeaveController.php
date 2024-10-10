<?php
require_once __DIR__ . '/../models/Leave.php';

class LeaveController {
    private $leaveModel;

    public function __construct($pdo) {
        $this->leaveModel = new Leave($pdo);
    }

    public function getLeaveBalance($userId) {
        return $this->leaveModel->getLeaveBalance($userId);
    }

    public function getLeaveHistory($userId) {
        return $this->leaveModel->getLeaveHistory($userId);
    }

    public function requestLeave($userId, $leaveType, $startDate, $endDate, $reason) {
        return $this->leaveModel->requestLeave($userId, $leaveType, $startDate, $endDate, $reason);
    }
}