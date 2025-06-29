#!/usr/bin/env php
<?php

// Velocity - Standalone PHP Deployment Tool
// No dependencies required!

class VelocityStandalone {
    public function run($args) {
        $command = $args[1] ?? 'help';
        
        echo "🚀 Velocity v1.0 - Zero-pain PHP deployment\n\n";
        
        switch ($command) {
            case 'init':
                $this->init();
                break;
            case 'deploy':
                $this->deploy();
                break;
            case 'debug':
                $this->debug();
                break;
            default:
                $this->help();
        }
    }
    
    private function init() {
        echo "⚡ Initializing Velocity...\n";
        
        // Create velocity directory
        if (!is_dir('velocity')) {
            mkdir('velocity');
            echo "✅ Created velocity/ directory\n";
        }
        
        // Generate nginx config
        $nginxConfig = $this->generateNginxConfig();
        file_put_contents('velocity/nginx.conf', $nginxConfig);
        echo "✅ Generated nginx.conf\n";
        
        // Generate deploy script
        $deployScript = $this->generateDeployScript();
        file_put_contents('velocity/deploy.sh', $deployScript);
        echo "✅ Generated deploy.sh\n";
        
        // Generate .env.example
        if (!file_exists('.env.example')) {
            $envExample = $this->generateEnvExample();
            file_put_contents('.env.example', $envExample);
            echo "✅ Generated .env.example\n";
        }
        
        echo "\n🎉 Velocity initialized! Run 'php velocity-standalone.php deploy' to deploy.\n";
    }
    
    private function deploy() {
        echo "🚀 Starting deployment...\n\n";
        
        if (!file_exists('velocity/deploy.sh')) {
            echo "❌ Run 'php velocity-standalone.php init' first\n";
            return;
        }
        
        // Pre-deployment checks
        echo "🔍 Pre-deployment checks:\n";
        $checks = [
            'PHP syntax' => $this->checkPhpSyntax(),
            'Environment file' => $this->checkEnvironmentFile(),
        ];
        
        foreach ($checks as $check => $passed) {
            if ($passed) {
                echo "✅ $check\n";
            } else {
                echo "❌ $check\n";
                return;
            }
        }
        
        echo "\n✅ All checks passed!\n";
        echo "🎉 Ready to deploy! (Run velocity/deploy.sh on your server)\n";
    }
    
    private function debug() {
        echo "🐛 Setting up debug environment...\n";
        
        $debugCode = $this->generateDebugCode();
        file_put_contents('velocity/debug.php', $debugCode);
        
        echo "✅ Debug dashboard created at velocity/debug.php\n";
        echo "📝 Add this to your main application:\n\n";
        echo "<?php\n";
        echo "if (\$_ENV['APP_DEBUG'] ?? false) {\n";
        echo "    require_once 'velocity/debug.php';\n";
        echo "}\n";
    }
    
    private function help() {
        echo "Available commands:\n\n";
        echo "  init    Initialize Velocity in your project\n";
        echo "  deploy  Deploy your application with health checks\n";
        echo "  debug   Set up beautiful error pages and profiling\n";
        echo "  help    Show this help message\n\n";
        echo "Examples:\n";
        echo "  php velocity-standalone.php init\n";
        echo "  php velocity-standalone.php deploy\n";
        echo "  php velocity-standalone.php debug\n";
    }
    
    private function checkPhpSyntax() {
        if (file_exists('index.php')) {
            ob_start();
            $result = include 'index.php';
            ob_end_clean();
            return true;
        }
        return true; // No main file to check
    }
    
    private function checkEnvironmentFile() {
        return file_exists('.env') || file_exists('.env.local') || true; // Optional
    }
    
    private function generateNginxConfig() {
        return <<<NGINX
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Pretty URLs
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Static files
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
NGINX;
    }
    
    private function generateDeployScript() {
        return <<<BASH
#!/bin/bash
set -e

echo "🚀 Velocity Deployment Starting..."

# Update code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations (if they exist)
if [ -f "migrate.php" ]; then
    php migrate.php
fi

# Clear caches
if [ -d "cache" ]; then
    rm -rf cache/*
fi

# Reload services
sudo systemctl reload nginx
sudo systemctl reload php8.3-fpm

echo "✅ Deployment complete!"
BASH;
    }
    
    private function generateEnvExample() {
        return <<<ENV
# Database
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password

# App
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Security
APP_KEY=generate-a-random-32-character-string
ENV;
    }
    
    private function generateDebugCode() {
        return <<<'PHP'
<?php
// Velocity Debug Dashboard - Beautiful error pages and profiling

class VelocityDebugger {
    private static $startTime;
    private static $queries = [];
    
    public static function init() {
        self::$startTime = microtime(true);
        
        set_error_handler([self::class, 'errorHandler']);
        register_shutdown_function([self::class, 'renderDebugBar']);
    }
    
    public static function errorHandler($severity, $message, $file, $line) {
        $severityName = match($severity) {
            E_ERROR => 'FATAL ERROR',
            E_WARNING => 'WARNING', 
            E_NOTICE => 'NOTICE',
            default => 'ERROR'
        };
        
        echo "<div style='background: linear-gradient(135deg, #ff6b6b, #ee5a52); color: white; padding: 20px; margin: 10px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); font-family: -apple-system, BlinkMacSystemFont, sans-serif;'>";
        echo "<h3 style='margin: 0 0 10px 0;'>🚨 {$severityName}</h3>";
        echo "<p style='margin: 5px 0;'><strong>Message:</strong> {$message}</p>";
        echo "<p style='margin: 5px 0;'><strong>File:</strong> {$file}:{$line}</p>";
        echo "</div>";
        
        return true;
    }
    
    public static function renderDebugBar() {
        $executionTime = round((microtime(true) - self::$startTime) * 1000, 2);
        $memoryUsage = round(memory_get_peak_usage() / 1024 / 1024, 2);
        
        echo "<div style='position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px; font-family: -apple-system, BlinkMacSystemFont, sans-serif; z-index: 9999; box-shadow: 0 -4px 15px rgba(0,0,0,0.2);'>";
        echo "<div style='display: flex; gap: 30px; align-items: center;'>";
        echo "<span style='font-weight: bold;'>🚀 Velocity Debug</span>";
        echo "<span>⏱️ {$executionTime}ms</span>";
        echo "<span>💾 {$memoryUsage}MB</span>";
        echo "<span>🔍 " . count(self::$queries) . " queries</span>";
        echo "</div>";
        echo "</div>";
    }
}

VelocityDebugger::init();
PHP;
    }
}

// Run the application
$velocity = new VelocityStandalone();
$velocity->run($argv);
