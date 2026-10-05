<?php
header("Content-Type: application/json");
require_once "../config/database.php";
$s=$pdo->query("SELECT * FROM history ORDER BY id DESC LIMIT 500");
echo json_encode(["success"=>true,"data"=>$s->fetchAll()]);
?>