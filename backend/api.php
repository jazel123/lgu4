<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Custom error handler to return JSON
function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'message' => 'Internal Server Error',
        'error' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit;
}

set_error_handler("jsonErrorHandler");

// Enable CORS for all requests
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// For non-OPTIONS requests, set the content type to JSON
if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    header('Content-Type: application/json');
}

// Log all requests for debugging
$requestLog = date('Y-m-d H:i:s') . ' - ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n";
file_put_contents('request_log.txt', $requestLog, FILE_APPEND);

// Start the session
session_start();

// Include necessary files
require_once 'db_connection.php';
require_once 'user_functions.php';

// Rest of your API code starts here
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Handle login request
if ($uri[1] === 'api' && $uri[2] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }

        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }

        $user = authenticateUser($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $token = generateJWTToken($user['id']);
            $refreshToken = generateRefreshToken($user['id']);

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'refreshToken' => $refreshToken,
                'user' => [
                    'id' => $user['id'] ?? null,
                    'name' => $user['name'] ?? $user['firstName'] ?? $user['first_name'] ?? 'Unknown',
                    'email' => $user['email'] ?? '',
                    'role' => $user['role'] ?? 'user'
                ]
            ]);
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal Server Error: ' . $e->getMessage()
        ]);
    }
    exit();
}

// Handle user dashboard data request
if ($uri[1] === 'api' && $uri[2] === 'user-dashboard-data' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $userId = verifyJWTToken();
        if (!$userId) {
            throw new Exception('Unauthorized');
        }

        $dashboardController = new DashboardController($pdo);
        $dashboardData = $dashboardController->getUserDashboardData($userId);

        echo json_encode([
            'success' => true,
            'data' => $dashboardData
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
    exit();
}

// If no route matches, return 404
http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Not Found']);

// Add this new route handler after your existing routes
if ($uri[1] === 'api' && $uri[2] === 'overtime-request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = verifyJWTToken();
        if (!$userId) {
            throw new Exception('Unauthorized');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $result = handleOvertimeRequest($userId, $input);

        echo json_encode([
            'success' => true,
            'message' => 'Overtime request submitted successfully',
            'data' => $result
        ]);
    } catch (Exception $e) {
        http_response_code($e->getMessage() === 'Unauthorized' ? 401 : 500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// Add this route handler
if ($uri[1] === 'user-dashboard-data' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $userId = verifyJWTToken();
        if (!$userId) {
            throw new Exception('Unauthorized');
        }

        $dashboardController = new DashboardController($pdo);
        $dashboardData = $dashboardController->getUserDashboardData($userId);

        echo json_encode([
            'success' => true,
            'data' => $dashboardData
        ]);
    } catch (Exception $e) {
        http_response_code($e->getMessage() === 'Unauthorized' ? 401 : 500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}