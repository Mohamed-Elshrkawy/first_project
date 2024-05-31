<?php
    require 'connect.php';
    session_start();
    $stmt = $con->prepare('SELECT user_blogs.*, user_data.full_name,profile_image FROM user_blogs JOIN user_data ON user_blogs.user_id = user_data.id ORDER BY user_blogs.time DESC');
    $stmt->execute();
    $blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blogs</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f7f9fc;
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
        .btn-like {
            color: #007bff;
            cursor: pointer;
        }
        .btn-like:hover {
            color: #0056b3;
        }
        .comment-section {
            margin-top: 20px;
        }
        .comment-section h5 {
            font-weight: bold;
            color: #333333;
        }
        .comment-form .form-group {
            margin-bottom: 15px;
        }
        .comment-form button {
            background-color: #007bff;
            color: #ffffff;
        }
        .comment-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">All Blogs</h1>
        <?php foreach ($blogs as $blog): ?>
        <!-- Blog Post -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Sample Blog Title</h2>
                <p class="card-subtitle">By<?= htmlspecialchars($blog['full_name']) ?> on <?= date('F j, Y, g:i a', strtotime($blog['time'])) ?></p>
            </div>
            <div class="card-body blog-content">
                <p class="card-text"><?= nl2br(htmlspecialchars($blog['text_blog'])) ?></p>
                <?php if ($blog['image_blog']): ?>
                    <img src="<?= htmlspecialchars($blog['image_blog']) ?>" class="img-fluid" alt="Blog Image">
                <?php endif; ?>
            </div>
            <!-- Like Button -->
            <?php
                $stmt = $con->prepare('SELECT username FROM post_likes WHERE post_id = ?');
                $stmt->execute([$blog['id']]);
                $post_likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $post_likes_count = count($post_likes);
            ?>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="control.php?like_post=<?= $blog['id'] ?>" class="btn-like"><i class="far fa-thumbs-up"></i> Like</a>
                <?php endif; ?>
                <span>
                    <?= $post_likes_count ?> likes
                    <?php if ($post_likes_count > 0): ?>
                        <a href="#post_likes_<?= $blog['id'] ?>" data-toggle="collapse" aria-expanded="false" aria-controls="post_likes_<?= $blog['id'] ?>">View likers</a>
                    <?php endif; ?>
                </span>
            </div>
            <?php if ($post_likes_count > 0): ?>
            <div class="collapse" id="post_likes_<?= $blog['id'] ?>">
                <div class="card-footer">
                    <ul>
                        <?php foreach ($post_likes as $like): ?>
                            <li><?= htmlspecialchars($like['username']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>              
            <!-- Comment Section -->
            <?php if (isset($_SESSION['id'])): ?>
            <div class="card-footer">
                <div class="comment-section">
                    <form action="control.php" method="POST" enctype="multipart/form-data" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $blog['id'] ?>">
                        <div class="form-group">
                            <textarea name="comment_content" class="form-control" rows="3" placeholder="Write your comment" required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="file" name="comment_image" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            <!-- Comments -->
            <div class="card-footer">
                <?php
                    $stmt = $con->prepare('SELECT blog_comment.*, user_data.full_name,profile_image FROM blog_comment JOIN user_data ON blog_comment.user_id = user_data.id WHERE blog_comment.blog_id = ? ORDER BY blog_comment.time DESC');
                    $stmt->execute([$blog['id']]);
                    $comments = $stmt->fetchAll();
                ?>
                <?php foreach ($comments as $comment): ?>
                <h5>Comments</h5>
                <div class="media mb-3">
                    <img class="mr-3" src="<?= htmlspecialchars($comment['profile_image']) ?>" alt="Comment Image" style="width: 64px; height: 64px; object-fit: cover;">
                    <div class="media-body">
                        <h6 class="mt-0"><?= htmlspecialchars($comment['full_name']) ?> <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($comment['time'])) ?></small></h6>
                        <p><?= nl2br(htmlspecialchars($comment['text_comment'])) ?></p>
                        <?php if ($comment['image_comment']): ?>
                            <img class="mr-3" src="<?= htmlspecialchars($comment['image_comment']) ?>" alt="Comment Image" style="width: 64px; height: 64px; object-fit: cover;">
                        <?php endif; ?>
                        <!-- Like Button -->
                        <?php
                            $stmt = $con->prepare('SELECT username FROM comment_likes WHERE comment_id = ?');
                            $stmt->execute([$comment['id']]);
                            $comment_likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $comment_likes_count = count($comment_likes);
                        ?>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="control.php?like_comment=<?= $comment['id'] ?>" class="btn-like"><i class="far fa-thumbs-up"></i> Like</a>
                            <?php endif; ?>
                            <span>
                                <?= $comment_likes_count ?> likes
                                <?php if ($comment_likes_count > 0): ?>
                                    <a href="#post_likes_<?= $comment['id'] ?>" data-toggle="collapse" aria-expanded="false" aria-controls="post_likes_<?= $blog['id'] ?>">View likers</a>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($comment_likes_count > 0): ?>
                        <div class="collapse" id="post_likes_<?= $comment['id'] ?>">
                            <div class="card-footer">
                                <ul>
                                    <?php foreach ($comment_likes as $like): ?>
                                        <li><?= htmlspecialchars($like['username']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
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
