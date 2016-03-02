<?php

header('Content-Type: application/json');
session_start();
include 'function.php';
$project_id = intval($_REQUEST['project_id']);
$id = intval($_REQUEST['id']);
$status = intval($_REQUEST['status']);
$password = md5($_REQUEST['password']);
$name = $_REQUEST['name'];
$action = $_REQUEST['action'];


$index = new Project();
switch ($action) {
    case "addUser":
        $index->addUser($name,$password);
        break;

    case "loginUser":
        $index->loginUser($name,$password);
        break;

    case "logout":
        $index->logout();
        break;

    case "editProject":
        $index->editProject($id,$name);
        break;

    case "deleteProject":
        $index->deleteProject($id);
        break;

    case "addProject":
        $index->addProject($name);
        break;

    case "addTask":
        $index->addTasks($project_id,$name);
        break;

    case "deleteTask":
        $index->deleteTask($id);
        break;

    case "editTasks":
        $index->editTasks($id,$name,$status);
        break;
}

if($_SESSION["user_id"]){
    $index->getProjects();
}
$index->renderReguest();

?>
