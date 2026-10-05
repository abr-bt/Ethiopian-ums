<?php
header("Content-Type: application/json");
require_once "../config/database.php";
$method=$_SERVER["REQUEST_METHOD"];
if($method==="GET"){
 $stmt=$pdo->query("SELECT s.*,u.name university_name FROM students s LEFT JOIN universities u ON s.university_id=u.id ORDER BY s.id DESC");
 echo json_encode(["success"=>true,"data"=>$stmt->fetchAll()]); exit;
}
if($method==="POST"){
 $data=json_decode(file_get_contents("php://input"),true);
 $sql="INSERT INTO students (student_id,full_name,gender,university_id,faculty,department,program,year_level,phone,email) VALUES (:student_id,:full_name,:gender,:university_id,:faculty,:department,:program,:year_level,:phone,:email)";
 $stmt=$pdo->prepare($sql);
 foreach(["student_id", "full_name", "gender", "university_id", "faculty", "department", "program", "year_level", "phone", "email"] as $f) $stmt->bindValue(":".$f, $data[$f] ?? null);
 $stmt->execute();
 $id=$pdo->lastInsertId();
 $h=$pdo->prepare("INSERT INTO history(action,entity_type,entity_id,description,performed_by) VALUES('CREATE',:type,:id,:description,'Administrator')");
 $h->execute([":type"=>"students",":id"=>$id,":description"=>"Created record in students"]);
 echo json_encode(["success"=>true,"message"=>"Record registered successfully.","id"=>$id]); exit;
}
if($method==="DELETE"){
 $id=$_GET["id"]??null;
 if(!$id){http_response_code(400);echo json_encode(["success"=>false,"message"=>"ID is required."]);exit;}
 $stmt=$pdo->prepare("DELETE FROM students WHERE id=:id");$stmt->execute([":id"=>$id]);
 echo json_encode(["success"=>true,"message"=>"Record deleted successfully."]);exit;
}
http_response_code(405); echo json_encode(["success"=>false,"message"=>"Method not allowed."]);
?>