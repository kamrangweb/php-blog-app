<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;

class SeleniumBlogTest extends TestCase
{
    protected $driver;
    protected $baseUrl = 'http://localhost:8000';

    protected function setUp(): void
    {
        parent::setUp();
        
        $options = new ChromeOptions();
        $options->addArguments(['--headless', '--no-sandbox', '--disable-dev-shm-usage']);
        
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        
        $this->driver = ChromeDriver::start($capabilities);
        $this->driver->manage()->window()->maximize();
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
        parent::tearDown();
    }

    public function testHomePageNavigation(): void
    {
        $this->driver->get($this->baseUrl);
        
        // Wait for page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
        
        // Check page title
        $this->assertStringContainsString('Blog', $this->driver->getTitle());
        
        // Check for navigation menu
        $nav = $this->driver->findElement(WebDriverBy::tagName('nav'));
        $this->assertNotNull($nav);
    }

    public function testBlogPostCreation(): void
    {
        // Login first
        $this->loginAsAdmin();
        
        // Navigate to create post page
        $this->driver->get($this->baseUrl . '/admin/posts/create');
        
        // Fill in the form
        $titleField = $this->driver->findElement(WebDriverBy::name('title'));
        $titleField->sendKeys('Selenium Test Post');
        
        $contentField = $this->driver->findElement(WebDriverBy::name('content'));
        $contentField->sendKeys('This is a test post created by Selenium WebDriver.');
        
        $excerptField = $this->driver->findElement(WebDriverBy::name('excerpt'));
        $excerptField->sendKeys('Test excerpt for Selenium post.');
        
        // Select category
        $categorySelect = $this->driver->findElement(WebDriverBy::name('category_id'));
        $categorySelect->click();
        $categoryOption = $this->driver->findElement(WebDriverBy::xpath("//option[contains(text(), 'Technology')]"));
        $categoryOption->click();
        
        // Set status to published
        $statusSelect = $this->driver->findElement(WebDriverBy::name('status'));
        $statusSelect->click();
        $publishedOption = $this->driver->findElement(WebDriverBy::xpath("//option[text()='published']"));
        $publishedOption->click();
        
        // Submit the form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Wait for redirect and check success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
        
        $successMessage = $this->driver->findElement(WebDriverBy::className('alert-success'));
        $this->assertStringContainsString('Post created successfully', $successMessage->getText());
    }

    public function testBlogPostEditing(): void
    {
        // Login first
        $this->loginAsAdmin();
        
        // Navigate to posts list
        $this->driver->get($this->baseUrl . '/admin/posts');
        
        // Find and click edit button for first post
        $editButton = $this->driver->findElement(WebDriverBy::xpath("//a[contains(@href, '/edit')]"));
        $editButton->click();
        
        // Wait for edit form to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('title'))
        );
        
        // Update the title
        $titleField = $this->driver->findElement(WebDriverBy::name('title'));
        $titleField->clear();
        $titleField->sendKeys('Updated Post Title - Selenium Test');
        
        // Submit the form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Check success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
        
        $successMessage = $this->driver->findElement(WebDriverBy::className('alert-success'));
        $this->assertStringContainsString('Post updated successfully', $successMessage->getText());
    }

    public function testBlogPostDeletion(): void
    {
        // Login first
        $this->loginAsAdmin();
        
        // Navigate to posts list
        $this->driver->get($this->baseUrl . '/admin/posts');
        
        // Find and click delete button for first post
        $deleteButton = $this->driver->findElement(WebDriverBy::xpath("//button[contains(@class, 'btn-danger')]"));
        $deleteButton->click();
        
        // Handle confirmation dialog
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::alertIsPresent()
        );
        
        $alert = $this->driver->switchTo()->alert();
        $alert->accept();
        
        // Check success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
        
        $successMessage = $this->driver->findElement(WebDriverBy::className('alert-success'));
        $this->assertStringContainsString('Post deleted successfully', $successMessage->getText());
    }

    public function testUserRegistration(): void
    {
        $this->driver->get($this->baseUrl . '/auth/register');
        
        // Fill registration form
        $usernameField = $this->driver->findElement(WebDriverBy::name('username'));
        $usernameField->sendKeys('seleniumuser' . time());
        
        $emailField = $this->driver->findElement(WebDriverBy::name('email'));
        $emailField->sendKeys('selenium' . time() . '@example.com');
        
        $passwordField = $this->driver->findElement(WebDriverBy::name('password'));
        $passwordField->sendKeys('password123');
        
        $confirmPasswordField = $this->driver->findElement(WebDriverBy::name('password_confirm'));
        $confirmPasswordField->sendKeys('password123');
        
        // Submit form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Check for success message or redirect
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
        
        $pageSource = $this->driver->getPageSource();
        $this->assertTrue(
            strpos($pageSource, 'Registration successful') !== false || 
            strpos($pageSource, 'Welcome') !== false,
            'Registration should be successful'
        );
    }

    public function testUserLogin(): void
    {
        $this->driver->get($this->baseUrl . '/auth/login');
        
        // Fill login form
        $emailField = $this->driver->findElement(WebDriverBy::name('email'));
        $emailField->sendKeys('admin@example.com');
        
        $passwordField = $this->driver->findElement(WebDriverBy::name('password'));
        $passwordField->sendKeys('admin123');
        
        // Submit form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Check for successful login
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
        
        $pageSource = $this->driver->getPageSource();
        $this->assertTrue(
            strpos($pageSource, 'Welcome') !== false || 
            strpos($pageSource, 'Dashboard') !== false,
            'Login should be successful'
        );
    }

    public function testBlogSearch(): void
    {
        $this->driver->get($this->baseUrl . '/blog');
        
        // Find search input
        $searchInput = $this->driver->findElement(WebDriverBy::name('search'));
        $searchInput->sendKeys('test');
        $searchInput->sendKeys(WebDriverKeys::ENTER);
        
        // Wait for search results
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('article'))
        );
        
        // Check if search results are displayed
        $articles = $this->driver->findElements(WebDriverBy::tagName('article'));
        $this->assertGreaterThan(0, count($articles), 'Search should return results');
    }

    public function testBlogPagination(): void
    {
        $this->driver->get($this->baseUrl . '/blog');
        
        // Check if pagination exists
        $pagination = $this->driver->findElements(WebDriverBy::className('pagination'));
        
        if (!empty($pagination)) {
            // Click on next page
            $nextButton = $this->driver->findElement(WebDriverBy::xpath("//a[contains(text(), 'Next')]"));
            $nextButton->click();
            
            // Wait for page to load
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('article'))
            );
            
            // Check if we're on page 2
            $currentUrl = $this->driver->getCurrentURL();
            $this->assertStringContainsString('page=2', $currentUrl);
        }
    }

    public function testResponsiveDesign(): void
    {
        $this->driver->get($this->baseUrl);
        
        // Test mobile viewport
        $this->driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(375, 667));
        
        // Check if mobile menu is accessible
        $mobileMenu = $this->driver->findElements(WebDriverBy::className('navbar-toggler'));
        
        if (!empty($mobileMenu)) {
            $mobileMenu[0]->click();
            
            // Wait for mobile menu to expand
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('navbar-collapse'))
            );
            
            $navbarCollapse = $this->driver->findElement(WebDriverBy::className('navbar-collapse'));
            $this->assertTrue($navbarCollapse->isDisplayed(), 'Mobile menu should be visible');
        }
        
        // Test tablet viewport
        $this->driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(768, 1024));
        
        // Test desktop viewport
        $this->driver->manage()->window()->setSize(new \Facebook\WebDriver\WebDriverDimension(1920, 1080));
    }

    public function testFormValidation(): void
    {
        $this->driver->get($this->baseUrl . '/auth/register');
        
        // Try to submit empty form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Check for validation errors
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-danger'))
        );
        
        $errorMessages = $this->driver->findElements(WebDriverBy::className('alert-danger'));
        $this->assertGreaterThan(0, count($errorMessages), 'Validation errors should be displayed');
    }

    public function testFileUpload(): void
    {
        // Login first
        $this->loginAsAdmin();
        
        $this->driver->get($this->baseUrl . '/admin/posts/create');
        
        // Find file upload input
        $fileInput = $this->driver->findElement(WebDriverBy::name('featured_image'));
        
        // Upload a test image
        $testImagePath = __DIR__ . '/../../public/images/hero-img.webp';
        $fileInput->sendKeys($testImagePath);
        
        // Fill other required fields
        $titleField = $this->driver->findElement(WebDriverBy::name('title'));
        $titleField->sendKeys('Post with Image');
        
        $contentField = $this->driver->findElement(WebDriverBy::name('content'));
        $contentField->sendKeys('This post has an uploaded image.');
        
        // Submit form
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Check for success
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
    }

    private function loginAsAdmin(): void
    {
        $this->driver->get($this->baseUrl . '/auth/login');
        
        $emailField = $this->driver->findElement(WebDriverBy::name('email'));
        $emailField->sendKeys('admin@example.com');
        
        $passwordField = $this->driver->findElement(WebDriverBy::name('password'));
        $passwordField->sendKeys('admin123');
        
        $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
        $submitButton->click();
        
        // Wait for login to complete
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
        );
    }
} 