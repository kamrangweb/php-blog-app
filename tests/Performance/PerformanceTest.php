<?php

namespace Tests\Performance;

use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class PerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHomePageResponseTime(): void
    {
        $startTime = microtime(true);
        
        $response = $this->httpClient->get('/');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(500, $responseTime, "Home page should load in less than 500ms, took {$responseTime}ms");
    }

    public function testBlogIndexResponseTime(): void
    {
        $startTime = microtime(true);
        
        $response = $this->httpClient->get('/blog');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(1000, $responseTime, "Blog index should load in less than 1000ms, took {$responseTime}ms");
    }

    public function testConcurrentRequests(): void
    {
        $requests = function () {
            for ($i = 0; $i < 10; $i++) {
                yield new Request('GET', '/');
            }
        };

        $startTime = microtime(true);
        
        $pool = new Pool($this->httpClient, $requests(), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                $this->assertEquals(200, $response->getStatusCode());
            },
            'rejected' => function ($reason, $index) {
                $this->fail("Request $index failed: " . $reason->getMessage());
            },
        ]);

        $pool->promise()->wait();
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        
        $this->assertLessThan(2000, $totalTime, "10 concurrent requests should complete in less than 2000ms, took {$totalTime}ms");
    }

    public function testDatabaseQueryPerformance(): void
    {
        // Create test data
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        // Insert 100 test posts
        for ($i = 1; $i <= 100; $i++) {
            $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                             VALUES ('Performance Post $i', 'performance-post-$i', 'Content $i', 'Excerpt $i', $userId, $categoryId, 'published')");
        }
        
        $startTime = microtime(true);
        
        // Perform a complex query
        $stmt = $this->pdo->query("
            SELECT p.*, u.username, c.name as category_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'published' 
            ORDER BY p.created_at DESC 
            LIMIT 20
        ");
        $posts = $stmt->fetchAll();
        
        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;
        
        $this->assertCount(20, $posts);
        $this->assertLessThan(100, $queryTime, "Complex query should execute in less than 100ms, took {$queryTime}ms");
    }

    public function testMemoryUsage(): void
    {
        $initialMemory = memory_get_usage();
        
        // Perform memory-intensive operations
        $posts = [];
        for ($i = 0; $i < 1000; $i++) {
            $posts[] = [
                'id' => $i,
                'title' => "Post $i",
                'content' => str_repeat("Content for post $i. ", 100),
                'author' => "Author $i",
                'category' => "Category " . ($i % 10)
            ];
        }
        
        $peakMemory = memory_get_peak_usage();
        $memoryIncrease = $peakMemory - $initialMemory;
        
        // Memory usage should be reasonable (less than 50MB for this operation)
        $this->assertLessThan(50 * 1024 * 1024, $memoryIncrease, "Memory usage should be less than 50MB, used " . round($memoryIncrease / 1024 / 1024, 2) . "MB");
    }

    public function testApiResponseTime(): void
    {
        $startTime = microtime(true);
        
        $response = $this->httpClient->get('/api/posts');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(300, $responseTime, "API response should be faster than 300ms, took {$responseTime}ms");
    }

    public function testLoadTest(): void
    {
        $concurrentUsers = 20;
        $requestsPerUser = 5;
        $totalRequests = $concurrentUsers * $requestsPerUser;
        
        $successfulRequests = 0;
        $failedRequests = 0;
        $responseTimes = [];
        
        $requests = function () use ($requestsPerUser) {
            for ($i = 0; $i < $requestsPerUser; $i++) {
                yield new Request('GET', '/');
            }
        };
        
        $startTime = microtime(true);
        
        $pool = new Pool($this->httpClient, $requests(), [
            'concurrency' => $concurrentUsers,
            'fulfilled' => function ($response, $index) use (&$successfulRequests, &$responseTimes, $startTime) {
                $successfulRequests++;
                $responseTimes[] = (microtime(true) - $startTime) * 1000;
            },
            'rejected' => function ($reason, $index) use (&$failedRequests) {
                $failedRequests++;
            },
        ]);

        $pool->promise()->wait();
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        
        // Calculate statistics
        $avgResponseTime = array_sum($responseTimes) / count($responseTimes);
        $maxResponseTime = max($responseTimes);
        $minResponseTime = min($responseTimes);
        
        // Assertions
        $this->assertEquals($totalRequests, $successfulRequests + $failedRequests);
        $this->assertGreaterThan(0.9, $successfulRequests / $totalRequests, "Success rate should be above 90%");
        $this->assertLessThan(5000, $totalTime, "Load test should complete in less than 5 seconds");
        $this->assertLessThan(1000, $avgResponseTime, "Average response time should be less than 1000ms");
        
        // Log performance metrics
        echo "\nLoad Test Results:\n";
        echo "Total Requests: $totalRequests\n";
        echo "Successful: $successfulRequests\n";
        echo "Failed: $failedRequests\n";
        echo "Success Rate: " . round(($successfulRequests / $totalRequests) * 100, 2) . "%\n";
        echo "Total Time: " . round($totalTime, 2) . "ms\n";
        echo "Average Response Time: " . round($avgResponseTime, 2) . "ms\n";
        echo "Min Response Time: " . round($minResponseTime, 2) . "ms\n";
        echo "Max Response Time: " . round($maxResponseTime, 2) . "ms\n";
    }

    public function testStressTest(): void
    {
        $concurrentUsers = 50;
        $duration = 30; // seconds
        $startTime = time();
        
        $successfulRequests = 0;
        $failedRequests = 0;
        $responseTimes = [];
        
        while (time() - $startTime < $duration) {
            $requests = function () {
                for ($i = 0; $i < 10; $i++) {
                    yield new Request('GET', '/');
                }
            };
            
            $pool = new Pool($this->httpClient, $requests(), [
                'concurrency' => $concurrentUsers,
                'fulfilled' => function ($response, $index) use (&$successfulRequests, &$responseTimes) {
                    $successfulRequests++;
                    $responseTimes[] = microtime(true);
                },
                'rejected' => function ($reason, $index) use (&$failedRequests) {
                    $failedRequests++;
                },
            ]);

            $pool->promise()->wait();
            
            // Small delay to prevent overwhelming the server
            usleep(100000); // 100ms
        }
        
        $totalRequests = $successfulRequests + $failedRequests;
        $successRate = $successfulRequests / $totalRequests;
        
        $this->assertGreaterThan(0.8, $successRate, "Stress test success rate should be above 80%");
        $this->assertGreaterThan(100, $totalRequests, "Should handle at least 100 requests during stress test");
        
        echo "\nStress Test Results:\n";
        echo "Duration: {$duration} seconds\n";
        echo "Total Requests: $totalRequests\n";
        echo "Successful: $successfulRequests\n";
        echo "Failed: $failedRequests\n";
        echo "Success Rate: " . round($successRate * 100, 2) . "%\n";
        echo "Requests per second: " . round($totalRequests / $duration, 2) . "\n";
    }

    public function testDatabaseConnectionPool(): void
    {
        $connections = [];
        $startTime = microtime(true);
        
        // Create multiple database connections
        for ($i = 0; $i < 10; $i++) {
            $connections[] = new \PDO(
                "mysql:host=localhost;dbname=blog_test;charset=utf8mb4",
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        
        $connectionTime = (microtime(true) - $startTime) * 1000;
        
        // Test concurrent queries
        $startTime = microtime(true);
        $results = [];
        
        foreach ($connections as $pdo) {
            $results[] = $pdo->query("SELECT COUNT(*) as count FROM posts")->fetch();
        }
        
        $queryTime = (microtime(true) - $startTime) * 1000;
        
        // Clean up connections
        foreach ($connections as $pdo) {
            $pdo = null;
        }
        
        $this->assertLessThan(1000, $connectionTime, "Database connections should be established quickly");
        $this->assertLessThan(500, $queryTime, "Concurrent queries should execute quickly");
        $this->assertCount(10, $results);
    }
} 