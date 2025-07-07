# Testing Guide for PHP Blog Application

## Overview

This testing framework provides comprehensive testing capabilities for the PHP Blog Application, covering all aspects mentioned in the vacancy requirements:

- **Unit Tests**: Individual component testing
- **Functional Tests**: End-to-end functionality testing
- **API Tests**: RESTful API endpoint testing
- **Performance Tests**: Load, stress, and performance testing
- **Integration Tests**: Cross-component integration testing
- **Selenium Tests**: Browser automation testing

## Test Types and Coverage

### 1. Unit Tests (`tests/Unit/`)

**Purpose**: Test individual methods and classes in isolation

**Coverage**:
- User model operations (CRUD, authentication, validation)
- Post model operations (CRUD, search, filtering)
- Category and Tag models
- Validation logic
- Helper functions

**Example**:
```php
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
}
```

### 2. Functional Tests (`tests/Functional/`)

**Purpose**: Test complete user workflows and page functionality

**Coverage**:
- Blog page loading and navigation
- Post viewing and interaction
- Search functionality
- Pagination
- Category filtering
- RSS feeds and sitemaps

**Example**:
```php
public function testBlogIndexPage(): void
{
    $response = $this->httpClient->get('/blog');
    
    $this->assertEquals(200, $response->getStatusCode());
    $content = $response->getBody()->getContents();
    $this->assertStringContainsString('Blog Posts', $content);
}
```

### 3. API Tests (`tests/Api/`)

**Purpose**: Test RESTful API endpoints and responses

**Coverage**:
- GET, POST, PUT, DELETE operations
- JSON response validation
- Error handling
- Authentication and authorization
- Rate limiting
- Pagination and filtering

**Example**:
```php
public function testGetAllPosts(): void
{
    $response = $this->httpClient->get('/api/posts');
    
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertJson($response->getBody()->getContents());
    
    $data = json_decode($response->getBody()->getContents(), true);
    $this->assertArrayHasKey('posts', $data);
}
```

### 4. Performance Tests (`tests/Performance/`)

**Purpose**: Measure system performance under various conditions

**Coverage**:
- Response time testing
- Concurrent request handling
- Database query performance
- Memory usage monitoring
- Load testing (multiple users)
- Stress testing (extended periods)

**Example**:
```php
public function testLoadTest(): void
{
    $concurrentUsers = 20;
    $requestsPerUser = 5;
    
    // Simulate concurrent users making requests
    $pool = new Pool($this->httpClient, $requests(), [
        'concurrency' => $concurrentUsers,
        'fulfilled' => function ($response, $index) use (&$successfulRequests) {
            $successfulRequests++;
        }
    ]);
    
    $this->assertGreaterThan(0.9, $successfulRequests / $totalRequests);
}
```

### 5. Integration Tests (`tests/Integration/`)

**Purpose**: Test interactions between different system components

**Coverage**:
- User authentication flow
- Session management
- Database transactions
- Cross-component data flow
- Error propagation

**Example**:
```php
public function testUserRegistrationFlow(): void
{
    $userData = [
        'username' => 'newuser' . time(),
        'email' => 'newuser' . time() . '@example.com',
        'password' => 'password123'
    ];

    $response = $this->httpClient->post('/auth/register', [
        'form_params' => $userData
    ]);

    $this->assertEquals(200, $response->getStatusCode());
    
    // Verify user was created in database
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$userData['email']]);
    $user = $stmt->fetch();
    
    $this->assertNotNull($user);
}
```

### 6. Selenium Tests (`tests/Functional/SeleniumBlogTest.php`)

**Purpose**: Browser automation testing for real user interactions

**Coverage**:
- Form submissions
- Navigation flows
- UI interactions
- Responsive design testing
- File uploads
- JavaScript functionality

**Example**:
```php
public function testBlogPostCreation(): void
{
    $this->loginAsAdmin();
    
    $this->driver->get($this->baseUrl . '/admin/posts/create');
    
    $titleField = $this->driver->findElement(WebDriverBy::name('title'));
    $titleField->sendKeys('Selenium Test Post');
    
    $submitButton = $this->driver->findElement(WebDriverBy::xpath("//button[@type='submit']"));
    $submitButton->click();
    
    $successMessage = $this->driver->findElement(WebDriverBy::className('alert-success'));
    $this->assertStringContainsString('Post created successfully', $successMessage->getText());
}
```

## Running Tests

### Prerequisites

1. **Install Dependencies**:
   ```bash
   composer install
   ```

2. **Setup Test Database**:
   ```sql
   CREATE DATABASE blog_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Configure Environment**:
   - Set database credentials in `phpunit.xml`
   - Ensure web server is running on `http://localhost:8000`

### Test Commands

#### Using the Test Runner Script

```bash
# Check test environment
php run-tests.php check

# Run all tests
php run-tests.php all

# Run specific test types
php run-tests.php unit
php run-tests.php functional
php run-tests.php api
php run-tests.php performance
php run-tests.php integration
php run-tests.php selenium

# Run with coverage
php run-tests.php coverage

# Generate comprehensive report
php run-tests.php report

# Run regression tests
php run-tests.php regression

# Run load/stress tests
php run-tests.php load
php run-tests.php stress
```

#### Using PHPUnit Directly

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit --testsuite unit
vendor/bin/phpunit --testsuite functional
vendor/bin/phpunit --testsuite api
vendor/bin/phpunit --testsuite performance
vendor/bin/phpunit --testsuite integration

# Run specific test file
vendor/bin/phpunit tests/Unit/UserTest.php

# Run specific test method
vendor/bin/phpunit tests/Unit/UserTest.php::testUserCreation

# Run with coverage
vendor/bin/phpunit --coverage-html coverage

# Run with verbose output
vendor/bin/phpunit --verbose
```

## Test Configuration

### PHPUnit Configuration (`phpunit.xml`)

```xml
<phpunit>
    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="functional">
            <directory>tests/Functional</directory>
        </testsuite>
        <testsuite name="api">
            <directory>tests/Api</directory>
        </testsuite>
        <testsuite name="performance">
            <directory>tests/Performance</directory>
        </testsuite>
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>

    <coverage>
        <include>
            <directory suffix=".php">app/</directory>
        </include>
        <exclude>
            <directory>vendor/</directory>
            <directory>tests/</directory>
        </exclude>
    </coverage>
</phpunit>
```

### Environment Variables

```bash
# Test environment
APP_ENV=testing
DB_DATABASE=blog_test
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
```

## Test Data Management

### Database Setup

The test framework automatically:
1. Creates test database if it doesn't exist
2. Runs migrations to create tables
3. Seeds test data
4. Cleans up after each test

### Test Data Seeding

```php
protected function seedTestData(): void
{
    // Create test users
    $this->pdo->exec("INSERT IGNORE INTO users (username, email, password, role) VALUES 
        ('testuser', 'test@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'user'),
        ('admin', 'admin@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')");

    // Create test categories
    $this->pdo->exec("INSERT IGNORE INTO categories (name, slug, description) VALUES 
        ('Technology', 'technology', 'Technology related posts'),
        ('Lifestyle', 'lifestyle', 'Lifestyle related posts')");
}
```

## Best Practices

### 1. Test Organization

- **Arrange**: Set up test data and conditions
- **Act**: Execute the code being tested
- **Assert**: Verify the expected outcomes

### 2. Test Naming

- Use descriptive test method names
- Follow the pattern: `test[What][When][ExpectedResult]`
- Example: `testUserCreationWithValidDataReturnsUserId()`

### 3. Test Isolation

- Each test should be independent
- Clean up test data after each test
- Don't rely on test execution order

### 4. Assertions

- Use specific assertions
- Test one concept per test method
- Include both positive and negative test cases

### 5. Performance Testing

- Set realistic performance thresholds
- Test under various load conditions
- Monitor memory usage and response times

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: ''
          MYSQL_DATABASE: blog_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: pdo_mysql
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
        
    - name: Run tests
      run: vendor/bin/phpunit --coverage-clover coverage.xml
        
    - name: Upload coverage
      uses: codecov/codecov-action@v1
      with:
        file: ./coverage.xml
```

## Reporting and Metrics

### Coverage Reports

- **HTML Coverage**: `coverage/index.html`
- **Text Coverage**: `coverage.txt`
- **Clover XML**: `test-reports/coverage.xml`

### Test Reports

- **JUnit XML**: `test-reports/junit.xml`
- **HTML Report**: `test-reports/test-report-[timestamp].html`

### Key Metrics

- **Test Coverage**: Percentage of code covered by tests
- **Test Execution Time**: How long tests take to run
- **Success Rate**: Percentage of tests passing
- **Performance Metrics**: Response times, throughput

## Troubleshooting

### Common Issues

1. **Database Connection Errors**:
   - Verify database credentials
   - Ensure test database exists
   - Check MySQL service is running

2. **Selenium Test Failures**:
   - Install ChromeDriver
   - Ensure Chrome browser is available
   - Check web server is running

3. **Performance Test Failures**:
   - Adjust performance thresholds
   - Check system resources
   - Verify test environment setup

### Debug Mode

```bash
# Run tests with debug output
vendor/bin/phpunit --verbose --debug

# Run specific test with detailed output
vendor/bin/phpunit tests/Unit/UserTest.php::testUserCreation --verbose
```

## Conclusion

This comprehensive testing framework provides:

✅ **Unit Testing**: Individual component validation  
✅ **Functional Testing**: End-to-end workflow testing  
✅ **API Testing**: RESTful endpoint validation  
✅ **Performance Testing**: Load and stress testing  
✅ **Integration Testing**: Cross-component testing  
✅ **Browser Automation**: Selenium WebDriver testing  
✅ **Automated Reporting**: Coverage and test reports  
✅ **CI/CD Integration**: GitHub Actions support  

The framework meets all the requirements specified in the vacancy and provides a solid foundation for maintaining code quality and ensuring application reliability. 