<?php
/**
 * Application Configuration
 */
return [
  // Application name
  'name' => $_ENV['APP_NAME'] ?: 'Simple MVC Framework',
  // Application environment (development, testing, production)
  'env' => $_ENV['APP_ENV'] ?: 'development',
  // Application debug mode
  'debug' => $_ENV['APP_DEBUG'] === 'true' ? true : false,
  // Application URL
  'url' => $_ENV['APP_URL'] ?: 'http://localhost:8000', 
  // Application timezone
  'timezone' => 'UTC',
  // Application locale
  'locale' => 'en',
  // Encryption key
  'key' => $_ENV'APP_KEY'] ?: 'base64:'.base64_encode(random_bytes(32)),
  // Autoloaded service providers
  'providers' => [
    // Add your service providers here
  ],

  // Class aliases
  'aliases' => [
    // Add your class aliases here
  ],

  // Session configuration
  'session' => [
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => __DIR__ . '/../storage/sessions',
    'cookie' => 'blogz_session',
    'path' => '/',
    'domain' => null,
    'secure' => isset($_SERVER['HTTPS']),
    'http_only' => true,
    'same_site' => 'lax',
  ],

  // Cache configuration
  'cache' => [
    'driver' => 'file',
    'path' => getRoot().'/../storage/cache',
  ],

  // Logging configuration
  'logging' => [
    'channel' => $_ENV['LOG_CHANNEL'] ?: 'stack',
    'path' => getRoot().'/../storage/logs/app.log',
    'level' => $_ENV'LOG_LEVEL'] ?: 'debug',
  ],
];
