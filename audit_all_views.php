<?php
define('BASEPATH', 1);
define('ENVIRONMENT', 'development');

function scan_dir_recursive($dir) {
    $results = [];
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f == '.' || $f == '..') continue;
        $path = $dir . '/' . $f;
        if (is_dir($path)) {
            $results = array_merge($results, scan_dir_recursive($path));
        } else if (pathinfo($path, PATHINFO_EXTENSION) == 'php') {
            $results[] = $path;
        }
    }
    return $results;
}

$views = scan_dir_recursive('application/views');
echo "Found " . count($views) . " view files.\n";

$issues = [];

foreach ($views as $v) {
    $content = file_get_contents($v);
    
    // Check syntax
    $cmd = "php -l " . escapeshellarg($v);
    $output = shell_exec($cmd);
    if (strpos($output, 'No syntax errors') === false) {
        $issues[] = "[SYNTAX ERROR] $v: " . trim($output);
    }
    
    // Check for risky pattern: $formValues['upload_type'] without isset
    if (preg_match('/\$formValues\[[\'"](\w+)[\'"]\]\s*&&/', $content, $m)) {
        $issues[] = "[RISKY ARRAY KEY] $v: Uses \$formValues['{$m[1]}'] without isset()";
    }
    
    // Check for $view used before definition
    $lines = explode("\n", $content);
    $defined = false;
    foreach ($lines as $num => $line) {
        if (preg_match('/\$view\s*=/', $line)) {
            $defined = true;
        }
        if (preg_match('/<\?php\s+echo\s+\$view\b/', $line) || preg_match('/echo\s+\$view\b/', $line)) {
            if (!$defined) {
                $issues[] = "[UNDEFINED \$view] $v Line " . ($num + 1) . ": $line";
            }
        }
    }
}

if (empty($issues)) {
    echo "SUCCESS: Zero issues found across all " . count($views) . " view files!\n";
} else {
    echo "FOUND " . count($issues) . " ISSUES:\n";
    foreach ($issues as $iss) {
        echo "- " . $iss . "\n";
    }
}
