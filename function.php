<?php
	// Config DB
	define ("DBHOST", "localhost"); 
	define ("DBNAME", "testdb");
	define ("DBUSER", "root");
	define ("DBPASS", "");
	// Config  DB
	
	define ("COLLATE", "utf8");
	include 'mysql.php';

class Project
{
	private $db;
	private $user_id;

	private $reguest = array();
	private $data;
	private $class;
	private $message;
	private $status;

	public function __construct() {
		$this->db = new db();
		if($_SESSION["user_id"]){
			$this->user_id = $_SESSION["user_id"];
			$this->checkLogin();
		}
	}

	public function addUser($name, $password)
	{
		$password = $this->hashStr($password);
		$temp_row = $this->db->super_query("SELECT COUNT(*) as count FROM users WHERE name='$name'");
		if ($temp_row['count'] < 1) {
			if ($this->db->query("INSERT INTO users (name, password) values ('{$name}', '{$password}')")) {
				$this->ok("Пользователь успешно зарегистрирован");
			}
		} else {
			$this->error("Пользователь с таким ником уже существует");
		}
	}

	function checkLogin(){
		$temp_row = $this->db->super_query("SELECT * FROM users WHERE id='$this->user_id'");
		if ($temp_row['id']) {
			$this->user_id = $temp_row['id'];
			$_SESSION["user_id"] = $temp_row['id'];
			$_SESSION["user_name"] = $temp_row['name'];
			$this->status = "login";
		} else {
			$this->logout();
		}
	}
	public function loginUser($name, $password)
	{
		$password = $this->hashStr($password);
		$temp_row = $this->db->super_query("SELECT * FROM users WHERE name='$name' AND password='$password'");
		if ($temp_row['id']) {
			$this->user_id = $temp_row['id'];
			$_SESSION["user_id"] = $temp_row['id'];
			$_SESSION["user_name"] = $temp_row['name'];
			$this->ok("Пользователь успешно авторизован");
			$this->status = "login";
			$this->getProjects();
		} else {
			$this->error("Неверный логин или пароль");
		}
	}
	public function logout()
	{
		$this->user_id = "";
		if (!empty($_SESSION["user_id"])) {
			unset($_SESSION["user_id"]);
		}
		if (!empty($_SESSION["user_name"])) {
			unset($_SESSION["user_name"]);
		}
		$this->data = null;
		$this->status = null;
	}

	function getProjects()
	{
		$row = $this->db->query("SELECT * FROM projects WHERE id_user='$this->user_id'");
		$Projects = array();
		while ($project = $this->db->get_row($row)) {
			$project['tasks'] = $this->getProjectTasks($project['id']);
			$Projects[] = $project;
		}
		$this->setData("projects", $Projects);
	}
	function getProjectTasks($project_id)
	{
		$row = $this->db->query("SELECT * FROM tasks WHERE project_id='$project_id'");
		$Tasks = array();
		while ($task = $this->db->get_row($row)) {
			$Tasks[] = $task;
		}
		return $Tasks;
	}


	function editProject($id, $name)
	{
		if($this->checkProject($id)){
			return $this->db->query("UPDATE projects set name='$name'  WHERE id='$id'");
		}else{
			return 0;
		}
	}


	function deleteProject($id)
	{
		if($this->checkProject($id)){
			$this->db->query("DELETE FROM projects WHERE id='$id'");
			$this->deleteProjectTasks($id);
			return 1;
		}else{
			return 0;
		}
	}

	function deleteProjectTasks($project_id)
	{
		return $this->db->query("DELETE FROM tasks WHERE project_id='$project_id'");
	}

	function addProject($name)
	{
		if($this->user_id){
			return $this->db->query("INSERT INTO projects (id, name, id_user) values ('', '{$name}', '$this->user_id')");
		}else{
			return 0;
		}
	}

	function addTasks($project_id, $name, $status = 0)
	{
		if($this->checkProject($project_id)){
			return $this->db->query("INSERT INTO tasks (id, name, status, project_id) values ('', '{$name}', '{$status}', '{$project_id}')");
		}else{
			return 0;
		}

	}

	function deleteTask($id)
	{
		if($this->checkTasks($id)){
			return $this->db->query("DELETE FROM tasks WHERE id='$id'");
		}else{
			return 0;
		}
	}

	function editTasks($id, $name, $status)
	{
		if($this->checkTasks($id)){
			return $this->db->query("UPDATE tasks set name='$name', status='$status'  WHERE id='$id'");
		}else{
			return 0;
		}
	}




	function checkTasks($id){
		$row = $this->db->super_query( "SELECT  COUNT(*) as count  FROM projects AS projects, tasks AS tasks  WHERE projects.id = tasks.project_id AND projects.id_user='$this->user_id' AND tasks.id='$id'" );
		return $row['count'];
	}
	function checkProject($id){
		$row = $this->db->super_query( "SELECT  COUNT(*) as count  FROM projects WHERE id_user='$this->user_id' AND id='$id'" );
		return $row['count'];
	}








	function error($text)
	{
		$this->class = "alert-danger";
		$this->message = $text;
	}
	function ok($text)
	{
		$this->class = "alert-success";
		$this->message = $text;
	}
	public function renderReguest()
	{
		$this->reguest[] = $this->json = array(
			"status" => $this->status,
			"class" => $this->class,
			"message" => $this->message,
			"data" => $this->data,
		);
		echo $this->jdecoder(json_encode($this->reguest));
	}
	public function setData($key, $value)
	{
		$this->data[$key] = $value;
	}

	private function hashStr($str)
	{
		return md5(md5($str));
	}

	private function jdecoder($json_str)
	{
		$cyr_chars = array(
			'\u0430' => 'а', '\u0410' => 'А',
			'\u0431' => 'б', '\u0411' => 'Б',
			'\u0432' => 'в', '\u0412' => 'В',
			'\u0433' => 'г', '\u0413' => 'Г',
			'\u0434' => 'д', '\u0414' => 'Д',
			'\u0435' => 'е', '\u0415' => 'Е',
			'\u0451' => 'ё', '\u0401' => 'Ё',
			'\u0436' => 'ж', '\u0416' => 'Ж',
			'\u0437' => 'з', '\u0417' => 'З',
			'\u0438' => 'и', '\u0418' => 'И',
			'\u0439' => 'й', '\u0419' => 'Й',
			'\u043a' => 'к', '\u041a' => 'К',
			'\u043b' => 'л', '\u041b' => 'Л',
			'\u043c' => 'м', '\u041c' => 'М',
			'\u043d' => 'н', '\u041d' => 'Н',
			'\u043e' => 'о', '\u041e' => 'О',
			'\u043f' => 'п', '\u041f' => 'П',
			'\u0440' => 'р', '\u0420' => 'Р',
			'\u0441' => 'с', '\u0421' => 'С',
			'\u0442' => 'т', '\u0422' => 'Т',
			'\u0443' => 'у', '\u0423' => 'У',
			'\u0444' => 'ф', '\u0424' => 'Ф',
			'\u0445' => 'х', '\u0425' => 'Х',
			'\u0446' => 'ц', '\u0426' => 'Ц',
			'\u0447' => 'ч', '\u0427' => 'Ч',
			'\u0448' => 'ш', '\u0428' => 'Ш',
			'\u0449' => 'щ', '\u0429' => 'Щ',
			'\u044a' => 'ъ', '\u042a' => 'Ъ',
			'\u044b' => 'ы', '\u042b' => 'Ы',
			'\u044c' => 'ь', '\u042c' => 'Ь',
			'\u044d' => 'э', '\u042d' => 'Э',
			'\u044e' => 'ю', '\u042e' => 'Ю',
			'\u044f' => 'я', '\u042f' => 'Я',

			'\r' => '',
			'\n' => '<br />',
			'\t' => ''
		);
		foreach ($cyr_chars as $key => $value) {
			$json_str = str_replace($key, $value, $json_str);
		}
		return $json_str;
	}
}
?>