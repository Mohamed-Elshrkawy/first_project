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
            $_SESSION['user_id']=reset($row);
            header ("location:user_page.php");
            exit ;
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
            exit ;  
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
                $_SESSION['user_id'] = $user['id'];
                header('Location:user_page.php');
                exit ;
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
        move_uploaded_file($image_tmp , dirname(__FILE__).'\profile_image\\'.$image_name);
        $this->image = 'profile_image/'.$image_name;
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
class blog{
    public function insert_blogs($con){
        $bage=$_POST['URI'];
        $textblog=$_POST['blog_text'];
        $imageblog=$_FILES['blog_image'];
        $image_name=$_FILES['blog_image']['name'];
        $image_tmp=$_FILES['blog_image']['tmp_name'];
        $id=$_SESSION['user_id'];
        $publish_time = date('Y-m-d H:i:s');
        if((is_string($textblog))&&($image_tmp !='')){
            
            move_uploaded_file($image_tmp ,dirname(__FILE__).'\blog_image\\'.$image_name);
            $imageblog = 'blog_image/'.$image_name;
            $sql = $con->prepare('INSERT INTO user_blogs ( text_blog ,image_blog, user_id ,time ) VALUES (?,?,?,?) ');
            $sql->execute([$textblog ,$imageblog ,$id ,$publish_time]);
            header("Location:$bage");
            exit;
        } elseif(is_string($textblog)){
                $sql = $con->prepare('INSERT INTO user_blogs ( text_blog , user_id ,time ) VALUES (?,?,?) ');
                $sql->execute([$textblog ,$id ,$publish_time]);
                header("Location:$bage");
                exit;
        }elseif(($image_tmp !='')){
                move_uploaded_file($image_tmp ,dirname(__FILE__).'\blog_image\\'.$image_name);
                $imageblog = 'blog_image/'.$image_name;
                $sql = $con->prepare('INSERT INTO user_blogs (image_blog, user_id ,time ) VALUES (?,?,?) ');
                $sql->execute([$imageblog ,$id ,$publish_time]);
                header("Location:$bage");
                exit;
        }else{
            header("Location:$bage");
            exit;
        }
    }
    public function insert_comment($con){
        $bage=$_POST['URI'];
        $textcomment=$_POST['comment_text'];
        $imagecomment=$_FILES['comment_image'];
        $image_name=$_FILES['comment_image']['name'];
        $image_tmp=$_FILES['comment_image']['tmp_name'];
        $id=$_SESSION['user_id'];
        $publish_time = date('Y-m-d H:i:s');
         $b_id=$_POST['post_id'];
            if((is_string($textcomment))&&($image_tmp !=null)){
                move_uploaded_file($image_tmp ,dirname(__FILE__).'\comment_image\\'.$image_name);
                $imagecomment = 'comment_image/'.$image_name;
                $sql = $con->prepare('INSERT INTO blog_comment ( text_comment ,image_comment, user_id,blog_id ,time ) VALUES (?,?,?,?,?) ');
                $sql->execute([$textcomment ,$imagecomment ,$id,$b_id ,$publish_time]);
                header("Location:$bage");
                exit;
            }elseif(is_string($textcomment)){
                $sql = $con->prepare('INSERT INTO blog_comment ( text_comment , user_id, blog_id ,time ) VALUES (?,?,?,?) ');
                $sql->execute([$textcomment , $id , $b_id , $publish_time]);
                header("Location:$bage");
                exit;
            }elseif($image_tmp !=null){
                move_uploaded_file($image_tmp ,dirname(__FILE__).'\comment_image\\'.$image_name);
                $imagecomment = 'comment_image/'.$image_name;
                $id=$_SESSION['id'];
                $sql = $con->prepare('INSERT INTO blog_comment (image_comment, user_id,blog_id ,time ) VALUES (?,?,?,?) ');
                $sql->execute([$imagecomment ,$id, $b_id ,$publish_time]);
                header ("location:all_blogs.php"); 
                exit();
            }else{
                header("Location:$bage");
                exit;
        }
    }
    public function insert_blog_like($con){
        $bage=$_GET['URI'];
        $post_id = $_GET['like_post'];
        
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
    
        $stmt = $con->prepare('SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?');
        $stmt->execute([$user_id, $post_id]);
        if ($stmt->rowCount() == 0) {
            $stmt = $con->prepare('INSERT INTO post_likes (user_id,username, post_id) VALUES (?,?, ?)');
            $stmt->execute([$user_id,$user_name, $post_id]);
        }
        header("Location:$bage");
        exit;
    }
    public function insert_comment_like($con){
        $comment_id = $_GET['like_comment'];
        $bage=$_GET['URI'];
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];

        $stmt = $con->prepare('SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ?');
        $stmt->execute([$user_id, $comment_id]);
        if ($stmt->rowCount() == 0) {
            $stmt = $con->prepare('INSERT INTO comment_likes (user_id,username, comment_id) VALUES (?,?, ?)');
            $stmt->execute([$user_id,$user_name, $comment_id]);
        }
        header("Location:$bage ");
        exit;
    }
    
}

$blog=new blog();
if(isset($_POST['comment'])){
    $blog->insert_comment($con);
}
if(isset($_POST['publish'])){
    $blog->insert_blogs($con);
}
if (isset($_GET['like_post']))  {
    $blog->insert_blog_like($con);
}
if (isset($_GET['like_comment'])) {
    $blog->insert_comment_like($con);
}