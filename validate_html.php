#!/usr/bin/env php
<?php
/**
 * Comprehensive HTML and Link Validator
 * Validates all HTML pages, checks for broken links, and verifies form actions
 */

class HTMLValidator {
    private $baseDir;
    private $errors = [];
    private $warnings = [];
    private $passed = [];
    private $links = [];
    private $forms = [];
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function run() {
        echo "\n╔════════════════════════════════════════════════════════╗\n";
        echo "║   Tweakorder - HTML & Link Validation Report          ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";
        
        $this->validateAllHTMLPages();
        $this->validateAllLinks();
        $this->validateAllForms();
        $this->printSummary();
        $this->generateReport();
    }
    
    private function validateAllHTMLPages() {
        echo "▶ Validating HTML Pages...\n";
        
        $htmlFiles = glob($this->baseDir . '/*.html');
        
        foreach ($htmlFiles as $file) {
            $filename = basename($file);
            $this->validateHTMLPage($file);
        }
        
        echo "  ✓ Validated " . count($htmlFiles) . " HTML pages\n\n";
    }
    
    private function validateHTMLPage($filepath) {
        $filename = basename($filepath);
        
        if (!file_exists($filepath)) {
            $this->addError("File not found: $filename");
            return;
        }
        
        $content = file_get_contents($filepath);
        
        // Check basic HTML structure
        $checks = [
            'DOCTYPE' => stripos($content, '<!DOCTYPE') !== false,
            '<html>' => stripos($content, '<html') !== false,
            '<head>' => stripos($content, '<head') !== false,
            '<body>' => stripos($content, '<body') !== false,
            '</html>' => stripos($content, '</html>') !== false,
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
        
        // Check for viewport meta tag (important for mobile)
        if (stripos($content, 'viewport') === false) {
            $this->addWarning("No viewport meta tag in $filename");
        }
        
        // Check for title tag
        if (!preg_match('/<title>.*?<\/title>/i', $content)) {
            $this->addWarning("No title tag in $filename");
        }
        
        // Extract all links
        $this->extractLinks($filename, $content);
        
        // Extract all forms
        $this->extractForms($filename, $content);
    }
    
    private function extractLinks($filename, $content) {
        // Extract all href attributes
        preg_match_all('/href=["\']([^"\']+)["\']/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $link) {
                $this->links[] = [
                    'file' => $filename,
                    'link' => $link,
                    'type' => 'href'
                ];
            }
        }
        
        // Extract all src attributes
        preg_match_all('/src=["\']([^"\']+)["\']/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $link) {
                $this->links[] = [
                    'file' => $filename,
                    'link' => $link,
                    'type' => 'src'
                ];
            }
        }
    }
    
    private function extractForms($filename, $content) {
        // Extract all form actions
        preg_match_all('/<form[^>]+action=["\']([^"\']+)["\']/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $action) {
                $this->forms[] = [
                    'file' => $filename,
                    'action' => $action
                ];
            }
        }
    }
    
    private function validateAllLinks() {
        echo "▶ Validating Links...\n";
        
        $internalLinks = 0;
        $externalLinks = 0;
        $brokenLinks = 0;
        
        foreach ($this->links as $linkData) {
            $link = $linkData['link'];
            
            // Skip empty links, anchors, javascript, and data URIs
            if (empty($link) || $link === '#' || 
                strpos($link, 'javascript:') === 0 ||
                strpos($link, 'data:') === 0 ||
                strpos($link, 'mailto:') === 0 ||
                strpos($link, 'tel:') === 0) {
                continue;
            }
            
            // Check if external link
            if (strpos($link, 'http://') === 0 || strpos($link, 'https://') === 0 || strpos($link, '//') === 0) {
                $externalLinks++;
                // Don't check external links to avoid network requests
                continue;
            }
            
            $internalLinks++;
            
            // Check internal file links
            if ($linkData['type'] === 'href' && !strpos($link, '?') && !strpos($link, '#')) {
                $filepath = $this->baseDir . '/' . ltrim($link, '/');
                
                if (!file_exists($filepath)) {
                    $this->addError("Broken link in {$linkData['file']}: $link");
                    $brokenLinks++;
                }
            }
            
            // Check CSS/JS files
            if ($linkData['type'] === 'src' || (strpos($link, '.css') !== false)) {
                $filepath = $this->baseDir . '/' . ltrim($link, '/');
                
                if (!file_exists($filepath)) {
                    $this->addWarning("Missing resource in {$linkData['file']}: $link");
                }
            }
        }
        
        echo "  ✓ Checked $internalLinks internal links and $externalLinks external links\n";
        if ($brokenLinks > 0) {
            echo "  ✗ Found $brokenLinks broken links\n";
        }
        echo "\n";
    }
    
    private function validateAllForms() {
        echo "▶ Validating Forms...\n";
        
        $formCount = count($this->forms);
        $missingActions = 0;
        
        foreach ($this->forms as $formData) {
            $action = $formData['action'];
            
            // Skip empty actions
            if (empty($action) || $action === '#') {
                $missingActions++;
                continue;
            }
            
            // Check if action file exists
            if (strpos($action, 'api/') === 0) {
                $filepath = $this->baseDir . '/' . $action;
                
                if (!file_exists($filepath)) {
                    $this->addError("Missing form action in {$formData['file']}: $action");
                }
            }
        }
        
        echo "  ✓ Found $formCount forms\n";
        if ($missingActions > 0) {
            echo "  ! $missingActions forms with empty actions\n";
        }
        echo "\n";
    }
    
    private function addError($message) {
        $this->errors[] = $message;
    }
    
    private function addWarning($message) {
        $this->warnings[] = $message;
    }
    
    private function addPass($message) {
        $this->passed[] = $message;
    }
    
    private function printSummary() {
        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║                  Validation Summary                    ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";
        
        echo "✅ Passed: " . count($this->passed) . "\n";
        echo "⚠️  Warnings: " . count($this->warnings) . "\n";
        echo "❌ Errors: " . count($this->errors) . "\n\n";
        
        if (!empty($this->errors)) {
            echo "Errors:\n";
            foreach (array_slice($this->errors, 0, 20) as $error) {
                echo "  ❌ $error\n";
            }
            if (count($this->errors) > 20) {
                echo "  ... and " . (count($this->errors) - 20) . " more errors\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "Warnings:\n";
            foreach (array_slice($this->warnings, 0, 10) as $warning) {
                echo "  ⚠️  $warning\n";
            }
            if (count($this->warnings) > 10) {
                echo "  ... and " . (count($this->warnings) - 10) . " more warnings\n";
            }
            echo "\n";
        }
    }
    
    private function generateReport() {
        $report = "# Tweakorder HTML Validation Report\n\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $report .= "## Summary\n\n";
        $report .= "- ✅ Passed: " . count($this->passed) . "\n";
        $report .= "- ⚠️ Warnings: " . count($this->warnings) . "\n";
        $report .= "- ❌ Errors: " . count($this->errors) . "\n\n";
        
        if (!empty($this->errors)) {
            $report .= "## Errors\n\n";
            foreach ($this->errors as $error) {
                $report .= "- $error\n";
            }
            $report .= "\n";
        }
        
        if (!empty($this->warnings)) {
            $report .= "## Warnings\n\n";
            foreach ($this->warnings as $warning) {
                $report .= "- $warning\n";
            }
            $report .= "\n";
        }
        
        $reportFile = $this->baseDir . '/HTML_VALIDATION_REPORT.md';
        file_put_contents($reportFile, $report);
        
        echo "📄 Full report saved to: HTML_VALIDATION_REPORT.md\n\n";
    }
}

// Run the validator
$validator = new HTMLValidator(__DIR__);
$validator->run();
?>
