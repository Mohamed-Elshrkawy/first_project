<?php
session_start();
$id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>up date</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group input[type="tel"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
        }
        .form-group input[type="file"] {
            margin-top: 5px;
        }
        .form-group input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        
    input, button {
        display: block;
        margin-bottom: 10px;
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    </style>
</head>
<body>
 <?php
include ('connect.php');

$sql = "SELECT * FROM user_data WHERE id = '$id'";
$stmt = $con->query($sql);
if($stmt->rowCount()>0){
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo"
    <div class='container'>
        <h2>Edit User Information</h2>
        <form action='control.php' method='post' enctype='multipart/form-data'>
            <div class='form-group'>
                <label for='fullname'>Full Name:</label>
                <input type='text' name='fullname' value ='$row[full_name]' required>
            </div>
            <div class='form-group'>
                <label for='email'>email:</label>
                <input type='text' name='email' value ='$row[email]' required>
            </div>
            <div class='form-group'>
                <label for='birthday'>Birth Day:</label>
                <input type='date' name='birthday' value ='$row[date_birth]' required>
            </div>
            <div class='form-group'>
                <label for='phone'>Phone:</label>
                <input type='text' name='phone' value =' $row[phone]' required>
            </div>
            <div class='form-group'>
            <button type='submit' name='update'>up date</button>
            </div>
        </form>
    </div>
    ";
}
}
?> 
</body>
</html>