<?php
session_start();
include('connect.php');
if (!isset($_SESSION['id'])) {
    header('Location:index.php');
    exit();
}
 $id = $_SESSION['id'];
 $sql = "SELECT*FROM user_data WHERE id ='$id' ";
 $stmt = $con->query($sql);
 if($stmt->rowCount()>0){
 while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
 $name= $row["full_name"] ;
 $phone= $row["phone"] ;
 $birthday= $row["date_birth"] ;
 $age= $row["age"] ;
 $email= $row["email"] ;
 $image= $row["profile_image"] ;
 
 }}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blogs</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
        }
        .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .profile-info, .blog-form, .blog-list, .comment-form {
        margin-bottom: 20px;
    }
    .profile-info {
        display: flex;
        align-items: center;
    }
    .profile-info .info {
        flex-grow: 1;
    }
    .profile-info p {
        margin: 5px 0;
    }
    .profile-info img {
        border-radius: 50%;
        margin-left: 20px;
        width: 200px;
        height: 200px;
    }
    .edit-button, .publish-button, .comment-button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;
    }
    .publish-button {
        background-color: #28a745;
    }
    .comment-button {
        background-color: #17a2b8;
    }
    .edit-button:hover, .publish-button:hover, .comment-button:hover {
        background-color: #0056b3;
    }
    .publish-button:hover {
        background-color: #218838;
    }
    .comment-button:hover {
        background-color: #138496;
    }
        .card {
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 2px solid #f0f0f0;
        }
        .card-title {
            font-weight: bold;
            color: #333333;
        }
        .card-subtitle {
            color: #777777;
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
            border-radius: 8px;
        }
        .comment-section {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
        }
        .comment-form textarea {
            resize: none;
        }
        .media img {
            border-radius: 50%;
        }
        .media-body h6 {
            font-weight: bold;
            color: #333333;
        }
        .media-body small {
            color: #999999;
        }
        .media-body p {
            color: #555555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Profile</h1>
    <div class="profile-info">
        <div class="info">
            <p><strong>Name:</strong> <?php echo $name;?></p>
            <p><strong>Phone Number:</strong> <?php echo $phone;?></p>
            <p><strong>Date of Birth:</strong> <?php echo $birthday;?></p>
            <p><strong>Age:</strong> <?php echo $age;?></p>
            <p><strong>Email:</strong><?php echo $email;?></p>
            <a href="update.php"><button class="edit-button">Edit Profile</button></a><br><br>
            <a href="all_blogs.php"><button class="edit-button">All Blogs</button></a>
        </div>
        <img src="<?php echo $image;?>" alt="User Image">
    </div>
    <div class="blog-form">
        <h2>Write a Blog</h2>
        <form action="control.php" method="post" enctype="multipart/form-data">
            <textarea rows="6" placeholder="Write your blog here..." name="blog_text"></textarea><br>
            <input type="file" accept="image/*" name="blog_image"><br>
            <button class="publish-button" type="submit" name ="publish">Publish</button>
        </form>
    </div>
    <?php
        $stmt = $con->prepare("SELECT * FROM user_blogs WHERE user_id='$id'");
        $stmt->execute();
        $blogs = $stmt->fetchAll();
        ?> 
    <div class="blog-list">
        <h2>My Blogs</h2>
      
        <?php foreach ($blogs as $blog): ?>
            <div class="card">
                <div class="card-header">
                    <p class="card-subtitle">By <?= htmlspecialchars($name) ?> on <?= date('F j, Y, g:i a', strtotime($blog['time'])) ?></p>
                </div>
                <div class="card-body blog-content">
                    <p class="card-text"><?= nl2br(htmlspecialchars($blog['text_blog'])) ?></p>
                    <?php if ($blog['image_blog']): ?>
                        <img src="<?= htmlspecialchars($blog['image_blog']) ?>" class="img-fluid" alt="Blog Image">
                    <?php endif; ?>
                </div>
                <?php if (isset($_SESSION['id'])): ?>
                    <div class="card-footer">
                        <div class="comment-section">
                            <form action="control.php" method="POST" enctype="multipart/form-data" class="comment-form">
                                <div class="form-group">
                                    <textarea name="comment_text" class="form-control" rows="1" placeholder="Write your comment"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="file" name="comment_image" class="form-control-file">
                                </div>
                                <input type="hidden" name="post_id" value="<?= $blog['id'] ?>">
                                <button type="submit" name="comment" class="btn btn-primary">Add Comment</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="card-footer">
                    <?php
                        $stmt = $con->prepare('SELECT blog_comment.*, user_data.full_name,profile_image FROM blog_comment JOIN user_data ON blog_comment.user_id = user_data.id WHERE blog_comment.blog_id = ? ORDER BY blog_comment.time DESC');
                        $stmt->execute([$blog['id']]);
                        $comments = $stmt->fetchAll();
                    ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="media mb-3">

                            <div class="media-body">
                                <img class="mr-3" src="<?= htmlspecialchars($comment['profile_image']) ?>" alt="Comment Image" style="width: 64px; height: 64px; object-fit: cover;">
                                <h6 class="mt-0"><?= htmlspecialchars($comment['full_name']) ?> <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($comment['time'])) ?></small></h6>

                                <p><?= nl2br(htmlspecialchars($comment['text_comment'])) ?></p>
                                <?php if ($comment['image_comment']): ?>
                                <img class="mr-3" src="<?= htmlspecialchars($comment['image_comment']) ?>" alt="Comment Image" style="width: 64px; height: 64px; object-fit: cover;">

                            <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
