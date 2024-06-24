<?php
session_start();
include('connect.php'); // Kết nối đến CSDL

// Xử lý khi người dùng submit form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra nếu người dùng đã nhập username và password
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Kiểm tra username và password trong CSDL cho admin
        $sql = "SELECT * FROM admins WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Đăng nhập thành công, lưu thông tin vào session
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_id'] = $admin['id']; // Lưu id của admin vào session
            $_SESSION['isAdminLoggedIn'] = true;

            // Chuyển hướng tới trang admin dashboard (hoặc bất kỳ trang nào cần đăng nhập)
            header('Location: tracnghiem.php');
            exit;
        } else {
            // Đăng nhập không thành công
            $error_message = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        }
    } else {
        $error_message = 'Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị viên</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
    }

    .container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"],
    input[type="password"] {
        width: 90%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    button[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    .error-message {
        color: red;
        margin-bottom: 10px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Đăng nhập quản trị viên</h2>
        <?php if (isset($error_message)) : ?>
        <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>

</html>