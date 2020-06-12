<?php


class API
{
    private $DB;
    public function __construct(){
    	//Конструктор - делает коннект к базе
        $servername = "localhost";
        $username = "f0446806_mohax";
        $password="260898ega";
        $database = "f0446806_cloud";
        try {
            $this->DB = new PDO("mysql:host=$servername;dbname=".$database, $username, $password);
            // set the PDO error mode to exception
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function session_checker()
    {
        $status = array(
            "status" => 0,
            "message" => ""
        );
        if(session_status() ==  PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_ID'])) {

            $user_ID = $_SESSION['user_ID'];
            $query = $this->DB->prepare("SELECT * FROM users WHERE user_ID = '$user_ID';");
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
            $result = $result[0];
            $status['status'] = 0;
            $status['message'] = 'Log in';
            $status['user_ID'] = $user_ID;
            $status['username'] = $result['username'];
            return $status;
        } else {
            $status['status'] = 1;
            $status['message'] = 'gtfo';
            return $status;
        }
    }


    public function sign_in($username,$password){
        $status = array(
            "status" => 0,
            "message" => ""
        );
        $check = $this->is_user($username);
        if ($check){
            $query = $this->DB->prepare("SELECT * FROM users WHERE username = '$username';");
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
            $result = $result[0];
            if ($result['pass'] === $password){ 
                    session_start();
                    $_SESSION['user_ID'] = $result['user_ID'];
                    try {
                        $status['status'] = 0;
                        $status['message'] = 'login';
                        $status['user_ID'] = $_SESSION['user_ID'];
                        return $status;
                    }
                    catch (PDOException $e){
                        $status['status'] = 1;
                        $status['message'] = "Token update error";
                        //$status['e']=$e;
                        return $status;
                    }
                
            }
            else{
                $status['status'] = 1;
                $status['message'] = "Wrong password";
                return $status;
            }
        }
        else {
            $status['status'] = 1;
            $status['message'] = "Wrong username";
            return $status;
        }
    }

// Существует ли юзер с таким ником
// UPD: Теперь приватно, потому что не нужно проверять это на прямую
    private function is_user($username){
    	//на вход строка - имя пользователя
    	//на выход true/false есть юзер или нет
        $query = $this->DB->prepare("SELECT COUNT(`user_ID`) FROM users WHERE `username` = '$username';");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_NUM);
        $result = $query->fetch();
        if ($result [0]){
            return true;
        }
        return false;
    }

//ID юзера по его юзернейму
    private function get_user_id($username){
    	//на вход строка - юзернейм
    	//на выход id юзера в базе
        $query = $this->DB->prepare("SELECT user_ID FROM users WHERE username = '$username';");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_NUM);
        $result = $query->fetchAll();
        return $result;
    }


    


    public function sign_up($username, $password){
        $status = array(
            'status' => 0,
            'message' => "",
        );

        if ($this->is_user($username)){
            $status['status'] = 1;
            $status['message'] = 'User already exist';
            return $status;
        }
        $user_ID = $this->DB->prepare("SELECT MAX(user_ID) FROM users;");
        $user_ID->execute();
        $user_ID->setFetchMode(PDO::FETCH_NUM);
        $result_ID = $user_ID->fetchAll()[0][0];
        $this->DB->exec("BEGIN;");
        try {
            $sql = "INSERT INTO users (username, pass, directory_ID) VALUES ('$username', '$password','$result_ID'+1);";
            $this->DB->exec($sql);
            $sql = "INSERT INTO directories (directory_ID, directory_name) VALUES ('$result_ID'+1,'$username')";
            $this->DB->exec($sql);
            $this->DB->exec("COMMIT;");
            $folder = "../root/" . $username;
            mkdir($folder, 0777);
            $status['message'] = 'User created';
            return $status;
        }
        catch (PDOException $e){
            $this->DB->exec("ROLLBACK;");
            $status['status'] = 1;
            $status['message'] = $result_ID;
            return $status;
        }
    }



    public function logout(){
       $status = array(
            'status' => 0,
            'message' => "",
        );
        $validation = $this->session_checker();
        if ($validation['status'] == 0){
            if (isset($validation['username'])){  
                $status['status'] = 0;
                $status['message'] = "Logged out";
                session_destroy();
                setcookie('PHPSESSID', "", time()-3600);
            }
            else{
                $status['status'] = 1;
                $status['message'] = "¯\_(ツ)_/¯";

                return $status;
            }
        }
        else{
            $status['status'] = 1;
            $status['message'] = "Already logged out";
            return $status;
        }
    }


    public function get_user_folder($username, $sel_dir) {
        $status = array(
            'status' => 0,
            'message' => "",
        );
        //полный пусть до файла/директории - туда-обратно
        $folder = "../root/" . $username  .$sel_dir;
        $files = array_diff(scandir($folder), array('..', '.'));
        $folders= [];
        foreach ($files as &$file) {
            if(is_dir($folder."/". $file)){
                $folders[] = ['folder', "./root/" . $username  .$sel_dir.$file, $file];
            } else {
                $folders[] = ['file', "./root/" . $username  .$sel_dir. $file, $file];

            }
        }
        
        $shared = $this->shared_checker($username);
        $status['shared'] = $shared;
        $status['message'] = $folder;
        $status['data'] = $folders;
        return $status;
    }

    public function create_folder($path, $username) {
        $status = array(
            'status' => 0,
            'message' => "",
        );
        if(mkdir("../root/".$username.$path['path'].$path['foldername'], 0777)) {
            $status['ok'] = 'good';
        } else {
            $status['ok'] = 'bad';
        }
        $status['message'] = "../root/".$username.$path['path'].$path['foldername'];
        return $status;
    }

    private function get_directory_ID($username) {
        $query = $this->DB->prepare("SELECT directory_ID FROM users WHERE username = '$username';");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_NUM);
        $result = $query->fetchAll();
        return $result;
    }

    public function create_link($path, $hash, $username, $filename) {
        $status = array(
            'status' => 0,
            'message' => "",
        );
        $directory_ID = $this->get_directory_ID($username)[0][0];
        try{
            $sql = "INSERT INTO shared_files (directory_ID, file, token, filename) VALUES ('$directory_ID', '$path', '$hash', '$filename')";
            $this->DB->exec($sql);
            $status['message'] = "link created";

            return $status;
        }
        catch (PDOException $e){
            $status['status'] = 1;
            $status['message'] = 'File already shared';

            return $status;
        }
    }

    public function get_file_by_link($hash) {
        $query = $this->DB->prepare("SELECT * FROM shared_files WHERE token = '$hash';");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_NUM);
        $result = $query->fetchAll();
        return $result;
    }

    private function shared_checker($username) {
        $directory_ID = $this->get_directory_ID($username)[0][0];
        $query = $this->DB->prepare("SELECT file, token FROM shared_files WHERE directory_ID = '$directory_ID';");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_NUM);
        $result = $query->fetchAll();
        return $result;

    }
    
    public function delete_link($link) {
        $status = array(
            'status' => 0,
            'message' => "",
            'deleted' => false,
        );
        try{
            $sql = "DELETE FROM shared_files WHERE file='$link'";
            $result = $this->DB->exec($sql);
            $status['message'] = $result;

            return $status;
        }
        catch (PDOException $e){
            $status['status'] = 1;
            $status['message'] = 'something was wrong';

            return $status;
        }
    }

}