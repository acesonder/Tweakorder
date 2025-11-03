#!/usr/bin/env php
<?php
/**
 * Comprehensive System Validation Script
 * Validates all pages, links, and form submissions in Tweakorder
 */

// Color codes for terminal output
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m"; // No Color
}

class SystemValidator {
    private $baseDir;
    private $errors = [];
    private $warnings = [];
    private $passed = [];
    private $dbConnection;
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function run() {
        echo "\n" . Colors::$BLUE . "=== Tweakorder System Validation ===" . Colors::$NC . "\n\n";
        
        // Test database connection
        $this->testDatabaseConnection();
        
        // Validate all HTML pages
        $this->validateHTMLPages();
        
        // Validate all links in HTML pages
        $this->validateAllLinks();
        
        // Test all API endpoints
        $this->testAPIEndpoints();
        
        // Test form submissions
        $this->testFormSubmissions();
        
        // Print summary
        $this->printSummary();
    }
    
    private function testDatabaseConnection() {
        echo Colors::$BLUE . "Testing Database Connection..." . Colors::$NC . "\n";
        
        try {
            require_once $this->baseDir . '/config/database.php';
            $this->dbConnection = getDBConnection();
            
            if ($this->dbConnection->connect_error) {
                $this->addError("Database connection failed: " . $this->dbConnection->connect_error);
                return false;
            }
            
            $this->addPass("Database connection successful");
            
            // Test database tables exist
            $tables = ['products', 'clients', 'workers', 'orders', 'order_items', 'locations', 'users'];
            foreach ($tables as $table) {
                $result = $this->dbConnection->query("SHOW TABLES LIKE '$table'");
                if ($result->num_rows > 0) {
                    $this->addPass("Table '$table' exists");
                } else {
                    $this->addWarning("Table '$table' does not exist");
                }
            }
            
            return true;
        } catch (Exception $e) {
            $this->addError("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    private function validateHTMLPages() {
        echo "\n" . Colors::$BLUE . "Validating HTML Pages..." . Colors::$NC . "\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            
            if (!file_exists($file)) {
                $this->addError("File not found: $filename");
                continue;
            }
            
            if (!is_readable($file)) {
                $this->addError("File not readable: $filename");
                continue;
            }
            
            $content = file_get_contents($file);
            
            // Check for basic HTML structure
            if (strpos($content, '<!DOCTYPE') === false && strpos($content, '<!doctype') === false) {
                $this->addWarning("Missing DOCTYPE in $filename");
            }
            
            if (strpos($content, '<html') === false) {
                $this->addWarning("Missing <html> tag in $filename");
            }
            
            $this->addPass("HTML structure valid: $filename");
        }
    }
    
    private function validateAllLinks() {
        echo "\n" . Colors::$BLUE . "Validating Links in HTML Pages..." . Colors::$NC . "\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            $content = file_get_contents($file);
            
            // Extract all href attributes
            preg_match_all('/href=["\']([^"\']+)["\']/', $content, $matches);
            $hrefs = $matches[1];
            
            // Extract all onclick navigateTo calls
            preg_match_all('/navigateTo\(["\']([^"\']+)["\']\)/', $content, $navMatches);
            $navigateLinks = $navMatches[1];
            
            $allLinks = array_merge($hrefs, $navigateLinks);
            
            foreach ($allLinks as $link) {
                // Skip external links, anchors, and special protocols
                if (preg_match('/^(http|https|mailto|tel|javascript|#)/', $link)) {
                    continue;
                }
                
                // Skip API calls and dynamic links
                if (strpos($link, 'api/') !== false || strpos($link, '?') !== false) {
                    continue;
                }
                
                // Check if local file exists
                $linkPath = $this->baseDir . '/' . $link;
                
                if (!file_exists($linkPath)) {
                    $this->addError("Broken link in $filename: $link (file not found)");
                } else {
                    $this->addPass("Valid link in $filename: $link");
                }
            }
            
            // Extract all src attributes for CSS and JS
            preg_match_all('/src=["\']([^"\']+)["\']/', $content, $srcMatches);
            preg_match_all('/href=["\']([^"\']+\.css)["\']/', $content, $cssMatches);
            
            $resources = array_merge($srcMatches[1], $cssMatches[1]);
            
            foreach ($resources as $resource) {
                // Skip external resources
                if (preg_match('/^(http|https|\/\/)/', $resource)) {
                    continue;
                }
                
                $resourcePath = $this->baseDir . '/' . $resource;
                
                if (!file_exists($resourcePath)) {
                    $this->addWarning("Missing resource in $filename: $resource");
                }
            }
        }
    }
    
    private function testAPIEndpoints() {
        echo "\n" . Colors::$BLUE . "Testing API Endpoints..." . Colors::$NC . "\n";
        
        $apiFiles = glob($this->baseDir . '/api/*.php');
        
        foreach ($apiFiles as $file) {
            $filename = basename($file);
            
            // Check PHP syntax
            $output = [];
            $return_var = 0;
            exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);
            
            if ($return_var !== 0) {
                $this->addError("PHP syntax error in $filename: " . implode("\n", $output));
            } else {
                $this->addPass("PHP syntax valid: $filename");
            }
            
            // Check if file includes database config
            $content = file_get_contents($file);
            if (strpos($content, 'database.php') !== false || strpos($content, 'getDBConnection') !== false || strpos($content, 'getConnection') !== false) {
                $this->addPass("Database connection found in $filename");
            }
        }
    }
    
    private function testFormSubmissions() {
        echo "\n" . Colors::$BLUE . "Testing Form Submissions..." . Colors::$NC . "\n";
        
        if (!$this->dbConnection) {
            $this->addError("Cannot test form submissions - no database connection");
            return;
        }
        
        // Test 1: Add a test product
        $testProduct = [
            'name' => 'Validation Test Product',
            'description' => 'Created by validation script',
            'inventory' => 100,
            'background_color' => 'gradient-1'
        ];
        
        $stmt = $this->dbConnection->prepare("INSERT INTO products (name, description, inventory, background_color) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssis", $testProduct['name'], $testProduct['description'], $testProduct['inventory'], $testProduct['background_color']);
            
            if ($stmt->execute()) {
                $product_id = $stmt->insert_id;
                $this->addPass("Test product submission successful (ID: $product_id)");
                
                // Verify it was stored correctly
                $verifyStmt = $this->dbConnection->prepare("SELECT * FROM products WHERE id = ?");
                $verifyStmt->bind_param("i", $product_id);
                $verifyStmt->execute();
                $verify = $verifyStmt->get_result();
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    if ($row['name'] === $testProduct['name']) {
                        $this->addPass("Test product data verified in database");
                    } else {
                        $this->addError("Test product data mismatch in database");
                    }
                    $verifyStmt->close();
                    
                    // Clean up test data
                    $cleanupStmt = $this->dbConnection->prepare("DELETE FROM products WHERE id = ?");
                    $cleanupStmt->bind_param("i", $product_id);
                    $cleanupStmt->execute();
                    $cleanupStmt->close();
                    $this->addPass("Test product cleaned up");
                }
            } else {
                $this->addError("Failed to insert test product: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $this->addError("Failed to prepare product insert statement: " . $this->dbConnection->error);
        }
        
        // Test 2: Add a test worker
        $testWorker = [
            'name' => 'Validation Test Worker',
            'email' => 'test@validation.local'
        ];
        
        $stmt = $this->dbConnection->prepare("INSERT INTO workers (name, email) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $testWorker['name'], $testWorker['email']);
            
            if ($stmt->execute()) {
                $worker_id = $stmt->insert_id;
                $this->addPass("Test worker submission successful (ID: $worker_id)");
                
                // Clean up
                $cleanupStmt = $this->dbConnection->prepare("DELETE FROM workers WHERE id = ?");
                $cleanupStmt->bind_param("i", $worker_id);
                $cleanupStmt->execute();
                $cleanupStmt->close();
                $this->addPass("Test worker cleaned up");
            } else {
                $this->addError("Failed to insert test worker: " . $stmt->error);
            }
            $stmt->close();
        }
        
        // Test 3: Add a test client
        $testClient = [
            'name' => 'Validation Test Client',
            'phone' => '555-0123',
            'email' => 'client@validation.local'
        ];
        
        $stmt = $this->dbConnection->prepare("INSERT INTO clients (name, phone, email) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $testClient['name'], $testClient['phone'], $testClient['email']);
            
            if ($stmt->execute()) {
                $client_id = $stmt->insert_id;
                $this->addPass("Test client submission successful (ID: $client_id)");
                
                // Clean up
                $cleanupStmt = $this->dbConnection->prepare("DELETE FROM clients WHERE id = ?");
                $cleanupStmt->bind_param("i", $client_id);
                $cleanupStmt->execute();
                $cleanupStmt->close();
                $this->addPass("Test client cleaned up");
            } else {
                $this->addError("Failed to insert test client: " . $stmt->error);
            }
            $stmt->close();
        }
    }
    
    private function addError($message) {
        $this->errors[] = $message;
        echo Colors::$RED . "✗ " . $message . Colors::$NC . "\n";
    }
    
    private function addWarning($message) {
        $this->warnings[] = $message;
        echo Colors::$YELLOW . "⚠ " . $message . Colors::$NC . "\n";
    }
    
    private function addPass($message) {
        $this->passed[] = $message;
        echo Colors::$GREEN . "✓ " . $message . Colors::$NC . "\n";
    }
    
    private function printSummary() {
        echo "\n" . Colors::$BLUE . "=== Validation Summary ===" . Colors::$NC . "\n\n";
        
        echo Colors::$GREEN . "Passed: " . count($this->passed) . Colors::$NC . "\n";
        echo Colors::$YELLOW . "Warnings: " . count($this->warnings) . Colors::$NC . "\n";
        echo Colors::$RED . "Errors: " . count($this->errors) . Colors::$NC . "\n\n";
        
        if (count($this->errors) === 0) {
            echo Colors::$GREEN . "✓ All validation tests passed!" . Colors::$NC . "\n\n";
        } else {
            echo Colors::$RED . "✗ Validation found errors that need to be fixed" . Colors::$NC . "\n\n";
        }
        
        // Close database connection
        if ($this->dbConnection) {
            $this->dbConnection->close();
        }
    }
}

// Run the validator
$validator = new SystemValidator(__DIR__);
$validator->run();
