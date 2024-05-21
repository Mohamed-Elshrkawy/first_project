<?php
require('connect.php');
session_start();
class user{
    public $full_name;
    public $email;
    public $phone;
    public $birthday;
    public $age;
    public $password;
    public $blogs;
    public $id;
    public $image;


    public function insert_Data($con){
        $sql = $con->prepare('INSERT INTO user_data (full_name,email, phone, date_birth  ,password,profile_image, age )
        VALUES (?,?,?,?,?,?,?)');
        $sql->execute([$this->full_name, $this->email, $this->phone, $this->birthday,$this->password,$this->image, $this->age ]);
        $sql = "SELECT MAX(id) FROM user_data"; 
        $stmt = $con->query($sql);
        if($stmt->rowCount()>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $_SESSION['id']=reset($row);
            header ("location:user_page.php");
            }
        }
    }
    public function get_Data(){
            $this->full_name = $_POST['fullname'];
            $this->email = $_POST['email'];
            $this->phone = $_POST['phone'];
            $this->birthday = $_POST['birthday'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $this->password=$password;
    }
    public function update_Data($con){
            $id=$_SESSION['id']; 
            $this->full_name = $_POST['fullname'];
            $this->email = $_POST['email'];
            $this->phone = $_POST['phone'];
            $this->birthday = $_POST['birthday'];
            $sql = $con->prepare('UPDATE user_data SET full_name = ? ,email = ? , phone= ? ,date_birth = ? ,age = ? WHERE id = ?'); 
            $sql->execute([$this->full_name, $this->email, $this->phone, $this->birthday ,$this->age ,$id]);
            header ("location:user_page.php");  
    }
    public function accont_age(){
        $birthday = $_POST['birthday'];
        $new_birthday = new DateTime($birthday);
        $currentday = new DateTime();
        $age = $currentday->diff($new_birthday);
        $this->age=$age->y;    
    }
    public function check_data($con){

            $username = $_POST['username'];
            $password = $_POST['password'];
        
            $stmt = $con->prepare('SELECT * FROM user_data WHERE full_name = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
        
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                header('Location:user_page.php');
            } else {
                echo 'Invalid username or password';
            }
    }
    public function insert_image (){
        $image=$_FILES['image'];
        $image_type=$_FILES['image']['type'];
        $image_name=$_FILES['image']['name'];
        $image_error=$_FILES['image']['error'];
        $image_tmp=$_FILES['image']['tmp_name'];
        $image_size=$_FILES['image']['size'];
        move_uploaded_file($image_tmp ,'C:\xampp\htdocs\areej\profile_image\\'.$image_name);
        $this->image = 'profile_image/'.$image_name;
    }
    public function insert_blogs($con){
        $textblog=$_POST['blog_text'];
        $imageblog=$_FILES['blog_image'];
        $image_name=$_FILES['blog_image']['name'];
        $image_tmp=$_FILES['blog_image']['tmp_name'];
        $id=$_SESSION['id'];
        $publish_time = date('Y-m-d H:i:s');
        if((is_string($textblog))&&($image_tmp !='')){
            
            move_uploaded_file($image_tmp ,'C:\xampp\htdocs\areej\blog_image\\'.$image_name);
            $imageblog = 'blog_image/'.$image_name;
            $sql = $con->prepare('INSERT INTO user_blogs ( text_blog ,image_blog, user_id ,time ) VALUES (?,?,?,?) ');
            $sql->execute([$textblog ,$imageblog ,$id ,$publish_time]);
            header ("location:user_page.php"); 
            exit();
        } elseif(is_string($textblog)){
                $sql = $con->prepare('INSERT INTO user_blogs ( text_blog , user_id ,time ) VALUES (?,?,?) ');
                $sql->execute([$textblog ,$id ,$publish_time]);
                header ("location:user_page.php"); 
                exit();
        }elseif(($image_tmp !='')){
                move_uploaded_file($image_tmp ,'C:\xampp\htdocs\areej\blog_image\\'.$image_name);
                $imageblog = 'blog_image/'.$image_name;
                $sql = $con->prepare('INSERT INTO user_blogs (image_blog, user_id ,time ) VALUES (?,?,?) ');
                $sql->execute([$imageblog ,$id ,$publish_time]);
                header ("location:user_page.php"); 
                exit();
        }else{
        header ("location:user_page.php"); 
        exit();
        }
    }
    public function insert_comment($con){
        $textcomment=$_POST['comment_text'];
        $imagecomment=$_FILES['comment_image'];
        $image_name=$_FILES['comment_image']['name'];
        $image_tmp=$_FILES['comment_image']['tmp_name'];
        $id=$_SESSION['id'];
        $publish_time = date('Y-m-d H:i:s');
         $b_id=$_POST['post_id'];
            if((is_string($textcomment))&&($image_tmp !='')){
            move_uploaded_file($image_tmp ,'C:\xampp\htdocs\areej\comment_image\\'.$image_name);
            $imagecomment = 'comment_image/'.$image_name;
            $sql = $con->prepare('INSERT INTO blog_comment ( text_comment ,image_comment, user_id,blog_id ,time ) VALUES (?,?,?,?,?) ');
            $sql->execute([$textcomment ,$imagecomment ,$id,$b_id ,$publish_time]);
            header ("location:all_blogs.php"); 
            exit();
            }elseif(is_string($textcomment)){
                $sql = $con->prepare('INSERT INTO blog_comment ( text_comment , user_id, blog_id ,time ) VALUES (?,?,?,?) ');
                $sql->execute([$textcomment , $id , $b_id , $publish_time]);
                header ("location:user_page.php"); 
                exit();
            }elseif($image_tmp !=''){
                move_uploaded_file($image_tmp ,'C:\xampp\htdocs\areej\comment_image\\'.$image_name);
                $imagecomment = 'comment_image/'.$image_name;
                $id=$_SESSION['id'];
                $sql = $con->prepare('INSERT INTO blog_comment (image_comment, user_id,blog_id ,time ) VALUES (?,?,?,?) ');
                $sql->execute([$imagecomment ,$id, $b_id ,$publish_time]);
                header ("location:all_blogs.php"); 
                exit();
            }else{
        header ("location:all_blogs.php"); 
        exit();
        }
    }
}
$user1 = new user();
if(isset($_POST['SignUp'])){
    $user1 = new user();
    $user1->insert_image();
    $user1->accont_age();
    $user1->get_data();
    $user1->insert_data($con);
}
if(isset($_POST['update'])){
    $user1 = new user();
    $user1->get_data();
    $user1->accont_age();
    $user1->update_Data($con);
}
if(isset($_POST['Login'])){
    $user1 = new user();
    $user1->check_data($con);
}
if(isset($_POST['comment'])){
    $user1->insert_comment($con);
}
if(isset($_POST['publish'])){
    $user1->insert_blogs($con);
}