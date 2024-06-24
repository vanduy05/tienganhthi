<?php
session_start(); // Khởi động session
if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
    header('Location: login.php');
    exit();
}

include('connect.php');

// Hàm lấy danh sách câu hỏi và xáo trộn thứ tự
function getAllRandomQuestions($conn) {
    $sql = "SELECT * FROM cauhoi ORDER BY RAND()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy danh sách tất cả câu hỏi và xáo trộn thứ tự
$questions = getAllRandomQuestions($conn);

// Khởi tạo biến điểm số
$score = null;

// Xử lý khi người dùng nộp bài
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answers'])) {
        $answers = $_POST['answers'];
        $score = calculateScore($answers); // Tính điểm số từ câu trả lời
    }
}

// Hàm tính điểm số
function calculateScore($answers) {
    global $conn;
    $sql = "SELECT id, answer FROM cauhoi";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $correctAnswers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $score = 0;
    foreach ($answers as $questionId => $selectedAnswer) {
        if (isset($correctAnswers[$questionId]) && $correctAnswers[$questionId] === $selectedAnswer) {
            $score++;
        }
    }

    return $score;
}

$user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trắc nghiệm online</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    header {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        text-align: center;
        border-radius: 5px 5px 0 0;
    }

    footer {
        background-color: #f8f9fa;
        color: #333;
        padding: 10px 20px;
        text-align: center;
        border-top: 1px solid #ccc;
        border-radius: 0 0 5px 5px;
    }

    .content {
        display: flex;
        flex-wrap: wrap;
    }

    .main {
        flex: 2;
        margin-right: 20px;
    }

    .aside {
        flex: 1;
        text-align: center;
    }

    .links {
        margin-bottom: 20px;
        text-align: justify;
    }

    .links a {
        text-decoration: none;
        color: #007bff;
        margin: 0 10px;
    }

    .links a:hover {
        text-decoration: underline;
    }

    .btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: block;
        margin: 20px auto;
        text-align: center;
        width: 150px;
        text-decoration: none;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .quiz-info {
        margin-top: 20px;
        text-align: center;
    }

    .quiz-info div {
        margin-bottom: 10px;
    }

    .question {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #ccc;
    }

    .options label {
        display: block;
        margin-bottom: 10px;
    }

    .options input[type="radio"] {
        margin-right: 10px;
    }

    .answer {
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
        display: none;
    }

    .correct-answer {
        background-color: #d4edda;
        color: #155724;
    }

    .wrong-answer {
        background-color: #f8d7da;
        color: #721c24;
    }

    #progress-bar {
        margin-top: 20px;
        width: 100%;
        background-color: #e0e0e0;
        height: 20px;
        border-radius: 10px;
        overflow: hidden;
        display: none;
    }

    #progress {
        height: 100%;
        background-color: #007bff;
        width: 0;
        transition: width 0.5s;
    }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Trắc nghiệm online CRE Văn Duy</h1>
        </header>
        <div class="content">
            <div class="main">
                <p>Chào mừng,
                <h2><?php echo htmlspecialchars($user); ?>!</h2>
                </p>
                <div class="links">
                    <a href="profile.php" class="btn-link">Xem thông tin tài khoản</a>
                    <a href="logout.php" class="btn-link">Đăng xuất</a>
                </div>
                <button id="startQuiz" class="btn">Bắt đầu</button>
                <div class="quiz-info">
                    <div id="timer">Thời gian còn lại: <span id="timeDisplay">00:00</span></div>
                    <div id="scoreDisplay">Điểm: <span id="scoreValue">0</span>%</div>
                    <div id="progress-bar">
                        <div id="progress"></div>
                    </div>
                </div>
                <div id="quiz">
                    <div id="quiz-area">
                        <form id="quizForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            method="post">
                            <?php
                                $questionCount = 0; // Biến đếm số thứ tự câu hỏi
                                foreach ($questions as $question) :
                                    $questionCount++;
                                ?>
                            <div class="question" id="question<?php echo $question['id']; ?>"
                                data-answer="<?php echo $question['answer']; ?>">
                                <p><strong>Câu hỏi <?php echo $questionCount; ?>:</strong>
                                    <?php echo $question['question']; ?>
                                </p>
                                <div class="options">
                                    <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A">
                                        <?php echo $question['option_a']; ?></label>
                                    <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B">
                                        <?php echo $question['option_b']; ?></label>
                                    <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C">
                                        <?php echo $question['option_c']; ?></label>
                                    <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D">
                                        <?php echo $question['option_d']; ?></label>
                                </div>
                                <div class="answer correct-answer">
                                    Đáp án đúng: <?php echo $question['answer']; ?>
                                </div>
                                <div class="answer wrong-answer">
                                    Đáp án đúng: <?php echo $question['answer']; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <button type="button" id="submitQuiz" class="btn" style="display: none;">Nộp bài</button>
                            <button type="button" id="retryQuiz" class="btn" style="display: none;">Làm lại</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- <aside class="aside">

            </aside> -->
        </div>
        <footer>
            <p>&copy; 2024 Trắc nghiệm online. All rights reserved.</p>
        </footer>
    </div>

    <script>
    var timeLimit = 3600; // 60 minutes in seconds
    var timerInterval;
    var timerRunning = false;
    var totalQuestions = <?php echo count($questions); ?>;
    var score = 0;

    function startTimer() {
        var timerElement = document.getElementById('timeDisplay');
        var timer = timeLimit;
        var minutes, seconds;

        timerInterval = setInterval(function() {
            timerRunning = true;
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            timerElement.innerHTML = minutes + ':' + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                timerRunning = false;
                document.getElementById('submitQuiz').disabled = true;
                alert('Thời gian đã hết!');
                finishQuiz();
            }
        }, 1000);
    }

    function finishQuiz() {
        clearInterval(timerInterval);
        timerRunning = false;

        var questions = document.querySelectorAll('.question');
        var correctCount = 0;

        questions.forEach(function(question) {
            var correctAnswer = question.querySelector('.correct-answer');
            var wrongAnswer = question.querySelector('.wrong-answer');
            var selectedAnswer = question.querySelector('input[name^="answers"]:checked');
            var correctAnswerValue = question.getAttribute('data-answer');

            if (selectedAnswer) {
                if (selectedAnswer.value === correctAnswerValue) {
                    selectedAnswer.parentNode.style.backgroundColor = '#d4edda';
                    selectedAnswer.parentNode.style.color = '#155724';
                    correctCount++;
                } else {
                    selectedAnswer.parentNode.style.backgroundColor = '#f8d7da';
                    selectedAnswer.parentNode.style.color = '#721c24';
                }
            }

            correctAnswer.style.display = 'block';
            wrongAnswer.style.display = 'none';
        });

        score = Math.round((correctCount / totalQuestions) * 100);
        document.getElementById('scoreValue').textContent = score;
        document.getElementById('scoreDisplay').style.display = 'block';
        document.getElementById('submitQuiz').style.display = 'none';
        document.getElementById('retryQuiz').style.display = 'block';

        // Cập nhật thanh tiến trình
        var progressElement = document.getElementById('progress');
        progressElement.style.width = score + '%';
        document.getElementById('progress-bar').style.display = 'block';
    }

    document.getElementById('startQuiz').addEventListener('click', function() {
        startTimer();
        document.getElementById('startQuiz').style.display = 'none';
        document.getElementById('submitQuiz').style.display = 'block';
    });

    document.getElementById('submitQuiz').addEventListener('click', function() {
        finishQuiz();
    });

    document.getElementById('retryQuiz').addEventListener('click', function() {
        location.reload();
    });
    </script>
</body>

</html>