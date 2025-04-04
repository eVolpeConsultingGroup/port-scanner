<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/env_loader.php';

$db = new SQLite3('monitoring.db');
$results = $db->query("SELECT * FROM ip_list");
$entries = [];

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    // Decode JSON ports to array
    $row['excluded_ports'] = json_decode($row['excluded_ports'] ?? '[]', true) ?? [];
    $entries[] = $row;
}

$thread_count = 50;
$chunks = array_chunk($entries, ceil(count($entries) / max($thread_count, 1)));
$processes = [];

foreach ($chunks as $index => $chunk) {
    if (empty($chunk)) continue;
    
    $data_file = "/tmp/scan_data_$index.json";
    file_put_contents($data_file, json_encode($chunk));
    
    $output_file = "/tmp/scan_report_$index.txt";
    $error_file = "/tmp/scan_error_$index.log";
    
    $cmd = "/usr/local/bin/php cron_worker.php $data_file > $output_file 2> $error_file &";
    shell_exec($cmd);
    $processes[] = [
        'output' => $output_file,
        'error' => $error_file,
        'ip_list' => array_map(fn($entry) => "{$entry['ip']} ({$entry['name']})", $chunk)
    ];
}

$max_wait_time = 300;
$start_time = time();
while (time() - $start_time < $max_wait_time) {
    $all_done = true;
    foreach ($processes as $process) {
        if (!file_exists($process['output']) || filesize($process['output']) === 0) {
            $all_done = false;
            break;
        }
    }
    if ($all_done) break;
    
    if (time() - $start_time >= $max_wait_time) {
        error_log("Timeout: Some scanning processes did not finish on time.");
        break;
    }
    usleep(500000);
}

$report = "### Port scanning results:\n";
$files_processed = 0;
foreach ($processes as $process) {
    if (file_exists($process['output'])) {
        $content = file_get_contents($process['output']);
        if (!empty(trim($content))) {
            $report .= $content;
        }
        unlink($process['output']);
        $files_processed++;
    }
}

if ($files_processed === 0) {
    $report .= "\n⚠️ No scanning results – check if the processes finished correctly.\n";
}

foreach ($processes as $process) {
    if (file_exists($process['error']) && filesize($process['error']) > 0) {
        $error_content = file_get_contents($process['error']);
        
        if (strpos($error_content, 'Skipping this scan type.') !== false) {
            unlink($process['error']);
            continue;
        }
        // Fixed error message translation
        $report .= "\n### Errors for IP: " . implode(", ", $process['ip_list']) . "\n";
        $report .= $error_content . "\n";
    }
    unlink($process['error']);
}

if (trim($report) !== "### Port scanning results:\n") {
    $webhook_url = getenv('WEBHOOK_URL');
    
    $payload = json_encode(["text" => $report]);
    
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    
    if ($response === false) {
        error_log('Webhook error: ' . curl_error($ch));
    }
    
    curl_close($ch);
}
?>
