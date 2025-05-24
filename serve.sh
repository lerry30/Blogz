#!/bin/bash
# Development server script for Termux

# Default port
PORT=${1:-8000}

# Go to project root directory
cd "$(dirname "$0")"

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "PHP is not installed. Installing..."
    pkg install php -y
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Installing..."
    pkg install composer -y
fi

# Check for .env file
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
    
    # Generate a random application key
    APP_KEY=$(php -r "echo base64_encode(random_bytes(32));")
    sed -i "s/APP_KEY=/APP_KEY=base64:$APP_KEY/" .env
fi

# Install dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    echo "Installing dependencies..."
    composer install
fi

# Create storage directories if they don't exist
mkdir -p storage/logs
mkdir -p storage/cache
mkdir -p storage/sessions

# Set permissions
chmod -R 755 storage

echo "Starting development server on port $PORT..."
echo "Access your application at http://localhost:$PORT"
echo "Press Ctrl+C to stop the server"

# Start PHP development server
php -S 0.0.0.0:$PORT -t public
