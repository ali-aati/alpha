<?php 
    /*
    * this is the base controller
    * this loads the model and views
    */

    class Base {
    /*
    *load model and view
    */ 

        public function model($model){
            // require model file
            require_once dirname(__dir__)."/models/{$model}.php";

            return new $model();
        }

        public function view($view, $data = [] ){
            if (file_exists(dirname(__dir__)."/views/{$view}.php")){
                require_once dirname(__dir__)."/views/{$view}.php";
            }else{
                require_once dirname(dirname(__dir__))."/public/html/404.html";
            }
        }

        public function img_upload($path,$image){
            // get image details
            $name = strtotime(date("Y-m-d H:i:s")).'_'.$image["name"];
            $temp = $image["tmp_name"];
            $size = $image["size"];

            $target_dir         =   $path;                                                  // destination path
            $target_file        =   $target_dir . basename($name);                    // destination file
            $upload_ok          =   1;                                                      // upload checker
            $image_file_type    =   strtolower(pathinfo($target_file,PATHINFO_EXTENSION));  // file type
            
            // Check if file already exists
            if (file_exists($target_file)) {
                $upload_ok = 0;
                return [true, null];
            }
            // Check file size
            if ($size > 10000000) {
                $upload_ok = 0;
                return [false, "Image too big"];
            }
            // Allow certain file formats
            if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"
            && $image_file_type != "gif" ) {
                $upload_ok = 0;
                return [false, $name." format not acceptable"];
            }
            // Check if $upload_ok is set to 0 by an error
            if ($upload_ok == 0) 
                // return error
                return [false, $name.": An error occured!"];
            else 
                if (move_uploaded_file($temp, $target_file))
                    return [true, $name];
                else
                    return [false, "Wasn't able to upload {$name}"];
        }

        public function sendmail($emails, $subject, $message){

            // headers
            $headers    =   "From: " . strip_tags("no-reply@email.com") . "\r\n";
            $headers    .=  "MIME-Version: 1.0\r\n";
            $headers    .=  "Content-Type: text/html; charset=ISO-8859-1\r\n";

            if (is_array($emails)):
                foreach ($emails as $email)
                    if (isset($email) && !empty($email)):
                        mail($email, $subject, $this->email_template($message), $headers);
                    endif;
            else:
                mail($emails, $subject, $this->email_template($message), $headers);
            endif;
        }

        public function email_template($message){
            return $message;
        }
    }
?>