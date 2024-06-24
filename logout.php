<!-- logout.php -->
<?php
session_start();
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session

// Chuyển hướng người dùng về trang đăng nhập
header('Location: index.php');
exit();
?>