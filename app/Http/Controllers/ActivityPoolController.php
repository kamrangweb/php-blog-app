<?php

namespace App\Http\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Domain\Models\ActivityPool;
use App\Http\Request;
use App\Domain\Database\DBConnection;

class ActivityPoolController extends Controller
{
    private $activityPool;

    public function __construct()
    {
        $db = new DBConnection(DB_HOST, DB_NAME, DB_USER, DB_PWD);
        parent::__construct($db);
        $this->activityPool = new ActivityPool();
    }

    public function trackContent()
    {
        $request = new Request([
            'GET' => $_GET,
            'POST' => $_POST
        ]);
        $data = $request->getJsonData();

        // For testing: set a test user ID if not set
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $postId = $data['post_id'] ?? null;
        $content = $data['content'] ?? '';
        $wordCount = $data['word_count'] ?? 0;

        try {
            $result = $this->activityPool->trackContentVolume($userId, $postId, $content, $wordCount);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to track content volume',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            exit;
        }
    }

    public function getNotifications()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];

        try {
            $notifications = $this->activityPool->getNotifications($userId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to get notifications',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function subscribe()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];

        // Set headers for Server-Sent Events
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');

        // Send initial connection message
        echo "data: " . json_encode(['type' => 'connected', 'user_id' => $userId]) . "\n\n";
        ob_flush();
        flush();

        // Start a simple Kafka consumer loop for SSE
        $config = \Kafka\ConsumerConfig::getInstance();
        $config->setMetadataBrokerList('localhost:9092');
        $config->setGroupId('blog_activity_pool_group');
        $config->setBrokerVersion('1.0.0');
        $config->setTopics(['blog_activity_pool']);
        $config->setOffsetReset('latest');

        $consumer = new \Kafka\Consumer();
        $consumer->start(function($topic, $part, $message) use ($userId) {
            $data = json_decode($message['message']['value'], true);
            if (isset($data['user_id']) && $data['user_id'] == $userId) {
                echo "data: " . json_encode([
                    'id' => uniqid(),
                    'type' => $data['type'],
                    'message' => $data['notification_level'] === 'warning'
                        ? "⚠️ WARNING: Your blog post has reached {$data['word_count']} words! This is quite lengthy. Consider breaking it into multiple posts."
                        : ($data['notification_level'] === 'info'
                            ? "ℹ️ INFO: Your blog post has reached {$data['word_count']} words. Good content length!"
                            : "📝 Your blog post currently has {$data['word_count']} words."),
                    'level' => $data['notification_level'],
                    'word_count' => $data['word_count'],
                    'timestamp' => $data['timestamp']
                ]) . "\n\n";
                ob_flush();
                flush();
            }
        });
    }
} 