#!/usr/bin/env php
<?php
/**
 * Database Form Submission Testing Script
 * Tests all form submissions to database
 */

// Color codes
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $CYAN = "\033[0;36m";
    public static $NC = "\033[0m";
}

class DatabaseFormTester {
    private $conn;
    private $pdo;
    private $baseDir;
    private $passed = [];
    private $failed = [];
    private $skipped = [];
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function run() {
        echo "\n" . Colors::$CYAN . "╔════════════════════════════════════════════════════════╗" . Colors::$NC . "\n";
        echo Colors::$CYAN . "║        Database Form Submission Testing                ║" . Colors::$NC . "\n";
        echo Colors::$CYAN . "╚════════════════════════════════════════════════════════╝" . Colors::$NC . "\n\n";
        
        // Test database connection
        if (!$this->connectToDatabase()) {
            echo Colors::$RED . "Cannot proceed without database connection." . Colors::$NC . "\n";
            echo Colors::$YELLOW . "Please ensure MySQL is running and database is configured." . Colors::$NC . "\n\n";
            return;
        }
        
        // Test all form submissions
        $this->testProductSubmission();
        $this->testWorkerSubmission();
        $this->testClientSubmission();
        $this->testOrderSubmission();
        $this->testCaseNoteSubmission();
        
        // Print summary
        $this->printSummary();
    }
    
    private function connectToDatabase() {
        echo Colors::$BLUE . "▶ Testing Database Connection..." . Colors::$NC . "\n";
        
        try {
            require_once $this->baseDir . '/config/database.php';
            
            // Try MySQLi connection
            $this->conn = getDBConnection();
            
            if ($this->conn->connect_error) {
                echo Colors::$RED . "  ✗ MySQLi connection failed: " . $this->conn->connect_error . Colors::$NC . "\n";
                return false;
            }
            
            echo Colors::$GREEN . "  ✓ MySQLi connection successful" . Colors::$NC . "\n";
            
            // Try PDO connection
            $this->pdo = getConnection();
            echo Colors::$GREEN . "  ✓ PDO connection successful" . Colors::$NC . "\n";
            
            // Verify database exists
            $result = $this->conn->query("SELECT DATABASE()");
            $row = $result->fetch_row();
            echo Colors::$GREEN . "  ✓ Connected to database: " . $row[0] . Colors::$NC . "\n\n";
            
            return true;
            
        } catch (Exception $e) {
            echo Colors::$RED . "  ✗ Database connection failed: " . $e->getMessage() . Colors::$NC . "\n\n";
            return false;
        }
    }
    
    private function testProductSubmission() {
        echo Colors::$BLUE . "▶ Testing Product Form Submission..." . Colors::$NC . "\n";
        
        try {
            // Test data
            $testProduct = [
                'name' => 'Test Product ' . time(),
                'description' => 'This is a test product created by validation script',
                'inventory' => 50,
                'background_color' => 'gradient-3'
            ];
            
            // Insert test product
            $stmt = $this->conn->prepare("INSERT INTO products (name, description, inventory, background_color) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $testProduct['name'], $testProduct['description'], $testProduct['inventory'], $testProduct['background_color']);
            
            if ($stmt->execute()) {
                $productId = $stmt->insert_id;
                echo Colors::$GREEN . "  ✓ Product inserted successfully (ID: $productId)" . Colors::$NC . "\n";
                
                // Verify data was stored correctly
                $verify = $this->conn->query("SELECT * FROM products WHERE id = $productId");
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    
                    $errors = [];
                    if ($row['name'] !== $testProduct['name']) $errors[] = "name mismatch";
                    if ($row['description'] !== $testProduct['description']) $errors[] = "description mismatch";
                    if ($row['inventory'] != $testProduct['inventory']) $errors[] = "inventory mismatch";
                    if ($row['background_color'] !== $testProduct['background_color']) $errors[] = "background_color mismatch";
                    
                    if (empty($errors)) {
                        echo Colors::$GREEN . "  ✓ All product data verified correctly in database" . Colors::$NC . "\n";
                        $this->passed[] = "Product submission and validation";
                    } else {
                        echo Colors::$RED . "  ✗ Data verification failed: " . implode(', ', $errors) . Colors::$NC . "\n";
                        $this->failed[] = "Product data verification: " . implode(', ', $errors);
                    }
                } else {
                    echo Colors::$RED . "  ✗ Could not retrieve inserted product" . Colors::$NC . "\n";
                    $this->failed[] = "Product retrieval after insert";
                }
                
                // Cleanup
                $this->conn->query("DELETE FROM products WHERE id = $productId");
                echo Colors::$GREEN . "  ✓ Test data cleaned up" . Colors::$NC . "\n";
                
            } else {
                echo Colors::$RED . "  ✗ Failed to insert product: " . $stmt->error . Colors::$NC . "\n";
                $this->failed[] = "Product insertion: " . $stmt->error;
            }
            
            $stmt->close();
            echo "\n";
            
        } catch (Exception $e) {
            echo Colors::$RED . "  ✗ Exception: " . $e->getMessage() . Colors::$NC . "\n\n";
            $this->failed[] = "Product test exception: " . $e->getMessage();
        }
    }
    
    private function testWorkerSubmission() {
        echo Colors::$BLUE . "▶ Testing Worker Form Submission..." . Colors::$NC . "\n";
        
        try {
            $testWorker = [
                'name' => 'Test Worker ' . time(),
                'email' => 'test' . time() . '@validation.local'
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO workers (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $testWorker['name'], $testWorker['email']);
            
            if ($stmt->execute()) {
                $workerId = $stmt->insert_id;
                echo Colors::$GREEN . "  ✓ Worker inserted successfully (ID: $workerId)" . Colors::$NC . "\n";
                
                // Verify
                $verify = $this->conn->query("SELECT * FROM workers WHERE id = $workerId");
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    if ($row['name'] === $testWorker['name'] && $row['email'] === $testWorker['email']) {
                        echo Colors::$GREEN . "  ✓ Worker data verified in database" . Colors::$NC . "\n";
                        $this->passed[] = "Worker submission and validation";
                    } else {
                        echo Colors::$RED . "  ✗ Worker data mismatch" . Colors::$NC . "\n";
                        $this->failed[] = "Worker data verification";
                    }
                }
                
                // Cleanup
                $this->conn->query("DELETE FROM workers WHERE id = $workerId");
                echo Colors::$GREEN . "  ✓ Test data cleaned up" . Colors::$NC . "\n";
                
            } else {
                echo Colors::$RED . "  ✗ Failed to insert worker: " . $stmt->error . Colors::$NC . "\n";
                $this->failed[] = "Worker insertion: " . $stmt->error;
            }
            
            $stmt->close();
            echo "\n";
            
        } catch (Exception $e) {
            echo Colors::$RED . "  ✗ Exception: " . $e->getMessage() . Colors::$NC . "\n\n";
            $this->failed[] = "Worker test exception: " . $e->getMessage();
        }
    }
    
    private function testClientSubmission() {
        echo Colors::$BLUE . "▶ Testing Client Form Submission..." . Colors::$NC . "\n";
        
        try {
            $testClient = [
                'name' => 'Test Client ' . time(),
                'phone' => '555-' . rand(1000, 9999),
                'email' => 'client' . time() . '@validation.local',
                'address' => '123 Test St'
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO clients (name, phone, email, address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $testClient['name'], $testClient['phone'], $testClient['email'], $testClient['address']);
            
            if ($stmt->execute()) {
                $clientId = $stmt->insert_id;
                echo Colors::$GREEN . "  ✓ Client inserted successfully (ID: $clientId)" . Colors::$NC . "\n";
                
                // Verify
                $verify = $this->conn->query("SELECT * FROM clients WHERE id = $clientId");
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    if ($row['name'] === $testClient['name'] && $row['phone'] === $testClient['phone']) {
                        echo Colors::$GREEN . "  ✓ Client data verified in database" . Colors::$NC . "\n";
                        $this->passed[] = "Client submission and validation";
                    } else {
                        echo Colors::$RED . "  ✗ Client data mismatch" . Colors::$NC . "\n";
                        $this->failed[] = "Client data verification";
                    }
                }
                
                // Cleanup
                $this->conn->query("DELETE FROM clients WHERE id = $clientId");
                echo Colors::$GREEN . "  ✓ Test data cleaned up" . Colors::$NC . "\n";
                
            } else {
                echo Colors::$RED . "  ✗ Failed to insert client: " . $stmt->error . Colors::$NC . "\n";
                $this->failed[] = "Client insertion: " . $stmt->error;
            }
            
            $stmt->close();
            echo "\n";
            
        } catch (Exception $e) {
            echo Colors::$RED . "  ✗ Exception: " . $e->getMessage() . Colors::$NC . "\n\n";
            $this->failed[] = "Client test exception: " . $e->getMessage();
        }
    }
    
    private function testOrderSubmission() {
        echo Colors::$BLUE . "▶ Testing Order Form Submission..." . Colors::$NC . "\n";
        
        try {
            // First create a test client
            $testClient = ['name' => 'Order Test Client', 'phone' => '555-TEST'];
            $stmt = $this->conn->prepare("INSERT INTO clients (name, phone) VALUES (?, ?)");
            $stmt->bind_param("ss", $testClient['name'], $testClient['phone']);
            $stmt->execute();
            $clientId = $stmt->insert_id;
            $stmt->close();
            
            // Create test order
            $testOrder = [
                'client_id' => $clientId,
                'status' => 'processing',
                'total_items' => 2
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO orders (client_id, status, total_items) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $testOrder['client_id'], $testOrder['status'], $testOrder['total_items']);
            
            if ($stmt->execute()) {
                $orderId = $stmt->insert_id;
                echo Colors::$GREEN . "  ✓ Order inserted successfully (ID: $orderId)" . Colors::$NC . "\n";
                
                // Verify
                $verify = $this->conn->query("SELECT * FROM orders WHERE id = $orderId");
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    if ($row['client_id'] == $testOrder['client_id'] && $row['status'] === $testOrder['status']) {
                        echo Colors::$GREEN . "  ✓ Order data verified in database" . Colors::$NC . "\n";
                        $this->passed[] = "Order submission and validation";
                    } else {
                        echo Colors::$RED . "  ✗ Order data mismatch" . Colors::$NC . "\n";
                        $this->failed[] = "Order data verification";
                    }
                }
                
                // Cleanup
                $this->conn->query("DELETE FROM orders WHERE id = $orderId");
                echo Colors::$GREEN . "  ✓ Test order cleaned up" . Colors::$NC . "\n";
                
            } else {
                echo Colors::$RED . "  ✗ Failed to insert order: " . $stmt->error . Colors::$NC . "\n";
                $this->failed[] = "Order insertion: " . $stmt->error;
            }
            
            $stmt->close();
            
            // Cleanup client
            $this->conn->query("DELETE FROM clients WHERE id = $clientId");
            echo "\n";
            
        } catch (Exception $e) {
            echo Colors::$RED . "  ✗ Exception: " . $e->getMessage() . Colors::$NC . "\n\n";
            $this->failed[] = "Order test exception: " . $e->getMessage();
        }
    }
    
    private function testCaseNoteSubmission() {
        echo Colors::$BLUE . "▶ Testing Case Note Form Submission..." . Colors::$NC . "\n";
        
        try {
            // Check if case_notes table exists
            $tableCheck = $this->conn->query("SHOW TABLES LIKE 'case_notes'");
            if ($tableCheck->num_rows == 0) {
                echo Colors::$YELLOW . "  ⚠ Table 'case_notes' not found - skipping test" . Colors::$NC . "\n\n";
                $this->skipped[] = "Case note submission (table not found)";
                return;
            }
            
            // Create test client
            $stmt = $this->conn->prepare("INSERT INTO clients (name, phone) VALUES (?, ?)");
            $name = 'Case Note Test Client';
            $phone = '555-CASE';
            $stmt->bind_param("ss", $name, $phone);
            $stmt->execute();
            $clientId = $stmt->insert_id;
            $stmt->close();
            
            // Create test case note
            $testNote = [
                'client_id' => $clientId,
                'note_text' => 'This is a test case note',
                'category' => 'General',
                'is_confidential' => 0
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO case_notes (client_id, note_text, category, is_confidential) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $testNote['client_id'], $testNote['note_text'], $testNote['category'], $testNote['is_confidential']);
            
            if ($stmt->execute()) {
                $noteId = $stmt->insert_id;
                echo Colors::$GREEN . "  ✓ Case note inserted successfully (ID: $noteId)" . Colors::$NC . "\n";
                
                // Verify
                $verify = $this->conn->query("SELECT * FROM case_notes WHERE id = $noteId");
                if ($verify && $verify->num_rows > 0) {
                    $row = $verify->fetch_assoc();
                    if ($row['client_id'] == $testNote['client_id'] && $row['note_text'] === $testNote['note_text']) {
                        echo Colors::$GREEN . "  ✓ Case note data verified in database" . Colors::$NC . "\n";
                        $this->passed[] = "Case note submission and validation";
                    }
                }
                
                // Cleanup
                $this->conn->query("DELETE FROM case_notes WHERE id = $noteId");
                echo Colors::$GREEN . "  ✓ Test case note cleaned up" . Colors::$NC . "\n";
            }
            
            $stmt->close();
            $this->conn->query("DELETE FROM clients WHERE id = $clientId");
            echo "\n";
            
        } catch (Exception $e) {
            echo Colors::$YELLOW . "  ⚠ " . $e->getMessage() . Colors::$NC . "\n\n";
            $this->skipped[] = "Case note test: " . $e->getMessage();
        }
    }
    
    private function printSummary() {
        echo Colors::$CYAN . "╔════════════════════════════════════════════════════════╗" . Colors::$NC . "\n";
        echo Colors::$CYAN . "║              Database Testing Summary                  ║" . Colors::$NC . "\n";
        echo Colors::$CYAN . "╚════════════════════════════════════════════════════════╝" . Colors::$NC . "\n\n";
        
        echo Colors::$GREEN . "  ✓ Passed: " . count($this->passed) . Colors::$NC . "\n";
        echo Colors::$RED . "  ✗ Failed: " . count($this->failed) . Colors::$NC . "\n";
        echo Colors::$YELLOW . "  ⊘ Skipped: " . count($this->skipped) . Colors::$NC . "\n\n";
        
        if (count($this->failed) === 0) {
            echo Colors::$GREEN . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n";
            echo Colors::$GREEN . "   ✓✓✓ All database tests passed! ✓✓✓" . Colors::$NC . "\n";
            echo Colors::$GREEN . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n\n";
        } else {
            echo Colors::$RED . "Failed tests:" . Colors::$NC . "\n";
            foreach ($this->failed as $failure) {
                echo Colors::$RED . "  - " . $failure . Colors::$NC . "\n";
            }
            echo "\n";
        }
        
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Run the tester
$tester = new DatabaseFormTester(__DIR__);
$tester->run();
