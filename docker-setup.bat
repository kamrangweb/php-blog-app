@echo off
setlocal enabledelayedexpansion

echo 🐳 Kafka Docker Setup Script
echo =============================

if "%1"=="" (
    echo Usage: docker-setup.bat [start^|stop^|restart^|status^|logs^|clean^|test]
    echo.
    echo Actions:
    echo   start   - Start Kafka services
    echo   stop    - Stop Kafka services
    echo   restart - Restart Kafka services
    echo   status  - Show service status
    echo   logs    - Show service logs
    echo   clean   - Clean up all data
    echo   test    - Test Kafka connection
    goto :eof
)

if "%1"=="start" (
    echo 🚀 Starting Kafka with Docker Compose...
    docker-compose up -d
    echo ✅ Kafka services started successfully!
    echo.
    echo 📊 Access Points:
    echo    - Kafka UI: http://localhost:8080
    echo    - Kafdrop UI: http://localhost:9000
    echo    - Kafka Broker: localhost:9092
    echo    - Zookeeper: localhost:2181
    goto :eof
)

if "%1"=="stop" (
    echo 🛑 Stopping Kafka services...
    docker-compose down
    echo ✅ Kafka services stopped.
    goto :eof
)

if "%1"=="restart" (
    echo 🔄 Restarting Kafka services...
    docker-compose down
    docker-compose up -d
    echo ✅ Kafka services restarted.
    goto :eof
)

if "%1"=="status" (
    echo 📊 Kafka Services Status:
    docker-compose ps
    goto :eof
)

if "%1"=="logs" (
    echo 📋 Kafka Logs:
    docker-compose logs -f
    goto :eof
)

if "%1"=="clean" (
    echo 🧹 Cleaning up Kafka data...
    docker-compose down -v
    docker system prune -f
    echo ✅ Kafka data cleaned up.
    goto :eof
)

if "%1"=="test" (
    echo 🧪 Testing Kafka connection...
    echo Testing PHP Kafka connection...
    php kafka-setup.php
    goto :eof
)

echo ❌ Unknown action: %1
echo Use: start, stop, restart, status, logs, clean, or test 