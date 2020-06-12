	var app = angular.module('myApp',['ngCookies','ngMaterial','ngMessages','ngFileUpload']);
	app.controller('form_controller', function ($scope, $http, $mdDialog, $filter,Upload) {
		$scope.currentNavItem = 'Sign In';
		$scope.session_check = function(){
			$http.get('src/session_check.php').then(function(response){
	            $scope.status = response.data;
	            console.log($scope.status);
	            if($scope.status['status']===0){
	                document.location.href = "folders.html"; 
	            } else{
	                console.log($scope.status['message']);
	            }
        	})
		}
		$scope.session_check();
		$scope.log_in = function() {
			if($scope.user.name == ''||$scope.user.password == '') {
				$scope.alert = 'Один из пунктов пуст!';
			} else {
				$http({
                method : 'POST',
                url : "src/log_in.php",
                data: {
                	username: $scope.user.name,
                	password: $scope.user.password
                }
             	})
                .then(function(response){
                	console.log(response.data);
                	if(response.data['status']==0){
                      	document.location.href = "folders.html"; 
                    } else {
                    	console.log('error login');
                    }
                	
                })
			}
			
		}
		$scope.check = function(tag) {
			if(tag==0) {
				$scope.alert = '';
				$scope.checker = 0;
			} else {
				$scope.alert = '';
				$scope.checker = 1;
			}
		}
		$scope.sign_up = function() {
			if($scope.user.name == ''||$scope.user.password == ''||$scope.user.password!=$scope.confirm_password) {
				$scope.alert = 'Пароли не совпадают!';
			} else {
				$http({
	                method : 'POST',
	                url : "src/sign_up.php",
	                data: {
	                	username: $scope.user.name,
	                	password: $scope.user.password
	                }
             	})
                .then(function(response){
                	console.log(response.data);
                	if(response.data['status']==0){
                		$scope.log_in();
                    } else {
                    	console.log(status['message']);
                    }
                	
                })
			}
			
		}
	})