<?php

class Student
{
    public $name;
    public $age;
    public $score;

    public function __construct($name, $age, $score)
    {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    public function getRank()
    {
        if ($this->score >= 8) {
            return 'Giỏi';
        }
        if ($this->score >= 6.5) {
            return 'Khá';
        }
        if ($this->score >= 5) {
            return 'Trung bình';
        }

        return 'Yếu';
    }

    public function isPassed()
    {
        return $this->score >= 5;
    }

    public function display()
    {
        echo '<tr>';
        echo '<td>' . $this->name . '</td>';
        echo '<td>' . $this->age . '</td>';
        echo '<td>' . $this->score . '</td>';
        echo '<td>' . $this->getRank() . '</td>';
        echo '<td>' . ($this->isPassed() ? 'Passed' : 'Failed') . '</td>';
        echo '</tr>';
    }
}

function findBestStudentObject($students)
{
    if (empty($students)) {
        return null;
    }

    $bestStudent = $students[0];
    foreach ($students as $student) {
        if ($student->score > $bestStudent->score) {
            $bestStudent = $student;
        }
    }

    return $bestStudent;
}

function countPassedStudentObjects($students)
{
    $count = 0;
    foreach ($students as $student) {
        if ($student->isPassed()) {
            $count++;
        }
    }

    return $count;
}

function calculateAverageScore($students)
{
    if (empty($students)) {
        return 0;
    }

    $total = 0;
    foreach ($students as $student) {
        $total += $student->score;
    }

    return $total / count($students);
}

$student1 = new Student('Nguyen Van An', 20, 8.5);
$student2 = new Student('Tran Thi Binh', 21, 6.5);
$student3 = new Student('Le Van Cuong', 19, 4.5);
$student4 = new Student('Pham Thi Dung', 20, 7.5);

$students = [$student1, $student2, $student3, $student4];
$bestStudent = findBestStudentObject($students);
?>
<table>
    <tr><th>Name</th><th>Age</th><th>Score</th><th>Rank</th><th>Result</th></tr>
    <?php foreach ($students as $student) { $student->display(); } ?>
</table>
<p>Best student: <?= $bestStudent->name ?> (<?= $bestStudent->score ?>)</p>
<p>Passed students: <?= countPassedStudentObjects($students) ?></p>
<p>Class average score: <?= number_format(calculateAverageScore($students), 2) ?></p>
