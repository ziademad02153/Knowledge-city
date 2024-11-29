<?php

// Add CORS and security headers
header('Access-Control-Allow-Origin: http://cc.localhost');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/CourseController.php';

// Rate limiting (simple implementation)
session_start();
$timeFrame = 60; // 1 minute
$maxRequests = 60; // maximum requests per minute

if (!isset($_SESSION['requests'])) {
    $_SESSION['requests'] = array();
}

// Clean old requests
$_SESSION['requests'] = array_filter($_SESSION['requests'], function($time) use ($timeFrame) {
    return $time > (time() - $timeFrame);
});

// Add current request
$_SESSION['requests'][] = time();

// Check if too many requests
if (count($_SESSION['requests']) > $maxRequests) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests. Please try again later.']);
    exit;
}

// Parse URL with validation
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL format']);
    exit;
}

$uri = explode('/', trim($uri, '/'));

// Route the request with improved validation
$controller = $uri[0] ?? '';
$id = $uri[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Validate ID if present
if ($id !== null) {
    if (!is_numeric($id) || $id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID format. Must be a positive number']);
        exit;
    }
    $id = (int)$id;
}

try {
    switch ($controller) {
        case 'categories':
            $categoryController = new CategoryController();
            if ($method === 'GET') {
                if ($id) {
                    $result = $categoryController->getById($id);
                    if (!$result) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Category not found']);
                        break;
                    }
                    echo json_encode($result);
                } else {
                    echo json_encode($categoryController->getAll());
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'courses':
            $courseController = new CourseController();
            if ($method === 'GET') {
                $categoryId = isset($_GET['category_id']) ? filter_var($_GET['category_id'], FILTER_VALIDATE_INT) : null;
                if (isset($_GET['category_id']) && $categoryId === false) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid category_id parameter']);
                    break;
                }
                
                if ($id) {
                    $result = $courseController->getById($id);
                    if (!$result) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Course not found']);
                        break;
                    }
                    echo json_encode($result);
                } else {
                    echo json_encode($courseController->getAll($categoryId));
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}