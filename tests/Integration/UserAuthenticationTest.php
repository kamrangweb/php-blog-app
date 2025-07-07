<?php

namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class UserAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserRegistrationFlow(): void
    {
        $userData = [
            'username' => 'newuser' . time(),
            'email' => 'newuser' . time() . '@example.com',
            'password' => 'password123',
            'password_confirm' => 'password123'
        ];

        // Test registration form submission
        $response = $this->httpClient->post('/auth/register', [
            'form_params' => $userData
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        
        // Verify user was created in database
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$userData['email']]);
        $user = $stmt->fetch();
        
        $this->assertNotNull($user);
        $this->assertEquals($userData['username'], $user['username']);
        $this->assertEquals($userData['email'], $user['email']);
    }

    public function testUserLoginFlow(): void
    {
        // Create a test user first
        $userData = [
            'username' => 'logintestuser',
            'email' => 'logintest@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        // Test login
        $loginData = [
            'email' => 'logintest@example.com',
            'password' => 'password123'
        ];

        $response = $this->httpClient->post('/auth/login', [
            'form_params' => $loginData
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        
        // Check if session was created
        $content = $response->getBody()->getContents();
        $this->assertStringContainsString('Welcome', $content);
    }

    public function testUserLogoutFlow(): void
    {
        // First login
        $userData = [
            'username' => 'logouttestuser',
            'email' => 'logouttest@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        $loginData = [
            'email' => 'logouttest@example.com',
            'password' => 'password123'
        ];

        $this->httpClient->post('/auth/login', [
            'form_params' => $loginData
        ]);

        // Test logout
        $response = $this->httpClient->get('/auth/logout');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        // Verify user is logged out by trying to access protected page
        $protectedResponse = $this->httpClient->get('/admin/posts', [
            'http_errors' => false
        ]);
        
        $this->assertEquals(302, $protectedResponse->getStatusCode()); // Should redirect to login
    }

    public function testPasswordResetFlow(): void
    {
        // Create a test user
        $userData = [
            'username' => 'resetuser',
            'email' => 'reset@example.com',
            'password' => password_hash('oldpassword', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        // Request password reset
        $resetData = [
            'email' => 'reset@example.com'
        ];

        $response = $this->httpClient->post('/auth/forgot-password', [
            'form_params' => $resetData
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        
        // Verify reset token was created (this would depend on your implementation)
        // $stmt = $this->pdo->prepare("SELECT * FROM password_resets WHERE email = ?");
        // $stmt->execute([$userData['email']]);
        // $resetToken = $stmt->fetch();
        // $this->assertNotNull($resetToken);
    }

    public function testUserProfileUpdate(): void
    {
        // Create and login a user
        $userData = [
            'username' => 'profileuser',
            'email' => 'profile@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        $loginData = [
            'email' => 'profile@example.com',
            'password' => 'password123'
        ];

        $this->httpClient->post('/auth/login', [
            'form_params' => $loginData
        ]);

        // Update profile
        $updateData = [
            'username' => 'updatedprofileuser',
            'email' => 'updatedprofile@example.com'
        ];

        $response = $this->httpClient->post('/profile/update', [
            'form_params' => $updateData
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        
        // Verify profile was updated
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$updateData['email']]);
        $updatedUser = $stmt->fetch();
        
        $this->assertNotNull($updatedUser);
        $this->assertEquals($updateData['username'], $updatedUser['username']);
    }

    public function testAdminAccessControl(): void
    {
        // Create regular user
        $regularUser = [
            'username' => 'regularuser',
            'email' => 'regular@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$regularUser['username'], $regularUser['email'], $regularUser['password'], $regularUser['role']]);

        // Login as regular user
        $loginData = [
            'email' => 'regular@example.com',
            'password' => 'password123'
        ];

        $this->httpClient->post('/auth/login', [
            'form_params' => $loginData
        ]);

        // Try to access admin area
        $response = $this->httpClient->get('/admin/posts', [
            'http_errors' => false
        ]);

        $this->assertEquals(403, $response->getStatusCode()); // Should be forbidden

        // Create admin user
        $adminUser = [
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminUser['username'], $adminUser['email'], $adminUser['password'], $adminUser['role']]);

        // Login as admin
        $adminLoginData = [
            'email' => 'admin@example.com',
            'password' => 'password123'
        ];

        $this->httpClient->post('/auth/login', [
            'form_params' => $adminLoginData
        ]);

        // Access admin area
        $adminResponse = $this->httpClient->get('/admin/posts');
        $this->assertEquals(200, $adminResponse->getStatusCode());
    }

    public function testSessionManagement(): void
    {
        // Create a user
        $userData = [
            'username' => 'sessionuser',
            'email' => 'session@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        // Login
        $loginData = [
            'email' => 'session@example.com',
            'password' => 'password123'
        ];

        $response = $this->httpClient->post('/auth/login', [
            'form_params' => $loginData
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        // Test session persistence
        $profileResponse = $this->httpClient->get('/profile');
        $this->assertEquals(200, $profileResponse->getStatusCode());

        // Test session timeout (this would depend on your session configuration)
        // For now, we'll just test that the session works
        $dashboardResponse = $this->httpClient->get('/dashboard');
        $this->assertEquals(200, $dashboardResponse->getStatusCode());
    }

    public function testConcurrentUserSessions(): void
    {
        // Create multiple users
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $userData = [
                'username' => "concurrentuser$i",
                'email' => "concurrent$i@example.com",
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user'
            ];

            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);
            
            $users[] = $userData;
        }

        // Test concurrent logins
        $responses = [];
        foreach ($users as $user) {
            $loginData = [
                'email' => $user['email'],
                'password' => 'password123'
            ];

            $responses[] = $this->httpClient->post('/auth/login', [
                'form_params' => $loginData
            ]);
        }

        // Verify all logins were successful
        foreach ($responses as $response) {
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testInvalidLoginAttempts(): void
    {
        // Create a user
        $userData = [
            'username' => 'invalidloginuser',
            'email' => 'invalidlogin@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], $userData['password'], $userData['role']]);

        // Test invalid password
        $invalidLoginData = [
            'email' => 'invalidlogin@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->httpClient->post('/auth/login', [
            'form_params' => $invalidLoginData,
            'http_errors' => false
        ]);

        $this->assertEquals(401, $response->getStatusCode());

        // Test non-existent user
        $nonExistentData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        $response = $this->httpClient->post('/auth/login', [
            'form_params' => $nonExistentData,
            'http_errors' => false
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }
} 