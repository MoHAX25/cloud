	var app = angular.module('folders',['ngCookies','ngMaterial','ngMessages','ngFileUpload']);
	app.controller('folders_controller', function ($scope, $http, $mdDialog, $filter,Upload) {
		
		$scope.session_check = function(){
			$http.get('src/session_check.php').then(function(response){
	            $scope.status = response.data;
	            console.log($scope.status);
	            if($scope.status['status']===0){
	                console.log('good');
	                $scope.username = $scope.status['username'];
	                $scope.get_user_folder("");
	            } else{
	            	console.log('what?');
	                document.location.href = "index.html"; 
	            }
        	})
		}
		$scope.session_check();
		$scope.log_out = function() {
			$http.post('src/logout.php').then(function(response){
	            document.location.href = "index.html";
        	})
		}
		$scope.check_folder = function(item) {
			if(item[0] == 'folder') {
				$scope.get_user_folder(item[2]);
			} else {
					key = item[2].lastIndexOf(".");
					obj = item[2].slice(key+1, item[2].length);
					let img = "bmp, dib, png, gif, tiff, jpeg, jpg, tif";
					if(img.indexOf(obj.toLowerCase())!== -1) {
						$mdDialog.show({
				      	controller: DialogController,
				      	templateUrl: 'src/image.html',
				      	parent: angular.element(document.body),
				      	targetEvent: item,
				     	locals: {
			           		item: item[3]
			         	},
					      clickOutsideToClose:true,
					      fullscreen: $scope.customFullscreen
					    })
					}
					
			}
			
		}
		$scope.back = false;
		$scope.show_back =function () {
			let cur_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
			
			if(cur_dir == "") {
				$scope.back = false;
			} else {
				$scope.back = true;
			}
			
		}

		$scope.current_dir = '';
		$scope.get_user_folder = function(sel_dir) {
            $scope.current_dir = $scope.current_dir+sel_dir+"/";
            console.log($scope.current_dir)
			$scope.show_back();
			$http({
                method : 'POST',
                url : "src/get_user_folder.php",
                data: {
                	'username':$scope.username,
                	'sel_dir': $scope.current_dir
                }
             })
            .then(function(response){
            	
            	if(response.data['status']==0){
            	
                    $scope.status = response.data;
                    $scope.files = $scope.status.data;
                    $scope.shared_files = $scope.status.shared;
                    console.log($scope.shared_files);
                    for (var i = $scope.files.length - 1; i >= 0; i--) {
                    	if($scope.files[i][0]=='folder') {
                    		$scope.files[i].push("icons/folder.png");                    	
                    	} else {
                    		let music = "WAV, AIF, MP3, MID.";
                    		let key = $scope.files[i][2].lastIndexOf(".");
							let obj = $scope.files[i][2].slice(key+1, $scope.files[i][2].length-1);						
							if(music.toLowerCase().indexOf(obj.toLowerCase())!== -1) {
								$scope.files[i].push("icons/music.png");
							} else {
								$scope.files[i].push($scope.files[i][1]);
							}
                    		
                    	}
                    }
                }
            	
            })
		}

		$scope.shared_check = function(path_to_file) {
			let share = $scope.shared_files;
			for (var i = 0; i < share.length; i++) {
				let share_try = share[i][0].slice(1, share[i][0].length);
				console.log(share_try);
				console.log(path_to_file);
				if(path_to_file == share_try){
					return true;
				} 
			}	
		}

		$scope.show_links = function(ev) {
			let share = $scope.shared_files;
			$scope.shared = [];
			let server = window.location.hostname;
			for (var i = 0; i < share.length; i++) {
				let key = share[i][0].indexOf("./root/");
				let obj = share[i][0].slice(key+7, share[i][0].length);	
				$scope.shared.push({"link" : "http://"+server+"/src"+"/sharing.php?hash="+share[i][1], "filename": obj });
			}
			console.log($scope.shared);
			$mdDialog.show({
		      	contentElement: '#sharedia',
		     	parent: angular.element(document.body),
		      	targetEvent: ev,
		      	clickOutsideToClose: true
		    });
		    $scope.current_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
		    $scope.get_user_folder("");
		}
		$scope.copy_link = function (id) {
			var copyText = document.getElementById(id);
			copyText.select();
			document.execCommand("copy");
			document.getElementById("Tooltip").style.visibility ='visible'; 

		}
		


		$scope.rollback = function() {
			let cur_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
			let key = cur_dir.lastIndexOf("/");
			$scope.current_dir = cur_dir.slice(0, key);
			$scope.get_user_folder("");
			$scope.show_back();
		}
		
		$scope.create_folder = function(ev) {
			$scope.folder = {
				path:'',
				foldername:''
			};
			$scope.folder.path = $scope.current_dir;
			$mdDialog.show({
	      	controller: NewFolderController,
	      	templateUrl: 'src/folder_maker.html',
	      	parent: angular.element(document.body),
	      	targetEvent: ev,
	     	locals: {
           		folder: $scope.folder
         	},
		      clickOutsideToClose:true,
		      fullscreen: $scope.customFullscreen
		    }).then(function(answer) {
		    	$http({
	                method : 'POST',
	                url : "src/folder_create.php",
	                data: {
	                	
	                	'folder': $scope.folder,
	                	'username':$scope.username
	                }
             	})
	            .then(function(response){
	            	console.log(response);
	            	
	            	$scope.current_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
	            	$scope.get_user_folder("");
	            })
		    	
		    }, function() {
		    });
			
		}

		
		$scope.download_icon = function(file_type) {
			if(file_type=="file") {
				return true;
			} else {
				return false;
			}
		} 
		$scope.delete = function(file) {
			$http({
	                method : 'POST',
	                url : "src/delete.php",
	                data: {
	                	userfile: file[2],
	                	path: $scope.current_dir,
	                	username: $scope.username
	                }
             	})
	            .then(function(response){
	            	$scope.current_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
	            	$scope.get_user_folder("");
	            })
		}

		$scope.create_link = function(file) {
			
			$http({
	                method : 'POST',
	                url : "src/create_link.php",
	                data: {
	                	userfile: file[2],
	                	path: $scope.current_dir,
	                	username: $scope.username
	                }
             	})
	            .then(function(response){
	            	console.log(response);
	            	$scope.current_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
	            	$scope.get_user_folder("");
	            })
		}

		$scope.upload_file = function (file) {
			
            Upload.upload({
            	
                url: "src/upload_file.php",
                data: {
                	userfile: file,
                	path: $scope.current_dir,
                	username: $scope.username
					}
            }).then(function (resp) {
               $scope.current_dir = $scope.current_dir.slice(0, $scope.current_dir.length-1);
               $scope.get_user_folder("");
            });
         
        }

		function DialogController($scope, $mdDialog,item) {
        	$scope.image = item;
        	
		    $scope.hide = function() {
		      $mdDialog.hide();
		    };

		    $scope.cancel = function() {
		      $mdDialog.cancel();
		    };

		    $scope.answer = function(answer) {
		      $mdDialog.hide(answer);
		    };
		} 

		function NewFolderController($scope, $mdDialog, folder) {
        	$scope.folder = folder;
        	
		    $scope.hide = function() {
		      $mdDialog.hide();
		    };

		    $scope.cancel = function() {
		      $mdDialog.cancel();
		    };

		    $scope.answer = function(answer) {
		      $mdDialog.hide(answer);
		    };
		} 
	})