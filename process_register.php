<?php
session_start();

include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

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

    // Thêm người dùng vào CSDL
    $sql = "INSERT INTO users (username, password, email, phone, avatar_path) VALUES (:username, :password, :email, :phone, :avatar_path)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); // Thường nên mã hóa mật khẩu trước khi lưu vào CSDL
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':avatar_path', $avatarPath);
    $stmt->execute();

    $_SESSION['register_success'] = 'Đăng ký thành công. Bây giờ bạn có thể đăng nhập.';
    header('Location: index.php');
    exit();
}
?>