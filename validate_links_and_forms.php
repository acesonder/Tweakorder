#!/usr/bin/env php
<?php
/**
 * Comprehensive Link and Form Validation Script
 * Validates all HTML pages, links, forms, and API integrations
 */

// Color codes for terminal output
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $CYAN = "\033[0;36m";
    public static $NC = "\033[0m";
}

class LinkAndFormValidator {
    private $baseDir;
    private $errors = [];
    private $warnings = [];
    private $passed = [];
    private $stats = [
        'html_files' => 0,
        'links_checked' => 0,
        'forms_found' => 0,
        'api_endpoints' => 0
    ];
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function run() {
        echo "\n" . Colors::$CYAN . "╔════════════════════════════════════════════════════════╗" . Colors::$NC . "\n";
        echo Colors::$CYAN . "║   Tweakorder - Link & Form Validation Report          ║" . Colors::$NC . "\n";
        echo Colors::$CYAN . "╚════════════════════════════════════════════════════════╝" . Colors::$NC . "\n\n";
        
        // Validate all HTML pages
        $this->validateHTMLPages();
        
        // Validate all links
        $this->validateAllLinks();
        
        // Validate forms and their API integrations
        $this->validateForms();
        
        // Validate API files
        $this->validateAPIFiles();
        
        // Check for common issues
        $this->checkCommonIssues();
        
        // Print summary
        $this->printSummary();
        
        // Generate detailed report
        $this->generateReport();
    }
    
    private function validateHTMLPages() {
        echo Colors::$BLUE . "\n▶ Validating HTML Pages Structure..." . Colors::$NC . "\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        $this->stats['html_files'] = count($htmlFiles);
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            
            if (!file_exists($file)) {
                $this->addError("File not found: $filename");
                continue;
            }
            
            $content = file_get_contents($file);
            
            // Check for basic HTML structure
            $checks = [
                'DOCTYPE' => (stripos($content, '<!DOCTYPE') !== false),
                '<html>' => (stripos($content, '<html') !== false),
                '<head>' => (stripos($content, '<head') !== false),
                '<body>' => (stripos($content, '<body') !== false),
                '</html>' => (stripos($content, '</html>') !== false),
            ];
            
            $allPassed = true;
            foreach ($checks as $check => $result) {
                if (!$result) {
                    $this->addWarning("Missing $check in $filename");
                    $allPassed = false;
                }
            }
            
            if ($allPassed) {
                $this->addPass("Valid HTML structure: $filename");
            }
            
            // Check for charset
            if (stripos($content, 'charset') === false) {
                $this->addWarning("No charset defined in $filename");
            }
            
            // Check for viewport meta tag (mobile responsiveness)
            if (stripos($content, 'viewport') === false) {
                $this->addWarning("No viewport meta tag in $filename (may affect mobile display)");
            }
        }
    }
    
    private function validateAllLinks() {
        echo "\n" . Colors::$BLUE . "▶ Validating Links..." . Colors::$NC . "\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            $content = file_get_contents($file);
            
            // Extract all href attributes
            preg_match_all('/href=["\']([^"\']+)["\']/', $content, $hrefMatches);
            $hrefs = $hrefMatches[1];
            
            // Extract all onclick navigateTo calls
            preg_match_all('/navigateTo\(["\']([^"\']+)["\']\)/', $content, $navMatches);
            $navigateLinks = $navMatches[1];
            
            // Extract all src attributes
            preg_match_all('/src=["\']([^"\']+)["\']/', $content, $srcMatches);
            $srcs = $srcMatches[1];
            
            // Check href links
            foreach ($hrefs as $link) {
                $this->stats['links_checked']++;
                
                // Skip external links, anchors, and special protocols
                if (preg_match('/^(http|https|mailto|tel|javascript|#)/', $link)) {
                    continue;
                }
                
                // Skip template literals and dynamic content
                if (strpos($link, '${') !== false || strpos($link, '{') !== false) {
                    continue;
                }
                
                // Skip API calls and dynamic links
                if (strpos($link, 'api/') !== false || strpos($link, '?') !== false) {
                    continue;
                }
                
                // Check CSS files
                if (preg_match('/\.css$/', $link)) {
                    $linkPath = $this->baseDir . '/' . $link;
                    if (!file_exists($linkPath)) {
                        $this->addError("Broken CSS link in $filename: $link");
                    } else {
                        $this->addPass("Valid CSS link: $link");
                    }
                    continue;
                }
                
                // Check if local HTML file exists
                $linkPath = $this->baseDir . '/' . $link;
                
                if (!file_exists($linkPath)) {
                    $this->addError("Broken link in $filename: $link → file not found");
                } else {
                    $this->addPass("Valid link: $link");
                }
            }
            
            // Check navigateTo links
            foreach ($navigateLinks as $link) {
                $this->stats['links_checked']++;
                $linkPath = $this->baseDir . '/' . $link;
                
                if (!file_exists($linkPath)) {
                    $this->addError("Broken navigateTo link in $filename: $link → file not found");
                } else {
                    $this->addPass("Valid navigateTo: $link");
                }
            }
            
            // Check src resources (JS, images)
            foreach ($srcs as $src) {
                // Skip external resources and data URIs
                if (preg_match('/^(http|https|\/\/|data:)/', $src)) {
                    continue;
                }
                
                // Skip template literals and dynamic content
                if (strpos($src, '${') !== false || strpos($src, '{') !== false) {
                    continue;
                }
                
                $srcPath = $this->baseDir . '/' . $src;
                
                if (!file_exists($srcPath)) {
                    $this->addWarning("Missing resource in $filename: $src");
                } else {
                    $this->addPass("Valid resource: $src");
                }
            }
        }
    }
    
    private function validateForms() {
        echo "\n" . Colors::$BLUE . "▶ Validating Forms..." . Colors::$NC . "\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            $content = file_get_contents($file);
            
            // Find all forms
            preg_match_all('/<form[^>]*id=["\']([^"\']+)["\'][^>]*>/', $content, $formMatches);
            $formIds = $formMatches[1];
            
            if (empty($formIds)) {
                continue; // No forms in this file
            }
            
            $this->stats['forms_found'] += count($formIds);
            
            foreach ($formIds as $formId) {
                $this->addPass("Found form in $filename: $formId");
                
                // Check if form has submit handler via addEventListener
                $pattern = "/getElementById\(['\"]" . preg_quote($formId, '/') . "['\"]\)\.addEventListener\(['\"]submit['\"]/";
                $hasEventListener = preg_match($pattern, $content);
                
                // Check if form has onsubmit attribute
                $pattern2 = "/<form[^>]*id=['\"]" . preg_quote($formId, '/') . "['\"][^>]*onsubmit=/";
                $hasOnsubmit = preg_match($pattern2, $content);
                
                if ($hasEventListener || $hasOnsubmit) {
                    $this->addPass("Form '$formId' has submit event handler");
                    
                    // Check if it calls an API
                    $formSection = $this->extractFormSection($content, $formId);
                    if ($formSection) {
                        if (preg_match('/ajax\(["\']([^"\']+)["\']/', $formSection, $apiMatch)) {
                            $apiEndpoint = $apiMatch[1];
                            $this->addPass("Form '$formId' submits to: $apiEndpoint");
                            
                            // Verify API endpoint exists
                            $apiPath = $this->baseDir . '/' . $apiEndpoint;
                            if (!file_exists($apiPath)) {
                                $this->addError("API endpoint missing for form '$formId': $apiEndpoint");
                            } else {
                                $this->addPass("API endpoint exists: $apiEndpoint");
                            }
                        } else if (preg_match('/fetch\(["\']([^"\']+)["\']/', $formSection, $fetchMatch)) {
                            $apiEndpoint = $fetchMatch[1];
                            $this->addPass("Form '$formId' fetches: $apiEndpoint");
                        }
                    }
                } else {
                    $this->addWarning("Form '$formId' in $filename may not have proper submit handler");
                }
                
                // Check for required fields
                if (preg_match('/<input[^>]*required[^>]*>/', $content)) {
                    $this->addPass("Form '$formId' has required field validation");
                }
            }
        }
    }
    
    private function extractFormSection($content, $formId) {
        // Extract the script section that handles this form
        $pattern = "/getElementById\(['\"]" . preg_quote($formId, '/') . "['\"]\)[^;]+;/s";
        if (preg_match($pattern, $content, $match)) {
            return $match[0];
        }
        
        // Try to find broader section
        $pattern = "/getElementById\(['\"]" . preg_quote($formId, '/') . "['\"][\s\S]{0,1000}/";
        if (preg_match($pattern, $content, $match)) {
            return $match[0];
        }
        
        return null;
    }
    
    private function validateAPIFiles() {
        echo "\n" . Colors::$BLUE . "▶ Validating API Files..." . Colors::$NC . "\n";
        
        $apiFiles = glob($this->baseDir . '/api/*.php');
        $this->stats['api_endpoints'] = count($apiFiles);
        
        foreach ($apiFiles as $file) {
            $filename = basename($file);
            
            // Validate file is within expected directory to prevent path traversal
            $realPath = realpath($file);
            $expectedPath = realpath($this->baseDir . '/api');
            if ($realPath === false || strpos($realPath, $expectedPath) !== 0) {
                $this->addError("Security: Invalid file path detected: $filename");
                continue;
            }
            
            // Check PHP syntax
            $output = [];
            $return_var = 0;
            exec("php -l " . escapeshellarg($realPath) . " 2>&1", $output, $return_var);
            
            if ($return_var !== 0) {
                $this->addError("PHP syntax error in $filename: " . implode("\n", $output));
            } else {
                $this->addPass("PHP syntax valid: api/$filename");
            }
            
            $content = file_get_contents($file);
            
            // Check for database connection
            if (strpos($content, 'database.php') !== false || 
                strpos($content, 'getDBConnection') !== false || 
                strpos($content, 'getConnection') !== false) {
                $this->addPass("API $filename includes database connection");
            } else {
                $this->addWarning("API $filename may not have database connection");
            }
            
            // Check for prepared statements (security)
            if (strpos($content, 'prepare(') !== false || strpos($content, 'prepare (') !== false) {
                $this->addPass("API $filename uses prepared statements (secure)");
            } else if (strpos($content, 'query(') !== false) {
                $this->addWarning("API $filename uses direct queries (check for SQL injection risk)");
            }
            
            // Check for JSON response
            if (strpos($content, 'json_encode') !== false) {
                $this->addPass("API $filename returns JSON response");
            }
            
            // Check for request method handling
            $methods = ['GET', 'POST', 'PUT', 'DELETE'];
            $handledMethods = [];
            foreach ($methods as $method) {
                if (preg_match('/case\s+["\']' . $method . '["\']/', $content) || 
                    preg_match('/REQUEST_METHOD.*==.*["\']' . $method . '["\']/', $content)) {
                    $handledMethods[] = $method;
                }
            }
            
            if (!empty($handledMethods)) {
                $this->addPass("API $filename handles: " . implode(', ', $handledMethods));
            }
        }
    }
    
    private function checkCommonIssues() {
        echo "\n" . Colors::$BLUE . "▶ Checking for Common Issues..." . Colors::$NC . "\n";
        
        // Check if assets directory exists
        $assetsDir = $this->baseDir . '/assets';
        if (!is_dir($assetsDir)) {
            $this->addError("Assets directory not found: assets/");
        } else {
            $this->addPass("Assets directory exists");
            
            // Check subdirectories
            $subdirs = ['css', 'js', 'uploads'];
            foreach ($subdirs as $subdir) {
                $path = $assetsDir . '/' . $subdir;
                if (!is_dir($path)) {
                    $this->addWarning("Assets subdirectory not found: assets/$subdir/");
                } else {
                    $this->addPass("Assets subdirectory exists: assets/$subdir/");
                }
            }
        }
        
        // Check config directory
        $configDir = $this->baseDir . '/config';
        if (!is_dir($configDir)) {
            $this->addError("Config directory not found");
        } else {
            $this->addPass("Config directory exists");
            
            // Check database config
            $dbConfig = $configDir . '/database.php';
            if (!file_exists($dbConfig)) {
                $this->addError("Database config not found: config/database.php");
            } else {
                $this->addPass("Database config exists");
            }
        }
        
        // Check for app.js (main JavaScript file)
        $appJs = $this->baseDir . '/assets/js/app.js';
        if (!file_exists($appJs)) {
            $this->addWarning("Main app.js not found");
        } else {
            $this->addPass("Main app.js exists");
            
            // Check for essential functions
            $jsContent = file_get_contents($appJs);
            $functions = ['navigateTo', 'ajax', 'showAlert'];
            foreach ($functions as $func) {
                if (strpos($jsContent, "function $func") !== false || 
                    strpos($jsContent, "$func = function") !== false ||
                    strpos($jsContent, "const $func") !== false) {
                    $this->addPass("Function defined in app.js: $func()");
                } else {
                    $this->addWarning("Function may be missing in app.js: $func()");
                }
            }
        }
    }
    
    private function addError($message) {
        $this->errors[] = $message;
        echo Colors::$RED . "  ✗ " . $message . Colors::$NC . "\n";
    }
    
    private function addWarning($message) {
        $this->warnings[] = $message;
        echo Colors::$YELLOW . "  ⚠ " . $message . Colors::$NC . "\n";
    }
    
    private function addPass($message) {
        $this->passed[] = $message;
        // Suppress detailed pass messages to reduce noise
        // echo Colors::$GREEN . "  ✓ " . $message . Colors::$NC . "\n";
    }
    
    private function printSummary() {
        echo "\n" . Colors::$CYAN . "╔════════════════════════════════════════════════════════╗" . Colors::$NC . "\n";
        echo Colors::$CYAN . "║                  Validation Summary                    ║" . Colors::$NC . "\n";
        echo Colors::$CYAN . "╚════════════════════════════════════════════════════════╝" . Colors::$NC . "\n\n";
        
        echo Colors::$BLUE . "Statistics:" . Colors::$NC . "\n";
        echo "  HTML Files: " . $this->stats['html_files'] . "\n";
        echo "  Links Checked: " . $this->stats['links_checked'] . "\n";
        echo "  Forms Found: " . $this->stats['forms_found'] . "\n";
        echo "  API Endpoints: " . $this->stats['api_endpoints'] . "\n\n";
        
        echo Colors::$BLUE . "Results:" . Colors::$NC . "\n";
        echo Colors::$GREEN . "  ✓ Passed: " . count($this->passed) . Colors::$NC . "\n";
        echo Colors::$YELLOW . "  ⚠ Warnings: " . count($this->warnings) . Colors::$NC . "\n";
        echo Colors::$RED . "  ✗ Errors: " . count($this->errors) . Colors::$NC . "\n\n";
        
        if (count($this->errors) === 0 && count($this->warnings) === 0) {
            echo Colors::$GREEN . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n";
            echo Colors::$GREEN . "   ✓✓✓ All validation checks passed! ✓✓✓" . Colors::$NC . "\n";
            echo Colors::$GREEN . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n\n";
        } else if (count($this->errors) === 0) {
            echo Colors::$YELLOW . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n";
            echo Colors::$YELLOW . "   ⚠ Validation passed with warnings" . Colors::$NC . "\n";
            echo Colors::$YELLOW . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n\n";
        } else {
            echo Colors::$RED . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n";
            echo Colors::$RED . "   ✗ Validation found errors that need fixing" . Colors::$NC . "\n";
            echo Colors::$RED . "═══════════════════════════════════════════════════════" . Colors::$NC . "\n\n";
        }
    }
    
    private function generateReport() {
        $reportFile = $this->baseDir . '/VALIDATION_REPORT.md';
        
        $report = "# System Validation Report\n\n";
        $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";
        
        $report .= "## Statistics\n\n";
        $report .= "- **HTML Files:** " . $this->stats['html_files'] . "\n";
        $report .= "- **Links Checked:** " . $this->stats['links_checked'] . "\n";
        $report .= "- **Forms Found:** " . $this->stats['forms_found'] . "\n";
        $report .= "- **API Endpoints:** " . $this->stats['api_endpoints'] . "\n\n";
        
        $report .= "## Summary\n\n";
        $report .= "- ✓ **Passed:** " . count($this->passed) . "\n";
        $report .= "- ⚠ **Warnings:** " . count($this->warnings) . "\n";
        $report .= "- ✗ **Errors:** " . count($this->errors) . "\n\n";
        
        if (!empty($this->errors)) {
            $report .= "## Errors Found\n\n";
            foreach ($this->errors as $error) {
                $report .= "- ✗ " . $error . "\n";
            }
            $report .= "\n";
        }
        
        if (!empty($this->warnings)) {
            $report .= "## Warnings\n\n";
            foreach ($this->warnings as $warning) {
                $report .= "- ⚠ " . $warning . "\n";
            }
            $report .= "\n";
        }
        
        if (count($this->errors) === 0 && count($this->warnings) === 0) {
            $report .= "## Status\n\n";
            $report .= "✅ **All validation checks passed!**\n\n";
            $report .= "All pages, links, and forms have been validated successfully.\n";
        }
        
        file_put_contents($reportFile, $report);
        
        echo Colors::$BLUE . "Detailed report saved to: VALIDATION_REPORT.md" . Colors::$NC . "\n\n";
    }
}

// Run the validator
$validator = new LinkAndFormValidator(__DIR__);
$validator->run();
