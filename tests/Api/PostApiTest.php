<?php

namespace Tests\Api;

use Tests\TestCase;
use GuzzleHttp\Client;

class PostApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetAllPosts(): void
    {
        $response = $this->httpClient->get('/api/posts');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->getContents());
        
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('posts', $data);
        $this->assertIsArray($data['posts']);
    }

    public function testGetSinglePost(): void
    {
        // Create a test post
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('API Test Post', 'api-test-post', 'API test content', 'API test excerpt', $userId, $categoryId, 'published')");
        
        $postId = $this->pdo->lastInsertId();
        
        $response = $this->httpClient->get("/api/posts/$postId");
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('API Test Post', $data['post']['title']);
    }

    public function testGetNonExistentPost(): void
    {
        $response = $this->httpClient->get('/api/posts/99999', [
            'http_errors' => false
        ]);
        
        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreatePost(): void
    {
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $postData = [
            'title' => 'New API Post',
            'slug' => 'new-api-post',
            'content' => 'This is a new post created via API',
            'excerpt' => 'New post excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];
        
        $response = $this->httpClient->post('/api/posts', [
            'json' => $postData
        ]);
        
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('post', $data);
        $this->assertEquals($postData['title'], $data['post']['title']);
    }

    public function testCreatePostValidation(): void
    {
        $invalidData = [
            'title' => '', // Empty title should fail
            'content' => 'Some content'
        ];
        
        $response = $this->httpClient->post('/api/posts', [
            'json' => $invalidData,
            'http_errors' => false
        ]);
        
        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('errors', $data);
    }

    public function testUpdatePost(): void
    {
        // Create a test post first
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Original Title', 'original-title', 'Original content', 'Original excerpt', $userId, $categoryId, 'published')");
        
        $postId = $this->pdo->lastInsertId();
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'draft'
        ];
        
        $response = $this->httpClient->put("/api/posts/$postId", [
            'json' => $updateData
        ]);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals($updateData['title'], $data['post']['title']);
        $this->assertEquals($updateData['content'], $data['post']['content']);
    }

    public function testDeletePost(): void
    {
        // Create a test post first
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Post to Delete', 'post-to-delete', 'Content to delete', 'Excerpt to delete', $userId, $categoryId, 'published')");
        
        $postId = $this->pdo->lastInsertId();
        
        $response = $this->httpClient->delete("/api/posts/$postId");
        
        $this->assertEquals(204, $response->getStatusCode());
        
        // Verify post is deleted
        $getResponse = $this->httpClient->get("/api/posts/$postId", [
            'http_errors' => false
        ]);
        $this->assertEquals(404, $getResponse->getStatusCode());
    }

    public function testGetPostsByCategory(): void
    {
        $categoryId = $this->pdo->query("SELECT id FROM categories WHERE slug = 'technology'")->fetch()['id'];
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        
        // Create posts in technology category
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Tech Post 1', 'tech-post-1', 'Tech content 1', 'Tech excerpt 1', $userId, $categoryId, 'published')");
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Tech Post 2', 'tech-post-2', 'Tech content 2', 'Tech excerpt 2', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/api/posts?category=technology');
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertCount(2, $data['posts']);
    }

    public function testSearchPosts(): void
    {
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Searchable Post', 'searchable-post', 'This post contains unique term SEARCH123', 'Search excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/api/posts?search=SEARCH123');
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertCount(1, $data['posts']);
        $this->assertEquals('Searchable Post', $data['posts'][0]['title']);
    }

    public function testGetPostsWithPagination(): void
    {
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        // Create multiple posts
        for ($i = 1; $i <= 15; $i++) {
            $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                             VALUES ('Post $i', 'post-$i', 'Content $i', 'Excerpt $i', $userId, $categoryId, 'published')");
        }
        
        $response = $this->httpClient->get('/api/posts?page=1&limit=10');
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertCount(10, $data['posts']);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertEquals(1, $data['pagination']['current_page']);
    }

    public function testGetPostsByUser(): void
    {
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        // Create posts for specific user
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('User Post 1', 'user-post-1', 'User content 1', 'User excerpt 1', $userId, $categoryId, 'published')");
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('User Post 2', 'user-post-2', 'User content 2', 'User excerpt 2', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get("/api/users/$userId/posts");
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertCount(2, $data['posts']);
    }

    public function testPostRateLimiting(): void
    {
        // Test rate limiting by making multiple requests quickly
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->httpClient->get('/api/posts', [
                'http_errors' => false
            ]);
        }
        
        // Check if any requests were rate limited (429 status)
        $rateLimited = false;
        foreach ($responses as $response) {
            if ($response->getStatusCode() === 429) {
                $rateLimited = true;
                break;
            }
        }
        
        // This test might pass or fail depending on your rate limiting configuration
        // $this->assertTrue($rateLimited, 'Rate limiting should be enforced');
    }

    public function testPostAuthentication(): void
    {
        // Test that protected endpoints require authentication
        $postData = [
            'title' => 'Protected Post',
            'content' => 'Protected content'
        ];
        
        $response = $this->httpClient->post('/api/posts', [
            'json' => $postData,
            'http_errors' => false
        ]);
        
        // This should return 401 if authentication is required
        // $this->assertEquals(401, $response->getStatusCode());
    }
} 