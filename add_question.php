<?php
// Kết nối đến cơ sở dữ liệu
include('connect.php');

// Kiểm tra nếu là phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form gửi lên
    $question = isset($_POST['question']) ? $_POST['question'] : '';
    $optionA = isset($_POST['option_a']) ? $_POST['option_a'] : '';
    $optionB = isset($_POST['option_b']) ? $_POST['option_b'] : '';
    $optionC = isset($_POST['option_c']) ? $_POST['option_c'] : '';
    $optionD = isset($_POST['option_d']) ? $_POST['option_d'] : '';
    $answer = isset($_POST['answer']) ? $_POST['answer'] : '';

    // Thực hiện truy vấn để chèn câu hỏi vào cơ sở dữ liệu
    $sql = "INSERT INTO cauhoi (question, option_a, option_b, option_c, option_d,answer) 
            VALUES (:question, :optionA, :optionB, :optionC, :optionD, :answer)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':optionA', $optionA);
    $stmt->bindParam(':optionB', $optionB);
    $stmt->bindParam(':optionC', $optionC);
    $stmt->bindParam(':optionD', $optionD);
    $stmt->bindParam(':answer', $answer);

    // Thực thi truy vấn và kiểm tra kết quả
    if ($stmt->execute()) {
        echo "Thêm câu hỏi thành công";
    } else {
        echo "Thêm câu hỏi thất bại";
    }
} else {
    echo "Phương thức không hợp lệ";
}
?>