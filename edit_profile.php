<?php
session_start();
include('connect.php');

if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng từ CSDL
$sql = "SELECT * FROM users WHERE id_users = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['profile_update_error'] = 'Không tìm thấy thông tin tài khoản.';
    header('Location: profile.php');
    exit();
}

// Lấy thông tin từ $user để sử dụng trong HTML
$username = isset($user['username']) ? htmlspecialchars($user['username']) : '';
$email = isset($user['email']) ? htmlspecialchars($user['email']) : '';
$phone = isset($user['phone']) ? htmlspecialchars($user['phone']) : '';
$avatar = isset($user['avatar_path']) ? htmlspecialchars($user['avatar_path']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin tài khoản</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
    }

    form {
        text-align: left;
        margin-top: 20px;
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

    button[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 16px;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }

    .btn-back {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-back:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Chỉnh sửa thông tin tài khoản</h2>
        <?php if (isset($_SESSION['profile_update_error'])) : ?>
        <div class="error-message"><?php echo $_SESSION['profile_update_error']; ?></div>
        <?php unset($_SESSION['profile_update_error']); ?>
        <?php endif; ?>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required>
            </div>
            <div class="form-group">
                <label for="avatar">Ảnh đại diện:</label><br>
                <?php if ($avatar) : ?>
                <img src="<?php echo $avatar; ?>" alt="Avatar" class="avatar-preview"><br>
                <?php endif; ?>
                <input type="file" id="avatar" name="avatar">
            </div>
            <button type="submit">Lưu thay đổi</button>
        </form>
        <a href="profile.php" class="btn-back">Quay lại trang profile</a>
    </div>
</body>

</html>