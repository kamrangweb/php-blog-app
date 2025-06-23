# Docker Kafka Setup Script for Windows
# This script helps you manage Kafka using Docker Compose

param(
    [Parameter(Mandatory=$false)]
    [ValidateSet("start", "stop", "restart", "status", "logs", "clean", "test")]
    [string]$Action = "start"
)

Write-Host "🐳 Kafka Docker Setup Script" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan

function Start-Kafka {
    Write-Host "🚀 Starting Kafka with Docker Compose..." -ForegroundColor Green
    
    # Check if Docker is running
    try {
        docker version | Out-Null
    } catch {
        Write-Host "❌ Docker is not running. Please start Docker Desktop first." -ForegroundColor Red
        exit 1
    }
    
    # Start the services
    docker-compose up -d
    
    Write-Host "✅ Kafka services started successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📊 Access Points:" -ForegroundColor Yellow
    Write-Host "   - Kafka UI: http://localhost:8080" -ForegroundColor White
    Write-Host "   - Kafdrop UI: http://localhost:9000" -ForegroundColor White
    Write-Host "   - Kafka Broker: localhost:9092" -ForegroundColor White
    Write-Host "   - Zookeeper: localhost:2181" -ForegroundColor White
    Write-Host ""
    Write-Host "⏳ Waiting for services to be ready..." -ForegroundColor Yellow
    
    # Wait for Kafka to be ready
    $maxAttempts = 30
    $attempt = 0
    
    while ($attempt -lt $maxAttempts) {
        try {
            $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -TimeoutSec 5
            if ($response.StatusCode -eq 200) {
                Write-Host "✅ Kafka UI is ready!" -ForegroundColor Green
                break
            }
        } catch {
            $attempt++
            Write-Host "⏳ Waiting for Kafka to be ready... ($attempt/$maxAttempts)" -ForegroundColor Yellow
            Start-Sleep -Seconds 2
        }
    }
    
    if ($attempt -eq $maxAttempts) {
        Write-Host "⚠️  Kafka might still be starting up. Check the logs with: .\docker-setup.ps1 -Action logs" -ForegroundColor Yellow
    }
}

function Stop-Kafka {
    Write-Host "🛑 Stopping Kafka services..." -ForegroundColor Yellow
    docker-compose down
    Write-Host "✅ Kafka services stopped." -ForegroundColor Green
}

function Restart-Kafka {
    Write-Host "🔄 Restarting Kafka services..." -ForegroundColor Yellow
    docker-compose down
    docker-compose up -d
    Write-Host "✅ Kafka services restarted." -ForegroundColor Green
}

function Get-KafkaStatus {
    Write-Host "📊 Kafka Services Status:" -ForegroundColor Cyan
    docker-compose ps
}

function Get-KafkaLogs {
    Write-Host "📋 Kafka Logs:" -ForegroundColor Cyan
    docker-compose logs -f
}

function Clean-Kafka {
    Write-Host "🧹 Cleaning up Kafka data..." -ForegroundColor Yellow
    docker-compose down -v
    docker system prune -f
    Write-Host "✅ Kafka data cleaned up." -ForegroundColor Green
}

function Test-Kafka {
    Write-Host "🧪 Testing Kafka connection..." -ForegroundColor Cyan
    
    # Test if Kafka is accessible
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -TimeoutSec 10
        if ($response.StatusCode -eq 200) {
            Write-Host "✅ Kafka UI is accessible at http://localhost:8080" -ForegroundColor Green
        }
    } catch {
        Write-Host "❌ Kafka UI is not accessible. Make sure services are running." -ForegroundColor Red
    }
    
    # Test PHP connection
    Write-Host "🧪 Testing PHP Kafka connection..." -ForegroundColor Cyan
    try {
        php kafka-setup.php
    } catch {
        Write-Host "❌ PHP Kafka test failed. Check if rdkafka extension is installed." -ForegroundColor Red
    }
}

# Main execution
switch ($Action) {
    "start" { Start-Kafka }
    "stop" { Stop-Kafka }
    "restart" { Restart-Kafka }
    "status" { Get-KafkaStatus }
    "logs" { Get-KafkaLogs }
    "clean" { Clean-Kafka }
    "test" { Test-Kafka }
    default {
        Write-Host "Usage: .\docker-setup.ps1 -Action [start|stop|restart|status|logs|clean|test]" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Actions:" -ForegroundColor Cyan
        Write-Host "  start   - Start Kafka services" -ForegroundColor White
        Write-Host "  stop    - Stop Kafka services" -ForegroundColor White
        Write-Host "  restart - Restart Kafka services" -ForegroundColor White
        Write-Host "  status  - Show service status" -ForegroundColor White
        Write-Host "  logs    - Show service logs" -ForegroundColor White
        Write-Host "  clean   - Clean up all data" -ForegroundColor White
        Write-Host "  test    - Test Kafka connection" -ForegroundColor White
    }
} 