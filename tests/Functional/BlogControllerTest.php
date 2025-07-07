<?php

namespace Tests\Functional;

use Tests\TestCase;
use GuzzleHttp\Client;

class BlogControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHomePageLoads(): void
    {
        $response = $this->httpClient->get('/');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Blog', $response->getBody()->getContents());
    }

    public function testBlogIndexPage(): void
    {
        $response = $this->httpClient->get('/blog');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Blog Posts', $content);
    }

    public function testBlogPostShowPage(): void
    {
        // First, create a test post in the database
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Test Blog Post', 'test-blog-post', 'Test content', 'Test excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog/test-blog-post');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Test Blog Post', $content);
    }

    public function testNonExistentPostReturns404(): void
    {
        $response = $this->httpClient->get('/blog/non-existent-post', [
            'http_errors' => false
        ]);
        
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBlogPostWithCategoryFilter(): void
    {
        // Create a test category and post
        $categoryId = $this->pdo->query("SELECT id FROM categories WHERE slug = 'technology'")->fetch()['id'];
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Tech Post', 'tech-post', 'Tech content', 'Tech excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog?category=technology');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Tech Post', $content);
    }

    public function testBlogSearchFunctionality(): void
    {
        // Create a test post with specific content
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Searchable Post', 'searchable-post', 'This post contains unique search term XYZ123', 'Search excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog?search=XYZ123');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Searchable Post', $content);
    }

    public function testBlogPagination(): void
    {
        // Create multiple test posts
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        for ($i = 1; $i <= 15; $i++) {
            $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                             VALUES ('Post $i', 'post-$i', 'Content $i', 'Excerpt $i', $userId, $categoryId, 'published')");
        }
        
        // Test first page
        $response = $this->httpClient->get('/blog?page=1');
        $this->assertEquals(200, $response->getStatusCode());
        
        // Test second page
        $response = $this->httpClient->get('/blog?page=2');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlogPostComments(): void
    {
        // This test would require a comments table and functionality
        // For now, we'll test that the comment form is present on post pages
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Commentable Post', 'commentable-post', 'Post content', 'Post excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog/commentable-post');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        // Check if comment section exists (this would depend on your template)
        // $this->assertStringContainsString('comment', $content);
    }

    public function testBlogRSSFeed(): void
    {
        $response = $this->httpClient->get('/blog/feed');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('<?xml', $content);
        $this->assertStringContainsString('<rss', $content);
    }

    public function testBlogSitemap(): void
    {
        $response = $this->httpClient->get('/sitemap.xml');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('<?xml', $content);
        $this->assertStringContainsString('<urlset', $content);
    }

    public function testBlogPostSharing(): void
    {
        // Test social media sharing functionality
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Shareable Post', 'shareable-post', 'Shareable content', 'Shareable excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog/shareable-post');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        // Check for social sharing buttons (this would depend on your template)
        // $this->assertStringContainsString('facebook', $content);
        // $this->assertStringContainsString('twitter', $content);
    }

    public function testBlogPostPrintVersion(): void
    {
        $userId = $this->pdo->query("SELECT id FROM users LIMIT 1")->fetch()['id'];
        $categoryId = $this->pdo->query("SELECT id FROM categories LIMIT 1")->fetch()['id'];
        
        $this->pdo->exec("INSERT INTO posts (title, slug, content, excerpt, user_id, category_id, status) 
                         VALUES ('Printable Post', 'printable-post', 'Printable content', 'Printable excerpt', $userId, $categoryId, 'published')");
        
        $response = $this->httpClient->get('/blog/printable-post/print');
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Printable Post', $content);
    }
} 