<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;
use GuzzleHttp\Client;
use Faker\Factory;

abstract class TestCase extends BaseTestCase
{
    protected PDO $pdo;
    protected Client $httpClient;
    protected $faker;
    protected string $baseUrl = 'http://localhost:8000';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->faker = Factory::create();
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'verify' => false
        ]);
        
        $this->setupDatabase();
    }

    protected function setupDatabase(): void
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_DATABASE'] ?? 'blog_test';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        // Create test database if it doesn't exist
        $this->createTestDatabase();
        
        // Run migrations
        $this->runMigrations();
        
        // Seed test data
        $this->seedTestData();
    }

    protected function createTestDatabase(): void
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $dbname = $_ENV['DB_DATABASE'] ?? 'blog_test';

        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    protected function runMigrations(): void
    {
        $migrations = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content TEXT NOT NULL,
                excerpt TEXT,
                featured_image VARCHAR(255),
                user_id INT NOT NULL,
                category_id INT,
                status ENUM('draft', 'published') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            )",
            
            "CREATE TABLE IF NOT EXISTS tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS post_tags (
                post_id INT NOT NULL,
                tag_id INT NOT NULL,
                PRIMARY KEY (post_id, tag_id),
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
            )"
        ];

        foreach ($migrations as $migration) {
            $this->pdo->exec($migration);
        }
    }

    protected function seedTestData(): void
    {
        // Create test user
        $this->pdo->exec("INSERT IGNORE INTO users (username, email, password, role) VALUES 
            ('testuser', 'test@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'user'),
            ('admin', 'admin@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')");

        // Create test category
        $this->pdo->exec("INSERT IGNORE INTO categories (name, slug, description) VALUES 
            ('Technology', 'technology', 'Technology related posts'),
            ('Lifestyle', 'lifestyle', 'Lifestyle related posts')");

        // Create test posts
        $this->pdo->exec("INSERT IGNORE INTO posts (title, slug, content, excerpt, user_id, category_id, status) VALUES 
            ('Test Post 1', 'test-post-1', 'This is test content for post 1', 'Test excerpt 1', 1, 1, 'published'),
            ('Test Post 2', 'test-post-2', 'This is test content for post 2', 'Test excerpt 2', 1, 2, 'published')");
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->pdo->exec("DELETE FROM post_tags");
        $this->pdo->exec("DELETE FROM posts");
        $this->pdo->exec("DELETE FROM tags");
        $this->pdo->exec("DELETE FROM categories");
        $this->pdo->exec("DELETE FROM users");
        
        parent::tearDown();
    }

    protected function assertResponseSuccess($response): void
    {
        $this->assertLessThan(400, $response->getStatusCode());
    }

    protected function assertResponseError($response): void
    {
        $this->assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    protected function assertJsonResponse($response, array $expectedData): void
    {
        $this->assertResponseSuccess($response);
        $this->assertJson($response->getBody()->getContents());
        
        $data = json_decode($response->getBody()->getContents(), true);
        foreach ($expectedData as $key => $value) {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($value, $data[$key]);
        }
    }
} 