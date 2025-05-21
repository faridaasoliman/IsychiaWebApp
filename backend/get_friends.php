<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "isychia_db";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "SELECT u.id, u.username FROM users u
            JOIN friends f ON (f.friend_id = u.id OR f.user_id = u.id)
            WHERE (f.user_id = ? OR f.friend_id = ?)
              AND u.id != ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $friends = array();
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }

    echo json_encode(["friends" => $friends]);
} else {
    echo json_encode(["error" => "Missing user_id"]);
}

$conn->close();
?>
