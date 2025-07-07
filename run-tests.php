<?php

/**
 * Test Runner Script for PHP Blog Application
 * 
 * This script provides easy commands to run different types of tests
 * and generate comprehensive test reports.
 */

require_once 'vendor/autoload.php';

class TestRunner
{
    private $phpunitPath;
    private $coverageDir;
    private $reportsDir;

    public function __construct()
    {
        $this->phpunitPath = 'vendor/bin/phpunit';
        $this->coverageDir = 'coverage';
        $this->reportsDir = 'test-reports';
        
        // Create directories if they don't exist
        if (!is_dir($this->coverageDir)) {
            mkdir($this->coverageDir, 0755, true);
        }
        if (!is_dir($this->reportsDir)) {
            mkdir($this->reportsDir, 0755, true);
        }
    }

    public function runAllTests(): void
    {
        echo "🚀 Running all tests...\n";
        $this->executeCommand("$this->phpunitPath --verbose");
    }

    public function runUnitTests(): void
    {
        echo "🧪 Running unit tests...\n";
        $this->executeCommand("$this->phpunitPath --testsuite unit --verbose");
    }

    public function runFunctionalTests(): void
    {
        echo "🔧 Running functional tests...\n";
        $this->executeCommand("$this->phpunitPath --testsuite functional --verbose");
    }

    public function runApiTests(): void
    {
        echo "🌐 Running API tests...\n";
        $this->executeCommand("$this->phpunitPath --testsuite api --verbose");
    }

    public function runPerformanceTests(): void
    {
        echo "⚡ Running performance tests...\n";
        $this->executeCommand("$this->phpunitPath --testsuite performance --verbose");
    }

    public function runIntegrationTests(): void
    {
        echo "🔗 Running integration tests...\n";
        $this->executeCommand("$this->phpunitPath --testsuite integration --verbose");
    }

    public function runWithCoverage(): void
    {
        echo "📊 Running tests with coverage report...\n";
        $this->executeCommand("$this->phpunitPath --coverage-html $this->coverageDir --coverage-text");
    }

    public function runSeleniumTests(): void
    {
        echo "🌍 Running Selenium WebDriver tests...\n";
        $this->executeCommand("$this->phpunitPath tests/Functional/SeleniumBlogTest.php --verbose");
    }

    public function generateTestReport(): void
    {
        echo "📋 Generating comprehensive test report...\n";
        
        $reportFile = $this->reportsDir . '/test-report-' . date('Y-m-d-H-i-s') . '.html';
        
        $command = "$this->phpunitPath --log-junit $this->reportsDir/junit.xml " .
                  "--coverage-html $this->coverageDir " .
                  "--coverage-clover $this->reportsDir/coverage.xml " .
                  "--testdox-html $reportFile";
        
        $this->executeCommand($command);
        
        echo "📄 Test report generated: $reportFile\n";
    }

    public function runRegressionTests(): void
    {
        echo "🔄 Running regression tests...\n";
        
        // Run critical functionality tests
        $criticalTests = [
            'tests/Unit/UserTest.php',
            'tests/Unit/PostTest.php',
            'tests/Functional/BlogControllerTest.php',
            'tests/Api/PostApiTest.php'
        ];
        
        foreach ($criticalTests as $test) {
            echo "Running: $test\n";
            $this->executeCommand("$this->phpunitPath $test --verbose");
        }
    }

    public function runLoadTests(): void
    {
        echo "📈 Running load tests...\n";
        $this->executeCommand("$this->phpunitPath tests/Performance/PerformanceTest.php::testLoadTest --verbose");
    }

    public function runStressTests(): void
    {
        echo "💪 Running stress tests...\n";
        $this->executeCommand("$this->phpunitPath tests/Performance/PerformanceTest.php::testStressTest --verbose");
    }

    public function checkTestEnvironment(): void
    {
        echo "🔍 Checking test environment...\n";
        
        $checks = [
            'PHP Version' => PHP_VERSION,
            'PHPUnit Available' => file_exists($this->phpunitPath),
            'Test Database' => $this->checkDatabaseConnection(),
            'Test Directories' => is_dir('tests'),
            'Coverage Directory' => is_dir($this->coverageDir),
            'Reports Directory' => is_dir($this->reportsDir)
        ];
        
        foreach ($checks as $check => $result) {
            $status = $result ? '✅' : '❌';
            echo "$status $check: " . ($result ? 'OK' : 'FAILED') . "\n";
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=blog_test;charset=utf8mb4",
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function executeCommand(string $command): void
    {
        echo "Executing: $command\n";
        $output = shell_exec($command . ' 2>&1');
        echo $output;
        
        if ($output === null) {
            echo "❌ Command failed to execute\n";
        }
    }

    public function showHelp(): void
    {
        echo "PHP Blog Application Test Runner\n";
        echo "================================\n\n";
        echo "Available commands:\n";
        echo "  all              - Run all tests\n";
        echo "  unit             - Run unit tests only\n";
        echo "  functional       - Run functional tests only\n";
        echo "  api              - Run API tests only\n";
        echo "  performance      - Run performance tests only\n";
        echo "  integration      - Run integration tests only\n";
        echo "  selenium         - Run Selenium WebDriver tests\n";
        echo "  coverage         - Run tests with coverage report\n";
        echo "  report           - Generate comprehensive test report\n";
        echo "  regression       - Run regression tests\n";
        echo "  load             - Run load tests\n";
        echo "  stress           - Run stress tests\n";
        echo "  check            - Check test environment\n";
        echo "  help             - Show this help message\n\n";
        echo "Usage: php run-tests.php [command]\n";
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    $runner = new TestRunner();
    
    $command = $argv[1] ?? 'help';
    
    switch ($command) {
        case 'all':
            $runner->runAllTests();
            break;
        case 'unit':
            $runner->runUnitTests();
            break;
        case 'functional':
            $runner->runFunctionalTests();
            break;
        case 'api':
            $runner->runApiTests();
            break;
        case 'performance':
            $runner->runPerformanceTests();
            break;
        case 'integration':
            $runner->runIntegrationTests();
            break;
        case 'selenium':
            $runner->runSeleniumTests();
            break;
        case 'coverage':
            $runner->runWithCoverage();
            break;
        case 'report':
            $runner->generateTestReport();
            break;
        case 'regression':
            $runner->runRegressionTests();
            break;
        case 'load':
            $runner->runLoadTests();
            break;
        case 'stress':
            $runner->runStressTests();
            break;
        case 'check':
            $runner->checkTestEnvironment();
            break;
        case 'help':
        default:
            $runner->showHelp();
            break;
    }
} 