# Installing rdkafka Extension on Windows with MAMP

Since you're using MAMP on Windows, you'll need to install the rdkafka PHP extension manually. Here are the steps:

## Method 1: Download Pre-compiled Extension (Recommended)

### Step 1: Find Your PHP Version and Architecture
You have PHP 8.3.1 (ZTS Visual C++ 2019 x64), which means:
- PHP Version: 8.3
- Architecture: x64 (64-bit)
- Thread Safety: ZTS (Thread Safe)
- Compiler: Visual C++ 2019

### Step 2: Download rdkafka Extension
1. Go to: https://pecl.php.net/package/rdkafka
2. Download the Windows DLL for your PHP version
3. Look for: `php_rdkafka-6.0.5-8.3-ts-vs16-x64.zip` (or similar)

### Step 3: Install the Extension
1. Extract the downloaded ZIP file
2. Copy `php_rdkafka.dll` to your MAMP PHP extensions directory:
   ```
   C:\MAMP\bin\php\php8.3.1\ext\
   ```

### Step 4: Enable the Extension
1. Open your `php.ini` file (usually in `C:\MAMP\bin\php\php8.3.1\php.ini`)
2. Add this line:
   ```ini
   extension=rdkafka
   ```
3. Save the file

### Step 5: Restart MAMP
1. Stop MAMP Apache server
2. Start MAMP Apache server again

## Method 2: Using Composer (Alternative)

If the extension installation is challenging, you can use a pure PHP Kafka client as an alternative:

```bash
composer require kwn/php-rdkafka-stubs
```

Then modify the `ActivityPool.php` to use a different approach.

## Method 3: Docker PHP with rdkafka (Alternative)

If you prefer, you can run your PHP application in Docker with rdkafka pre-installed:

```dockerfile
FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    librdkafka-dev \
    && rm -rf /var/lib/apt/lists/*

# Install rdkafka extension
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# Copy your application
COPY . /var/www/html/
```

## Verification

After installation, verify the extension is loaded:

```bash
php -m | grep rdkafka
```

Or create a test file:

```php
<?php
if (extension_loaded('rdkafka')) {
    echo "✅ rdkafka extension is loaded successfully!\n";
} else {
    echo "❌ rdkafka extension is not loaded.\n";
}
?>
```

## Troubleshooting

### Common Issues:

1. **"Class RdKafka\Conf not found"**
   - Extension not loaded properly
   - Check php.ini configuration
   - Restart web server

2. **"Unable to load dynamic library"**
   - Wrong architecture (x86 vs x64)
   - Missing Visual C++ Redistributable
   - Wrong PHP version

3. **"DLL load failed"**
   - Missing dependencies
   - Install Visual C++ Redistributable 2019

### Visual C++ Redistributable
Download and install: https://aka.ms/vs/16/release/vc_redist.x64.exe

## Alternative: Pure PHP Implementation

If extension installation continues to be problematic, I can create a pure PHP implementation using HTTP requests to Kafka REST API or a different message queue system like Redis.

## Next Steps

Once rdkafka is installed:

1. Test the connection:
   ```bash
   php kafka-setup.php
   ```

2. Start your blog application and test the activity pool

3. Access Kafka UI at: http://localhost:8080

## Support

If you continue having issues with rdkafka installation, let me know and I can:
1. Create a pure PHP alternative
2. Use a different message queue system
3. Provide a Docker-based PHP environment with rdkafka pre-installed 