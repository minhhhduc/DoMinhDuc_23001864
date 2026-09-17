<?php

function findBestStudent($students)
{
    if (empty($students)) return null;
    $bestStudent = $students[0];
    foreach ($students as $student) {
        if ($student['score'] > $bestStudent['score']) $bestStudent = $student;
    }
    return $bestStudent;
}

function findWorstStudent($students)
{
    if (empty($students)) return null;
    $worstStudent = $students[0];
    foreach ($students as $student) {
        if ($student['score'] < $worstStudent['score']) $worstStudent = $student;
    }
    return $worstStudent;
}

function countPassedStudents($students)
{
    $count = 0;
    foreach ($students as $student) {
        if ($student['score'] >= 5) $count++;
    }
    return $count;
}

function findStudentByName($students, $name)
{
    foreach ($students as $student) {
        if ($student['name'] === $name) return $student;
    }
    return null;
}

$students = [
        [
            "name" => "Nguyen Van An",
            "age" => 20,
            "score" => 8.5
        ],
        [
            "name" => "Tran Thi Binh",
            "age" => 21,
            "score" => 6.5
        ],
        [
            "name" => "Le Van Cuong",
            "age" => 19,
            "score" => 4.5
        ],
        [
            "name" => "Pham Thi Dung",
            "age" => 20,
            "score" => 7.5
        ]
    ];

$bestStudent = findBestStudent($students);
$worstStudent = findWorstStudent($students);
$student = findStudentByName($students, 'Tran Thi Binh');
?>
<table>
    <tr><th>Name</th><th>Age</th><th>Score</th></tr>
    <?php foreach ($students as $item): ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['age'] ?></td>
            <td><?= $item['score'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<p>Best student: <?= $bestStudent['name'] ?> (<?= $bestStudent['score'] ?>)</p>
<p>Worst student: <?= $worstStudent['name'] ?> (<?= $worstStudent['score'] ?>)</p>
<p>Passed students: <?= countPassedStudents($students) ?></p>
<p>Search result: <?= $student ? $student['name'] : 'Not found' ?></p>
