<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Domain\Models\User;
use PDO;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserCreation(): void
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];

        $user = new User($this->pdo);
        $userId = $user->create($userData);

        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);

        // Verify user was created
        $createdUser = $user->findById($userId);
        $this->assertEquals($userData['username'], $createdUser['username']);
        $this->assertEquals($userData['email'], $createdUser['email']);
        $this->assertEquals($userData['role'], $createdUser['role']);
    }

    public function testUserValidation(): void
    {
        $user = new User($this->pdo);

        // Test invalid email
        $invalidData = [
            'username' => 'testuser',
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $user->create($invalidData);
    }

    public function testUserAuthentication(): void
    {
        $user = new User($this->pdo);
        
        // Create a test user
        $userData = [
            'username' => 'authuser',
            'email' => 'auth@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        $userId = $user->create($userData);
        
        // Test successful authentication
        $authenticatedUser = $user->authenticate('auth@example.com', 'password123');
        $this->assertNotNull($authenticatedUser);
        $this->assertEquals($userData['email'], $authenticatedUser['email']);
        
        // Test failed authentication
        $failedAuth = $user->authenticate('auth@example.com', 'wrongpassword');
        $this->assertNull($failedAuth);
    }

    public function testUserUpdate(): void
    {
        $user = new User($this->pdo);
        
        // Create a test user
        $userData = [
            'username' => 'updateuser',
            'email' => 'update@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        $userId = $user->create($userData);
        
        // Update user
        $updateData = [
            'username' => 'updateduser',
            'email' => 'updated@example.com'
        ];
        
        $result = $user->update($userId, $updateData);
        $this->assertTrue($result);
        
        // Verify update
        $updatedUser = $user->findById($userId);
        $this->assertEquals($updateData['username'], $updatedUser['username']);
        $this->assertEquals($updateData['email'], $updatedUser['email']);
    }

    public function testUserDeletion(): void
    {
        $user = new User($this->pdo);
        
        // Create a test user
        $userData = [
            'username' => 'deleteuser',
            'email' => 'delete@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        $userId = $user->create($userData);
        
        // Verify user exists
        $existingUser = $user->findById($userId);
        $this->assertNotNull($existingUser);
        
        // Delete user
        $result = $user->delete($userId);
        $this->assertTrue($result);
        
        // Verify user is deleted
        $deletedUser = $user->findById($userId);
        $this->assertNull($deletedUser);
    }

    public function testFindUserByEmail(): void
    {
        $user = new User($this->pdo);
        
        // Create a test user
        $userData = [
            'username' => 'emailuser',
            'email' => 'email@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        $user->create($userData);
        
        // Find by email
        $foundUser = $user->findByEmail('email@example.com');
        $this->assertNotNull($foundUser);
        $this->assertEquals($userData['email'], $foundUser['email']);
        
        // Test non-existent email
        $notFoundUser = $user->findByEmail('nonexistent@example.com');
        $this->assertNull($notFoundUser);
    }

    public function testPasswordHashing(): void
    {
        $user = new User($this->pdo);
        
        $userData = [
            'username' => 'hashuser',
            'email' => 'hash@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        $userId = $user->create($userData);
        $createdUser = $user->findById($userId);
        
        // Verify password is hashed
        $this->assertNotEquals('password123', $createdUser['password']);
        $this->assertTrue(password_verify('password123', $createdUser['password']));
    }
} 