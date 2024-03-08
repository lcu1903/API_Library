<?php
    //api của admin

    require "../../middlewares/user.middleware.php";
    require "../../middlewares/book.middleware.php";
    require "../../controllers/books.controller.php";

    function route_update_book() {
        // Kiểm tra phương thức request là GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // lấy data từ req.query
     
        // thực hiện validate() 
        if(accessTokenValidator()){  
            if(isAdmin()){
                if( updateBookValidator()){
                    // gọi controller
                    updateBookController();
                }
            }
        }
    } else {
            // Trả về lỗi không hỗ trợ phương thức
            http_response_code(405);
            echo json_encode(array("message" => "Method Not Allowed"));
        }
        }
        route_update_book()
?>