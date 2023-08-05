<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <link rel="stylesheet" href="quiz.css">
</head>
<body>
    <div class="quiz-container">
        <form action="quiz.php" method="post">
            <?php
            // Read the questions and options from quiz.txt and display them
            $quizFile = "quiz.txt";
            $lines = file($quizFile);

            $questions = [];
            $currentQuestion = null;

            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^\d+\./', $line)) {
                    if ($currentQuestion !== null) {
                        $questions[] = $currentQuestion;
                    }
                    $currentQuestion = [
                        'question' => substr($line, 2),
                        'options' => []
                    ];
                } elseif ($line !== '') {
                    $currentQuestion['options'][] = $line;
                }
            }

            if ($currentQuestion !== null) {
                $questions[] = $currentQuestion;
            }

            foreach ($questions as $questionData) {
                echo '<p class="question">' . $questionData['question'] . '</p>';
                foreach ($questionData['options'] as $index => $option) {
                    // Use a common name attribute for all radio inputs
                    // Store the user's answers in an array
                    echo '<label><input type="radio" name="answers[' . $questionData['question'] . ']" value="' . htmlspecialchars(trim(substr($option, strpos($option, '.') + 1))) . '"> ' . htmlspecialchars($option) . '</label><br>';
                }
            }
            ?>
            <!-- Move the Submit and Reset buttons here -->
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </div>

    <?php
    // Check if the form has been submitted and display the result if true
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Read the correct answers from answers.txt
        $answersFile = "answers.txt";
        $correctAnswers = file($answersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $score = 0;
        $totalQuestions = count($questions);

        // Check user's answers and calculate score
        if (isset($_POST["answers"]) && is_array($_POST["answers"])) {
            $userAnswers = $_POST["answers"];

            foreach ($userAnswers as $question => $userAnswer) {
                $questionIndex = findQuestionIndex($questions, $question);
                if ($questionIndex !== -1) {
                    $correctAnswer = trim(substr($correctAnswers[$questionIndex], strpos($correctAnswers[$questionIndex], '.') + 1));

                    // Compare user's answer with the correct answer (case-insensitive)
                    if (strcasecmp($userAnswer, $correctAnswer) === 0) {
                        $score++;
                    }
                }
            }
        }

        // Calculate the percentage score
        $percentageScore = ($score / $totalQuestions) * 100;

        // Display the result message after the form submission
        echo '<div class="result-container">';
        // Add a class to the <h2> element based on the percentage score
        if ($percentageScore >= 80) {
            echo '<h2 class="green">You scored ' . round($percentageScore) . '% on the quiz</h2>';
        } elseif ($percentageScore >= 60) {
            echo '<h2 class="yellow">You scored ' . round($percentageScore) . '% on the quiz</h2>';
        } elseif ($percentageScore >= 50) {
            echo '<h2 class="red">You scored ' . round($percentageScore) . '% on the quiz</h2>';
        } else {
            echo '<h2 class="black">You scored ' . round($percentageScore) . '% on the quiz</h2>';
        }
        echo '</div>';
    }

    // Function to find the index of a question in the questions array
    function findQuestionIndex($questions, $question) {
        foreach ($questions as $index => $questionData) {
            if ($questionData['question'] === $question) {
                return $index;
            }
        }
        return -1;
    }
    ?>

    <!-- Last Modified date -->
    <?php
    $lastModified = date("H:i M d, Y T", filemtime(__FILE__));
    echo "<p class='last-modified'>Last Modified: $lastModified</p>";
    ?>
</body>
</html>

