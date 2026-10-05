const API="api/";
let universities=[], students=[], lecturers=[], securityStaff=[], cleaners=[], history=[];

async function request(file, options={}) {
  const r=await fetch(API+file, options);
  const data=await r.json();
  if(!r.ok || data.success===false) throw new Error(data.message||"Request failed");
  return data;
}
function body(form){return JSON.stringify(Object.fromEntries(new FormData(form).entries()));}
function esc(v){return String(v??"").replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));}

async function loadAll(){
 try{
  [universities,students,lecturers,securityStaff,cleaners,history]=await Promise.all([
   request("universities.php").then(x=>x.data),
   request("students.php").then(x=>x.data),
   request("lecturers.php").then(x=>x.data),
   request("security.php").then(x=>x.data),
   request("cleaners.php").then(x=>x.data),
   request("history.php").then(x=>x.data)
  ]);
  render();
 }catch(e){console.error(e);alert("Backend connection failed. Make sure Apache/MySQL are running and the database is imported.");}
}
function render(){
 universityCount.textContent=universities.length;
 studentCount.textContent=students.length;
 lecturerCount.textContent=lecturers.length;
 staffCount.textContent=securityStaff.length+cleaners.length;
 universitiesBody.innerHTML=universities.map(x=>`<tr><td>${x.id}</td><td>${esc(x.name)}</td><td>${esc(x.city)}</td><td>${esc(x.region)}</td><td>${esc(x.established)}</td><td>${esc(x.status)}</td><td><button class="danger" onclick="removeItem('universities.php',${x.id})">Delete</button></td></tr>`).join("");
 studentsBody.innerHTML=students.map(x=>`<tr><td>${x.id}</td><td>${esc(x.student_id)}</td><td>${esc(x.full_name)}</td><td>${esc(x.university_name||x.university_id)}</td><td>${esc(x.program)}</td><td><button class="danger" onclick="removeItem('students.php',${x.id})">Delete</button></td></tr>`).join("");
 lecturersBody.innerHTML=lecturers.map(x=>`<tr><td>${x.id}</td><td>${esc(x.employee_id)}</td><td>${esc(x.full_name)}</td><td>${esc(x.department)}</td><td>${esc(x.academic_rank)}</td><td><button class="danger" onclick="removeItem('lecturers.php',${x.id})">Delete</button></td></tr>`).join("");
 historyBody.innerHTML=history.map(x=>`<tr><td>${x.id}</td><td>${esc(x.action)}</td><td>${esc(x.entity_type)}</td><td>${esc(x.description)}</td><td>${esc(x.performed_by)}</td><td>${esc(x.created_at)}</td></tr>`).join("");
}
async function removeItem(file,id){if(!confirm("Delete this record?"))return;try{await request(file+"?id="+id,{method:"DELETE"});await loadAll()}catch(e){alert(e.message)}}

async function submitForm(formId,file){
 document.getElementById(formId).addEventListener("submit",async e=>{
  e.preventDefault();
  try{const x=await request(file,{method:"POST",headers:{"Content-Type":"application/json"},body:body(e.target)});alert(x.message);e.target.reset();await loadAll();}
  catch(err){alert(err.message)}
 });
}
document.addEventListener("DOMContentLoaded",()=>{
 submitForm("universityForm","universities.php");
 submitForm("studentForm","students.php");
 submitForm("lecturerForm","lecturers.php");
 submitForm("securityForm","security.php");
 submitForm("cleanerForm","cleaners.php");
 loadAll();
});
