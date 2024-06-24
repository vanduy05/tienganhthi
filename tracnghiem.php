<?php
session_start();
include('connect.php'); // Đảm bảo file connect.php chứa kết nối đến CSDL

// Kiểm tra nếu admin chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin.php');
    exit;
}

// Xử lý đăng xuất
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Xử lý thêm câu hỏi
if (isset($_POST['action']) && $_POST['action'] == 'add_question') {
    $question = $_POST['question'];
    $optionA = $_POST['option_a'];
    $optionB = $_POST['option_b'];
    $optionC = $_POST['option_c'];
    $optionD = $_POST['option_d'];
    $answer = $_POST['answer'];

    try {
        $sql = $conn->prepare("INSERT INTO cauhoi (question, option_a, option_b, option_c, option_d, answer) VALUES (:question, :optionA, :optionB, :optionC, :optionD, :answer)");
        $sql->bindParam(':question', $question, PDO::PARAM_STR);
        $sql->bindParam(':optionA', $optionA, PDO::PARAM_STR);
        $sql->bindParam(':optionB', $optionB, PDO::PARAM_STR);
        $sql->bindParam(':optionC', $optionC, PDO::PARAM_STR);
        $sql->bindParam(':optionD', $optionD, PDO::PARAM_STR);
        $sql->bindParam(':answer', $answer, PDO::PARAM_STR);

        if ($sql->execute()) {
            echo "Thêm câu hỏi thành công";
        } else {
            echo "Thêm câu hỏi thất bại";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
    exit;
}

// Xử lý xóa câu hỏi
if (isset($_POST['action']) && $_POST['action'] == 'delete_question') {
    $id = $_POST['id'];
    try {
        $sql = $conn->prepare("DELETE FROM cauhoi WHERE id = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($sql->execute()) {
            echo "Xóa câu hỏi thành công";
        } else {
            echo "Xóa câu hỏi thất bại";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
    exit;
}

// Xử lý sửa câu hỏi
if (isset($_POST['action']) && $_POST['action'] == 'edit_question') {
    $id = $_POST['id'];
    $question = $_POST['question'];
    $optionA = $_POST['option_a'];
    $optionB = $_POST['option_b'];
    $optionC = $_POST['option_c'];
    $optionD = $_POST['option_d'];
    $answer = $_POST['answer'];

    try {
        $sql = $conn->prepare("UPDATE cauhoi SET question = :question, option_a = :optionA, option_b = :optionB, option_c = :optionC, option_d = :optionD, answer = :answer WHERE id = :id");
        $sql->bindParam(':question', $question, PDO::PARAM_STR);
        $sql->bindParam(':optionA', $optionA, PDO::PARAM_STR);
        $sql->bindParam(':optionB', $optionB, PDO::PARAM_STR);
        $sql->bindParam(':optionC', $optionC, PDO::PARAM_STR);
        $sql->bindParam(':optionD', $optionD, PDO::PARAM_STR);
        $sql->bindParam(':answer', $answer, PDO::PARAM_STR);
        $sql->bindParam(':id', $id, PDO::PARAM_INT);

        if ($sql->execute()) {
            echo "Sửa câu hỏi thành công";
        } else {
            echo "Sửa câu hỏi thất bại";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
    exit;
}

// Xử lý xóa tất cả câu hỏi
if (isset($_POST['action']) && $_POST['action'] == 'delete_all_questions') {
    try {
        $sql = $conn->prepare("DELETE FROM cauhoi");
        
        if ($sql->execute()) {
            echo "Xóa tất cả câu hỏi thành công";
        } else {
            echo "Xóa tất cả câu hỏi thất bại";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý câu hỏi</title>
    <!-- Add Bootstrap CSS for styling and modal functionality -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    table {
        width: 95%;
        margin: 20px auto;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 10px;
        text-align: center;
        font-size: 18px;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    .row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }

    .row .them {
        text-align: center;
    }

    @media (max-width: 768px) {

        table,
        th,
        td {
            font-size: 14px;
            padding: 5px;
        }

        .btn {
            margin: 5px 0;
            width: 30%;
        }

        .row {
            flex-direction: column;
            align-items: flex-start;
        }

        .row .them {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 480px) {

        th,
        td {
            display: block;
            text-align: right;
        }

        th {
            text-align: center;
        }

        tr {
            margin-bottom: 10px;
            display: block;
            border: none;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td::before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
        }
    }
    </style>
</head>

<body>


    <div class="row">
        <div class="them">
            <button id="btnQuestion" class="btn btn-primary">Thêm câu hỏi</button>
            <button id="btnDeleteAll" class="btn btn-danger ml-3">Xóa tất cả câu hỏi</button>
        </div>
        <!-- Nút Đăng Xuất -->
        <form id="logoutForm" action="" method="POST" style="display:block">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn btn-danger ml-3" style="position: absolute; top: 20px; right: 20px;">Đăng
                xuất</button>
        </form>
    </div>
    <h1 class="text-center">Bảng danh sách câu hỏi</h1>
    <table>
        <thead>
            <tr>
                <th>Mã câu hỏi</th>
                <th>Nội dung câu hỏi</th>
                <th>Đáp án A</th>
                <th>Đáp án B</th>
                <th>Đáp án C</th>
                <th>Đáp án D</th>
                <th>Đáp án đúng</th>
                <th>Xóa</th>
                <th>Sửa</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch and display questions from the database
            $sql = $conn->prepare("SELECT * FROM cauhoi");
            $sql->execute();
            while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$result['id']}</td>";
                echo "<td>{$result['question']}</td>";
                echo "<td>{$result['option_a']}</td>";
                echo "<td>{$result['option_b']}</td>";
                echo "<td>{$result['option_c']}</td>";
                echo "<td>{$result['option_d']}</td>";
                echo "<td>{$result['answer']}</td>";
                echo "<td><button class='btn btn-danger btn-delete' data-id='{$result['id']}'>Xóa</button></td>";
                echo "<td><button class='btn btn-warning btn-edit' data-id='{$result['id']}' data-question='{$result['question']}' data-option-a='{$result['option_a']}' data-option-b='{$result['option_b']}' data-option-c='{$result['option_c']}' data-option-d='{$result['option_d']}' data-answer='{$result['answer']}'>Sửa</button></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal for adding/editing questions -->
    <div class="modal fade" id="modalQuestion" tabindex="-1" role="dialog" aria-labelledby="modalQuestionLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalQuestionLabel">Thêm/Sửa câu hỏi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="questionId" value="">
                    <div class="form-group">
                        <label for="question">Nội dung câu hỏi:</label>
                        <input type="text" class="form-control" id="question" required>
                    </div>
                    <div class="form-group">
                        <label for="optionA">Đáp án A:</label>
                        <input type="text" class="form-control" id="optionA" required>
                    </div>
                    <div class="form-group">
                        <label for="optionB">Đáp án B:</label>
                        <input type="text" class="form-control" id="optionB" required>
                    </div>
                    <div class="form-group">
                        <label for="optionC">Đáp án C:</label>
                        <input type="text" class="form-control" id="optionC" required>
                    </div>
                    <div class="form-group">
                        <label for="optionD">Đáp án D:</label>
                        <input type="text" class="form-control" id="optionD" required>
                    </div>
                    <div class="form-group">
                        <label for="answer">Đáp án đúng:</label>
                        <select class="form-control" id="answer" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" id="btnSaveQuestion" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        // Hiển thị modal thêm câu hỏi
        $('#btnQuestion').click(function() {
            $('#modalQuestionLabel').text('Thêm câu hỏi');
            $('#questionId').val('');
            $('#question').val('');
            $('#optionA').val('');
            $('#optionB').val('');
            $('#optionC').val('');
            $('#optionD').val('');
            $('#answer').val('A');
            $('#modalQuestion').modal('show');
        });

        // Xử lý sự kiện lưu câu hỏi
        $('#btnSaveQuestion').click(function() {
            var id = $('#questionId').val();
            var question = $('#question').val();
            var optionA = $('#optionA').val();
            var optionB = $('#optionB').val();
            var optionC = $('#optionC').val();
            var optionD = $('#optionD').val();
            var answer = $('#answer').val();
            var action = (id === '') ? 'add_question' : 'edit_question';

            $.ajax({
                type: 'POST',
                url: 'tracnghiem.php',
                data: {
                    action: action,
                    id: id,
                    question: question,
                    option_a: optionA,
                    option_b: optionB,
                    option_c: optionC,
                    option_d: optionD,
                    answer: answer
                },
                success: function(response) {
                    alert(response);
                    location.reload(); // Tải lại trang sau khi lưu thành công
                }
            });
        });

        // Hiển thị modal sửa câu hỏi
        $('.btn-edit').click(function() {
            $('#modalQuestionLabel').text('Sửa câu hỏi');
            var id = $(this).data('id');
            var question = $(this).data('question');
            var optionA = $(this).data('option-a');
            var optionB = $(this).data('option-b');
            var optionC = $(this).data('option-c');
            var optionD = $(this).data('option-d');
            var answer = $(this).data('answer');

            $('#questionId').val(id);
            $('#question').val(question);
            $('#optionA').val(optionA);
            $('#optionB').val(optionB);
            $('#optionC').val(optionC);
            $('#optionD').val(optionD);
            $('#answer').val(answer);
            $('#modalQuestion').modal('show');
        });

        // Xử lý sự kiện xóa câu hỏi
        $('.btn-delete').click(function() {
            var id = $(this).data('id');
            if (confirm('Bạn có chắc muốn xóa câu hỏi này không?')) {
                $.ajax({
                    type: 'POST',
                    url: 'tracnghiem.php',
                    data: {
                        action: 'delete_question',
                        id: id
                    },
                    success: function(response) {
                        alert(response);
                        location.reload(); // Tải lại trang sau khi xóa thành công
                    }
                });
            }
        });

        // Xử lý sự kiện xóa tất cả câu hỏi
        $('#btnDeleteAll').click(function() {
            if (confirm('Bạn có chắc muốn xóa tất cả câu hỏi không?')) {
                $.ajax({
                    type: 'POST',
                    url: 'tracnghiem.php',
                    data: {
                        action: 'delete_all_questions'
                    },
                    success: function(response) {
                        alert(response);
                        location.reload(); // Tải lại trang sau khi xóa thành công
                    }
                });
            }
        });
    });
    </script>
</body>

</html>