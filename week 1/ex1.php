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
?>

<table>
    <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Score</th>
    </tr>
    <?php
        $sum=0;
        foreach ($students as $student) {
            echo "<tr>
                <td>" . $student['name'] . "</td>
                <td>" . $student['age'] . "</td>
                <td>" . $student['score'] . "</td>
            </tr>";
            $sum += $student["score"];
        }
    ?>
</table>
<p>Score average = <?= number_format($sum / count($students), 2) ?></p>
