<?php
$conn = new mysqli("localhost", "root", "", "default_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, member_id, start_date, end_date, CAST(end_date AS CHAR) as end_date_cast, DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') as end_date_formatted, NOW() as current_datetime, DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') as current_datetime_formatted, status FROM subscriptions WHERE id = 54";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "=== SUBSCRIPTION 54 DETAILS ===\n";
    while($row = $result->fetch_assoc()) {
        foreach($row as $key => $value) {
            echo sprintf("%30s: %s\n", $key, $value);
        }
    }
} else {
    echo "No results found\n";
}
$conn->close();
?>
