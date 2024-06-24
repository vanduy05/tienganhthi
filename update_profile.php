<?php
session_start();
include('connect.php');

if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Xử lý tải lên ảnh đại diện nếu có
    if (!empty($_FILES['avatar']['name'])) {
        $avatarDir = "uploads/";
        $fileName = basename($_FILES['avatar']['name']);
        $targetFilePath = $avatarDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                $avatarPath = $targetFilePath;
            } else {
                $_SESSION['profile_update_error'] = 'Có lỗi khi tải lên ảnh đại diện.';
                header('Location: edit_profile.php');
                exit();
            }
        } else {
            $_SESSION['profile_update_error'] = 'Chỉ cho phép tải lên các định dạng JPG, JPEG, PNG và GIF.';
            header('Location: edit_profile.php');
            exit();
        }
    }

    // Cập nhật thông tin vào CSDL
    $sql = "UPDATE users SET username = :username, email = :email, phone = :phone";
    if (isset($avatarPath)) {
        $sql .= ", avatar_path = :avatar_path";
    }
    $sql .= " WHERE id_users = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    if (isset($avatarPath)) {
        $stmt->bindParam(':avatar_path', $avatarPath);
    }
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['profile_update_success'] = 'Thông tin tài khoản đã được cập nhật thành công.';
    header('Location: profile.php');
    exit();
} else {
    header('Location: edit_profile.php');
    exit();
}
?>