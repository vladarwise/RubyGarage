var app = angular.module('AppTasks', []);

app.controller('ProjectController', function($scope, $http) {
    getTask(); // Load all available tasks
    $("#preloader").hide();
    function getTask(){
        $http.get("/apisite.php").success(function(data){
            $scope.data = angular.fromJson(data);
            data = angular.fromJson(data);
            if(data[0].status == 'login'){
                $('.login-panel').hide();
                $('.tasks-panels').show();
            }else{
                $('.login-panel').show();
                $('.tasks-panels').hide();
            }
            $scope.projects = $scope.data[0].data.projects;
            $("#preloader").hide();
        }).error(function (data) {
            console.log("failed");
        });
    };
    $scope.action = 'addUser';
    $scope.SubmitForm = function (action,name,password) {
        if(action && name && password) {
            $http.post("/apisite.php?action=" + action + "&name=" + name + "&password=" + password).success(function (data) {
                $scope.user = angular.fromJson(data);
                $scope.action = 'loginUser';
                data = angular.fromJson(data);
                if(data[0].status == 'login'){
                    $('.login-panel').hide();
                    $('.tasks-panels').show();
                }else{
                    $('.login-panel').show();
                    $('.tasks-panels').hide();
                }
                update_scope(data);
            }).error(function (data) {
                console.log("failed");
            });
        }else{
            alert('Заполните все поля.');
        }
    };
    $scope.logout = function () {
        $("#preloader").show();
        $http.get("/apisite.php?action=logout").success(function(data){
            $scope.data[0].message="Пользователь покинул свой кабинет";
            getTask();
            $("#preloader").hide();
        });
    };
    $scope.deleteProject = function (projects) {
        $("#preloader").show();
        $http.get("/apisite.php?action=deleteProject&id="+projects.id).success(function(data){
            update_scope(data);
        });
    };
    $scope.editProject = function (projects) {
        $scope.Modal_title = "Редактировать проект " + projects.name;
        $scope.Modal_name = projects.name;
        $scope.Modal_id = projects.id;
        $scope.Modal_action = "editProject";
        $('#editProjectModal').modal('show');
    };
    $scope.newProject = function () {
        $scope.Modal_title = "Добавить проект";
        $scope.Modal_name = "New Project";
        $scope.Modal_id = '';
        $scope.Modal_action = "addProject";
        $('#editProjectModal').modal('show');
    };


    $scope.addTask = function (projects) {
        $("#preloader").show();
        $http.get("/apisite.php?action=addTask&name=" + projects.addtask + "&project_id=" +projects.id).success(function(data){
            update_scope(data);
        });
    };
    $scope.deleteTask = function (id) {
        $("#preloader").show();
        $http.get("/apisite.php?action=deleteTask&id=" + id).success(function(data){
            update_scope(data);
        });
    };
    $scope.editTask = function (task) {
        $scope.Modal_title = "Редактировать задачу " + task.name;
        $scope.Modal_name = task.name;
        $scope.Modal_id = task.id;
        $scope.Modal_action = "editTasks";
        $scope.Modal_status = task.status;
        $('#editProjectModal').modal('show');
    };
    $scope.taskChengeStatus = function (task) {
        $http.get("/apisite.php?action=editTasks&name="+task.name+"&id=" + task.id + "&status="+ task.status).success(function(data){
            update_scope(data);
        });
    };
    $scope.modal_submit = function () {
        $("#preloader").show();
        $('#editProjectModal').modal('hide');
        $http.get("/apisite.php?action=" +$scope.Modal_action + "&name=" + $scope.Modal_name + "&id=" + $scope.Modal_id + "&status="+ $scope.Modal_status).success(function(data){
            update_scope(data);
        });
    };

    function update_scope(data){
        $scope.data = angular.fromJson(data);
        $scope.projects = $scope.data[0].data.projects;
        $("#preloader").hide();
    }
});

