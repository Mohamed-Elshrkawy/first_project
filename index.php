<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-wrap">
                <div class="form-header">
                    <h2>Login & Sign Up</h2>
                </div>
                <div class="tabs">
                    <div class="tab tab-active" data-tab="login">Login</div>
                    <div class="tab" data-tab="signup">Sign Up</div>
                </div>
                <div class="tab-content">
                    <form action="control.php" method="post" id="login" class="form active-form" >
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" name="Login">Login</button>
                    </form>
                    <form action="control.php" method="post" id="signup" class="form" enctype="multipart/form-data">
                        <input type="text" name="fullname" placeholder="Full Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="tel" name="phone" placeholder="Phone" required>
                        <input type="date" name="birthday" placeholder="Birthday" required>
                        <input type="file" name="image">
                        <button type="submit" name="SignUp">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
