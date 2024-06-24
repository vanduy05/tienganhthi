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
    $_SESSION['profile_error'] = 'Không tìm thấy thông tin tài khoản.';
    header('Location: index.php');
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
    <title>Thông tin tài khoản</title>
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

    .avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th,
    table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    table th {
        background-color: #f2f2f2;
    }

    .btn-edit {
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

    .btn-edit:hover {
        background-color: #0056b3;
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
        <h2>Thông tin tài khoản</h2>
        <?php if (isset($_SESSION['profile_error'])) : ?>
        <div class="error-message"><?php echo $_SESSION['profile_error']; ?></div>
        <?php unset($_SESSION['profile_error']); ?>
        <?php endif; ?>
        <img src="<?php echo $avatar; ?>" alt="Avatar" class="avatar">
        <table>
            <tr>
                <th>Thông tin</th>
                <th>Chi tiết</th>
            </tr>
            <tr>
                <td>Tên đăng nhập:</td>
                <td><?php echo $username; ?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td><?php echo $phone; ?></td>
            </tr>
        </table>
        <a href="edit_profile.php" class="btn-edit">Chỉnh sửa thông tin</a>
        <a href="trangthi.php" class="btn-back">Quay lại trang thi</a>
    </div>
</body>

</html>