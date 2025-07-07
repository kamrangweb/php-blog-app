<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Domain\Models\Post;
use App\Domain\Models\User;
use App\Domain\Models\Category;

class PostTest extends TestCase
{
    protected Post $post;
    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->post = new Post($this->pdo);
        $this->user = new User($this->pdo);
        $this->category = new Category($this->pdo);
    }

    public function testPostCreation(): void
    {
        // Create test user and category first
        $userId = $this->user->create([
            'username' => 'postuser',
            'email' => 'post@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description'
        ]);

        $postData = [
            'title' => 'Test Post Title',
            'slug' => 'test-post-title',
            'content' => 'This is the content of the test post.',
            'excerpt' => 'Test post excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $postId = $this->post->create($postData);

        $this->assertIsInt($postId);
        $this->assertGreaterThan(0, $postId);

        // Verify post was created
        $createdPost = $this->post->findById($postId);
        $this->assertEquals($postData['title'], $createdPost['title']);
        $this->assertEquals($postData['content'], $createdPost['content']);
        $this->assertEquals($postData['status'], $createdPost['status']);
    }

    public function testPostValidation(): void
    {
        // Test post creation without required fields
        $invalidData = [
            'title' => '', // Empty title
            'content' => 'Some content'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->post->create($invalidData);
    }

    public function testPostUpdate(): void
    {
        // Create test user and category
        $userId = $this->user->create([
            'username' => 'updatepostuser',
            'email' => 'updatepost@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Update Category',
            'slug' => 'update-category',
            'description' => 'Update category description'
        ]);

        // Create initial post
        $postData = [
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'excerpt' => 'Original excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'draft'
        ];

        $postId = $this->post->create($postData);

        // Update post
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published'
        ];

        $result = $this->post->update($postId, $updateData);
        $this->assertTrue($result);

        // Verify update
        $updatedPost = $this->post->findById($postId);
        $this->assertEquals($updateData['title'], $updatedPost['title']);
        $this->assertEquals($updateData['content'], $updatedPost['content']);
        $this->assertEquals($updateData['status'], $updatedPost['status']);
    }

    public function testPostDeletion(): void
    {
        // Create test user and category
        $userId = $this->user->create([
            'username' => 'deletepostuser',
            'email' => 'deletepost@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Delete Category',
            'slug' => 'delete-category',
            'description' => 'Delete category description'
        ]);

        // Create post
        $postData = [
            'title' => 'Post to Delete',
            'slug' => 'post-to-delete',
            'content' => 'Content to delete',
            'excerpt' => 'Excerpt to delete',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $postId = $this->post->create($postData);

        // Verify post exists
        $existingPost = $this->post->findById($postId);
        $this->assertNotNull($existingPost);

        // Delete post
        $result = $this->post->delete($postId);
        $this->assertTrue($result);

        // Verify post is deleted
        $deletedPost = $this->post->findById($postId);
        $this->assertNull($deletedPost);
    }

    public function testFindPostsByUser(): void
    {
        // Create test user
        $userId = $this->user->create([
            'username' => 'findpostuser',
            'email' => 'findpost@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Find Category',
            'slug' => 'find-category',
            'description' => 'Find category description'
        ]);

        // Create multiple posts for the same user
        $postData1 = [
            'title' => 'First Post',
            'slug' => 'first-post',
            'content' => 'First post content',
            'excerpt' => 'First post excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $postData2 = [
            'title' => 'Second Post',
            'slug' => 'second-post',
            'content' => 'Second post content',
            'excerpt' => 'Second post excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $this->post->create($postData1);
        $this->post->create($postData2);

        // Find posts by user
        $userPosts = $this->post->findByUser($userId);
        $this->assertCount(2, $userPosts);
        $this->assertEquals($postData1['title'], $userPosts[0]['title']);
        $this->assertEquals($postData2['title'], $userPosts[1]['title']);
    }

    public function testFindPublishedPosts(): void
    {
        // Create test user and category
        $userId = $this->user->create([
            'username' => 'publisheduser',
            'email' => 'published@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Published Category',
            'slug' => 'published-category',
            'description' => 'Published category description'
        ]);

        // Create published and draft posts
        $publishedPost = [
            'title' => 'Published Post',
            'slug' => 'published-post',
            'content' => 'Published content',
            'excerpt' => 'Published excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $draftPost = [
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => 'Draft content',
            'excerpt' => 'Draft excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'draft'
        ];

        $this->post->create($publishedPost);
        $this->post->create($draftPost);

        // Find only published posts
        $publishedPosts = $this->post->findPublished();
        $this->assertCount(1, $publishedPosts);
        $this->assertEquals('Published Post', $publishedPosts[0]['title']);
    }

    public function testFindPostBySlug(): void
    {
        // Create test user and category
        $userId = $this->user->create([
            'username' => 'sluguser',
            'email' => 'slug@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]);

        $categoryId = $this->category->create([
            'name' => 'Slug Category',
            'slug' => 'slug-category',
            'description' => 'Slug category description'
        ]);

        // Create post
        $postData = [
            'title' => 'Slug Test Post',
            'slug' => 'slug-test-post',
            'content' => 'Slug test content',
            'excerpt' => 'Slug test excerpt',
            'user_id' => $userId,
            'category_id' => $categoryId,
            'status' => 'published'
        ];

        $this->post->create($postData);

        // Find by slug
        $foundPost = $this->post->findBySlug('slug-test-post');
        $this->assertNotNull($foundPost);
        $this->assertEquals($postData['title'], $foundPost['title']);

        // Test non-existent slug
        $notFoundPost = $this->post->findBySlug('non-existent-slug');
        $this->assertNull($notFoundPost);
    }
} 