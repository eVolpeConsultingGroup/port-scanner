<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($argv[1]) || !file_exists($argv[1])) {
    exit("No data file provided\n");
}

$data = json_decode(file_get_contents($argv[1]), true);
unlink($argv[1]); // Remove the temporary file

if (!is_array($data)) {
    exit("JSON decoding error\n");
}

$report = "";

foreach ($data as $row) {
    $ip = $row['ip'];
    $name = $row['name'] ?? 'Unnamed';
    $excluded_ports = $row['excluded_ports'] ?? [];
    
    // Convert all port values to integers and filter valid ports
    $excluded_ports = array_filter(array_map('intval', $excluded_ports), function($port) {
        return $port > 0 && $port <= 65535;
    });
    
    $excluded_string = '';
    if (!empty($excluded_ports)) {
        $excluded_string = "--exclude-ports " . implode(',', array_unique($excluded_ports));
    }
    
    $command = "nmap -T5 -Pn -n -p 1-65535 $excluded_string --open --max-retries 1 --min-rate 1000 --max-scan-delay 20 --min-parallelism 100 $ip";
    exec($command, $output, $return_var);
    
    $open_ports = [];
    foreach ($output as $line) {
        if (preg_match('/(\d+)\/tcp\s+open/', $line, $matches)) {
            $open_ports[] = (int)$matches[1];
        }
    }
    
    if (!empty($open_ports)) {
        sort($open_ports);
        $report .= "**$ip ($name)**: Open ports: " . implode(', ', $open_ports) . "\n";
    }
    
    // Add error reporting
    if ($return_var !== 0) {
        $report .= "**$ip ($name)**: Scan failed with error code $return_var\n";
    }
    
    unset($output);
}

echo $report;
?>
