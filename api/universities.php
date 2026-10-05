<?php
header("Content-Type: application/json");
require_once "../config/database.php";
$m=$_SERVER["REQUEST_METHOD"];
if($m==="GET"){ $s=$pdo->query("SELECT * FROM universities ORDER BY id DESC"); echo json_encode(["success"=>true,"data"=>$s->fetchAll()]); exit; }
if($m==="POST"){
 $d=json_decode(file_get_contents("php://input"),true);
 if(empty($d["name"])||empty($d["city"])||empty($d["region"])){http_response_code(400);echo json_encode(["success"=>false,"message"=>"Name, city and region are required."]);exit;}
 $s=$pdo->prepare("INSERT INTO universities(name,city,region,established,status) VALUES(:name,:city,:region,:established,:status)");
 $s->execute([":name"=>$d["name"],":city"=>$d["city"],":region"=>$d["region"],":established"=>$d["established"]??null,":status"=>$d["status"]??"Active"]);
 $id=$pdo->lastInsertId(); $h=$pdo->prepare("INSERT INTO history(action,entity_type,entity_id,description,performed_by) VALUES('CREATE','University',:id,:desc,'Administrator')");
 $h->execute([":id"=>$id,":desc"=>"Registered university: ".$d["name"]]);
 echo json_encode(["success"=>true,"message"=>"University registered successfully.","id"=>$id]);exit;
}
if($m==="DELETE"){ $id=$_GET["id"]??null; if(!$id){http_response_code(400);echo json_encode(["success"=>false,"message"=>"ID required."]);exit;} $s=$pdo->prepare("DELETE FROM universities WHERE id=:id");$s->execute([":id"=>$id]);echo json_encode(["success"=>true,"message"=>"University deleted successfully."]);exit;}
http_response_code(405);echo json_encode(["success"=>false,"message"=>"Method not allowed."]);
?>