<?php
$db = new SQLite3('monitoring.db');
$db->exec("CREATE TABLE IF NOT EXISTS ip_list (id INTEGER PRIMARY KEY, ip TEXT, name TEXT, excluded_ports TEXT)");

function getEntries($db) {
    $results = $db->query("SELECT * FROM ip_list");
    $entries = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $entries[] = $row;
    }
    return $entries;
}

function addEntry($db, $ip, $name, $excluded_ports) {
    // Encode array to JSON before saving
    $excluded_ports_json = json_encode($excluded_ports);
    $stmt = $db->prepare("INSERT INTO ip_list (ip, name, excluded_ports) VALUES (:ip, :name, :excluded_ports)");
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':excluded_ports', $excluded_ports_json, SQLITE3_TEXT);
    $stmt->execute();
}

function deleteEntry($db, $id) {
    $stmt = $db->prepare("DELETE FROM ip_list WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function editEntry($db, $id, $ip, $name, $excluded_ports) {
    // Encode array to JSON before saving
    $excluded_ports_json = json_encode($excluded_ports);
    $stmt = $db->prepare("UPDATE ip_list SET ip = :ip, name = :name, excluded_ports = :excluded_ports WHERE id = :id");
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':excluded_ports', $excluded_ports_json, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}
?>
