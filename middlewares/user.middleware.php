<?php
    function getUsersValidator() {
            $errors = [];
            
    
            if(isset($_GET['limit'])){
                $limit = $_GET['limit'];
            }else{
                $limit = null;
            }     
            if(isset($_GET['page'])){
                $page = $_GET['page'];
            }else{
                $page = null;
            }  // Kiểm tra $id
            if (isset($_GET['id']) && !is_numeric($_GET['id'])) {
                $errors[] = "ID phải là một số.";
            }
        
            // Kiểm tra $name
            if (isset($_GET['name'])) {
                if (!is_string($_GET['name']) || empty($_GET['name'])) {
                    $errors[] = "Name phải là một chuỗi và không được để trống.";
                }
            }
            
            // Kiểm tra $email
            if (isset($_GET['email'])) {
                if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email không hợp lệ.";
                }
            }
        
            // Kiểm tra $role
            if (isset($_GET['role'])) {
                if ($_GET['role'] !== 'AD' && $_GET['role'] !== 'UR') {
                    $errors[] = "Role phải là 'AD' hoặc 'UR'.";
                }
            }
            if ($limit ==null) {
                $errors[] = "Tham số 'limit' là bắt buộc.";
            }else{
                if(intval($limit)<=0 || intval($limit)>99){
                    $errors[] = "0 < 'limit' < 100.";
                }
            }
            if ($page == null) {
                $errors[] = "Tham số 'page' là bắt buộc.";
            }else{
                if(intval($page)<=0){
                    $errors[] = "'page' > 0 .";
                }
            }
            if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(array("error:"=> $errors)) ;
                
                return false;
            }
            return true;
    }

    function loginValidator(){
            $db = new Database();
            $conn = $db -> connect();
            $errors = [];
            if(isset($_POST['email']) && isset($_POST['password'])){
                if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                    $errors[] = "email không hợp lệ";
                }
                else if(strlen($_POST['password']) < 6){
                    $errors[] = "password phải từ 6 ký tự trở lên";
                }
                else {
                    $query = "SELECT * FROM USERS WHERE email = :email";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':email',$_POST['email']);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
           
                    if($user && $_POST['password'] == $user['password']){
                        //xác thực thành công

                        $query = "SELECT * FROM refresh_tokens WHERE  user_id = :user_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':user_id',$user['id']);
                        $stmt->execute();
                        $refresh_token = $stmt->fetch(PDO::FETCH_ASSOC);
                        if(!$refresh_token){
                            // thành công
                        $_REQUEST['user'] = $user;

                        }else{
                            $errors[] = "bạn đã login rồi";
                        }


                    }else{
                        $errors[] = "email hoặc password sai"; 
                    }
                }
            }
            else{
               $errors[] = "yêu cầu có email và password "; 
            }
            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(array("error:"=> $errors)) ;
                $conn = null;
            return false;
         }
            $conn = null;
            return true;        
    }
        
    function accessTokenValidator(){
        try{
        // Kiểm tra xem Access Token có được gửi lên hay không
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            http_response_code(401);
            echo json_encode(array("error" => "yêu cầu có access_token để xác thực"));
            return false;
        }
           // Lấy giá trị của Access Token từ header
           $accessToken = $_SERVER['HTTP_AUTHORIZATION'];
           // Kiểm tra xem Access Token có đúng định dạng hay không
           if (!preg_match('/Bearer\s(\S+)/', $accessToken, $matches)) {
               http_response_code(401);
               echo json_encode(array("error" => "access_token chưa đúng định dạng"));
               return false;
           }
           // Lấy giá trị thực tế của Access Token
            $accessToken = $matches[1];
            $decodeAuthorization = verifyToken($accessToken,"dayLaKEyAcCes5ToKEn123456123123");
            $_REQUEST['decode_authorization'] = $decodeAuthorization;
            return true;
        }catch(Exception $e){
            echo json_encode("token hết hạn hoặc không hợp lệ: " . $e->getMessage());
            return false;
        }

    }

    function isAdmin(){
        if($_REQUEST['decode_authorization']){
            if($_REQUEST['decode_authorization']->role=='AD'){
            return true;
        }
            else{
                http_response_code(401);
                echo json_encode(array("error" => "yêu cầu quyền admin"));
                return false;
            }
        }else{
            http_response_code(401);
            echo json_encode(array("error" => "yêu cầu đăng nhập"));

        }
        }


?>