<?php
session_start();

include('connect.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Mật khẩu và xác nhận mật khẩu không khớp.';
        header('Location: register.php');
        exit();
    }

    // Xử lý tải lên ảnh đại diện
    $avatarDir = "uploads/";
    $avatarPath = '';

    if (!empty($_FILES['avatar']['name'])) {
        $fileName = basename($_FILES['avatar']['name']);
        $targetFilePath = $avatarDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Kiểm tra định dạng của tệp tin
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload tệp tin lên server
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                $avatarPath = $targetFilePath;
            } else {
                $_SESSION['register_error'] = 'Có lỗi xảy ra khi tải ảnh lên.';
                header('Location: register.php');
                exit();
            }
        } else {
            $_SESSION['register_error'] = 'Chỉ cho phép tải lên các định dạng JPG, JPEG, PNG và GIF.';
            header('Location: register.php');
            exit();
        }
    } else {
        $_SESSION['register_error'] = 'Vui lòng chọn một tệp tin để tải lên.';
        header('Location: register.php');
        exit();
    }

    // Kiểm tra tên đăng nhập đã tồn tại hay chưa
    $sql_check_username = "SELECT COUNT(*) AS count FROM users WHERE username = :username";
    $stmt_check_username = $conn->prepare($sql_check_username);
    $stmt_check_username->bindParam(':username', $username);
    $stmt_check_username->execute();
    $row_username = $stmt_check_username->fetch(PDO::FETCH_ASSOC);

    if ($row_username['count'] > 0) {
        $_SESSION['register_error'] = 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.';
        header('Location: register.php');
        exit();
    }

    // Kiểm tra email đã tồn tại hay chưa
    $sql_check_email = "SELECT COUNT(*) AS count FROM users WHERE email = :email";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bindParam(':email', $email);
    $stmt_check_email->execute();
    $row_email = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

    if ($row_email['count'] > 0) {
        $_SESSION['register_error'] = 'Email đã tồn tại, vui lòng chọn email khác.';
        header('Location: register.php');
        exit();
    }

    // Kiểm tra số điện thoại đã tồn tại hay chưa
    if (!empty($phone)) {
        $sql_check_phone = "SELECT COUNT(*) AS count FROM users WHERE phone = :phone";
        $stmt_check_phone = $conn->prepare($sql_check_phone);
        $stmt_check_phone->bindParam(':phone', $phone);
        $stmt_check_phone->execute();
        $row_phone = $stmt_check_phone->fetch(PDO::FETCH_ASSOC);

        if ($row_phone['count'] > 0) {
            $_SESSION['register_error'] = 'Số điện thoại đã tồn tại, vui lòng chọn số điện thoại khác.';
            header('Location: register.php');
            exit();
        }
    }

    // Thêm người dùng vào CSDL
    $sql_insert = "INSERT INTO users (username, password, email, phone, avatar_path) VALUES (:username, :password, :email, :phone, :avatar_path)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bindParam(':username', $username);
    $stmt_insert->bindParam(':password', $password); // Thường nên mã hóa mật khẩu trước khi lưu vào CSDL
    $stmt_insert->bindParam(':email', $email);
    $stmt_insert->bindParam(':phone', $phone);
    $stmt_insert->bindParam(':avatar_path', $avatarPath);
    $stmt_insert->execute();

    $_SESSION['register_success'] = 'Đăng ký thành công. Bây giờ bạn có thể đăng nhập.';
    header('Location: index.php'); // Chuyển hướng đến trang đăng nhập sau khi đăng ký thành công
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-72pW3+HgNAOsKN6CZiUtZGXMFPlxQ8dS2jedPzj4RHxH3d3Ew4APfHcLnzPQWJnY/vB8+WOb1B95Q4xnmU3Plg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    input[type="password"],
    input[type="email"] {
        width: 90%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    input[type="file"] {
        width: 100%;
        padding: 8px;
        font-size: 16px;
    }

    .password-toggle {
        position: relative;
    }

    .password-toggle i {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
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
        <h2>Đăng ký tài khoản</h2>
        <?php if (isset($_SESSION['register_error'])) : ?>
        <p class="error-message"><?php echo $_SESSION['register_error'];
                                        unset($_SESSION['register_error']); ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="password-toggle">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="avatar">Ảnh đại diện:</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>
            <button type="submit">Đăng ký</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">Đã có tài khoản? <a href="index.php">Đăng nhập ngay</a></p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
        integrity="sha512-2+0d/RM8AfLBFJkjDKY1b/ckkCx3mwk5kpssI6Jy3J7xuNKo6vzMjFzNJf1P+QfoR6GT7jDh0WmB4nwcUHs8DQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        var passwordInput = document.getElementById('password');
        var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
    </script>
</body>

</html>