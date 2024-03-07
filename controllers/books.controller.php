<?php 
    
    header('Access-Control-Allow-Origin:*');
    header('Content-Type: application/json');
    require "../../db.php";
    require "../../models/books.model.php";


        function getBooksController(){
            $db = new Database();
    
            $connect = $db->connect();
            $book = new Book( $connect);
            if(isset($_GET['id'])){ $book->id= $_GET['id'];} 
            if(isset($_GET['title'])){ $book->title= $_GET['title'];} 
            if(isset($_GET['available'])){ $book->available= $_GET['available'];} 
            if(isset($_GET['description'])){ $book->description= $_GET['description'];} 
            if(isset($_GET['category_code'])){ $book->category_code= $_GET['category_code'];} 
            if(isset($_GET['author'])){ $book->author= $_GET['author'];} 
            
            $result = $book->getBooks();
            $num = $result->rowCount();
        
            if($num>0){
                $results_array= [];
                while($row= $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $item = array(
                        'id'=> $id,
                        'title'=>$title,
                        'available'=>$available,
                        'image'=>$image,
                        'description'=>$description,
                        'category_code'=>$category_code,
                        'author'=>$author,
                        'category_code' => $category_code,
                        'category_value' =>$category_value
                    );
                    array_push($results_array,$item);
                }
                echo json_encode(array("message"=>"Successfully",'data'=>$results_array));
            }
            else{
                echo json_encode(array('message:'=>"không tìm thấy sách"));    
            }
        }
    


?>