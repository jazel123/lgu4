<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';

class UserController {
    private $pdo;
    private $userModel;
    private $secretKey = 'your_secret_key_here'; // Replace with a secure secret key

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function login($email, $password) {
        try {
            $user = $this->userModel->login($email, $password);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            return [
                'success' => true,
                'token' => $this->generateAccessToken($user['id']),
                'refreshToken' => $this->generateRefreshToken($user['id']),
                'user' => [
                    'id' => $user['id'],
                    'firstName' => $user['firstName'],
                    'lastName' => $user['lastName'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during login'
            ];
        }
    }

    public function refreshToken($refreshToken) {
        try {
            $decoded = JWT::decode($refreshToken, $this->secretKey);
            $userId = $decoded['user_id'];
            
            // Check if the refresh token is valid in the database
            if (!$this->userModel->isRefreshTokenValid($userId, $refreshToken)) {
                return ['success' => false, 'message' => 'Invalid refresh token'];
            }

            $newAccessToken = $this->generateAccessToken($userId);
            $newRefreshToken = $this->generateRefreshToken($userId);

            // Update the refresh token in the database
            $this->userModel->updateRefreshToken($userId, $newRefreshToken);

            return [
                'success' => true,
                'token' => $newAccessToken,
                'refreshToken' => $newRefreshToken
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid refresh token'];
        }
    }

    private function verifyRefreshToken($refreshToken) {
        // Implement your refresh token verification logic
        // Return user ID if valid, false otherwise
        return $this->userModel->verifyRefreshToken($refreshToken);
    }

    private function generateAccessToken($userId) {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 hour expiration

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userId
        ];

        return JWT::encode($payload, $this->secretKey);
    }

    private function generateRefreshToken($userId) {
        $issuedAt = time();
        $expire = $issuedAt + (30 * 24 * 60 * 60); // 30 days expiration

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userId
        ];

        return JWT::encode($payload, $this->secretKey);
    }

    public function verifyToken($token) {
        try {
            $tokenParts = explode('.', $token);
            if (count($tokenParts) != 3) {
                throw new Exception("Invalid token format");
            }

            $payload = json_decode(base64_decode($tokenParts[1]), true);
            if (!$payload || !isset($payload['user_id']) || !isset($payload['exp'])) {
                throw new Exception("Invalid token payload");
            }

            if ($payload['exp'] < time()) {
                throw new Exception("Token has expired");
            }

            // Here you should add a check to verify the token signature
            // For simplicity, we're skipping this step, but in a production environment,
            // you should implement proper signature verification

            return $payload['user_id'];
        } catch (Exception $e) {
            error_log("Token verification failed: " . $e->getMessage());
            throw new Exception("Invalid token: " . $e->getMessage());
        }
    }

    public function register($userData) {
        error_log("UserController: Registering user with data: " . json_encode($userData));
        $result = $this->userModel->register($userData);
        error_log("UserController: Registration result: " . json_encode($result));
        return $result;
    }

    public function getUserInfo($userId) {
        return $this->userModel->getUserInfo($userId);
    }

    public function getUserFromToken($token) {
        $userId = $this->decodeToken($token);
        if ($userId) {
            return $this->userModel->getUserById($userId);
        }
        return null;
    }

    public function getUserProfile($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("User not found for ID: " . $userId);
            }

            // Remove sensitive information
            unset($user['password']);

            return [
                'success' => true,
                'user' => $user
            ];
        } catch (Exception $e) {
            error_log('Error in getUserProfile: ' . $e->getMessage());
            throw $e;
        }
    }

    private function decodeToken($token) {
        // Implement your token decoding logic here
        // This is a simple example and should be replaced with a more secure method
        return hexdec(substr($token, 0, 8));
    }

    public function getUserDashboardData($userId) {
        header("Access-Control-Allow-Origin: http://localhost:8080");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");

        // Fetch user data from the database
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // In a real application, you would fetch this data from your database
        return [
            'success' => true,
            'userName' => $user['firstName'] . ' ' . $user['lastName'],
            'leaveBalance' => 10, // Replace with actual data
            'hoursWorked' => 160, // Replace with actual data
            'absences' => 2, // Replace with actual data
            'recentActivities' => [
                ['id' => 1, 'date' => '2023-05-01', 'description' => 'Submitted leave request'],
                ['id' => 2, 'date' => '2023-04-28', 'description' => 'Completed project milestone'],
            ],
            'events' => [
                ['id' => 1, 'date' => '2023-05-15', 'title' => 'Team building event'],
                ['id' => 2, 'date' => '2023-05-20', 'title' => 'Quarterly review'],
            ],
        ];
    }
}