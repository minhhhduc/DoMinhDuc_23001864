<?php
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

function calculateAverageScore($students) {
    if (empty($students)) {
        return 0;
    }

    $sum=0;
    foreach ($students as $student) {
        $sum += $student["score"];
    }
    return $sum / count($students);
}

function getRank($score) {
    if ($score > 10 || $score < 0) {
        throw new InvalidArgumentException("Score must be between 0 and 10");
    }

    if ($score >= 8) {
        return "Giỏi";
    } elseif ($score >= 6.5) {
        return "Khá";
    } elseif ($score >= 5) {
        return "Trung bình";
    } else {
        return "Yếu";
    }
}

function displayStudent($student) {
    echo "<tr>
        <td>" . $student["name"]. "</td>
        <td>" . $student["age"]. "</td>
        <td>" . $student["score"]. "</td>
        <td>" . getRank($student["score"]). "</td>
    </tr>";
}
?>

<table>
    <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Score</th>
        <th>Rank</th>
    </tr>
    <?php 
        foreach ($students as $student) {
            displayStudent($student);
        }
    ?>
</table>
<p>Score average = <?= calculateAverageScore($students) ?></p>
