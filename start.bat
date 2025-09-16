@echo off
echo Starting Infobip SMS Webapp with Docker...
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker is not installed or not in PATH
    echo Please install Docker Desktop and try again
    pause
    exit /b 1
)

REM Check if Docker Compose is available
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker Compose is not available
    echo Please ensure Docker Compose is installed
    pause
    exit /b 1
)

REM Check if .env file exists
if not exist ".env" (
    echo WARNING: .env file not found
    echo Please copy env.example to .env and configure your API credentials
    echo.
    echo Creating .env file from template...
    copy env.example .env
    echo.
    echo Please edit .env file with your Infobip API credentials
    echo Then run this script again
    pause
    exit /b 1
)

echo Building and starting Docker containers...
echo.
echo Access the app at: http://localhost:8080
echo Press Ctrl+C to stop the containers
echo.

docker-compose up --build
