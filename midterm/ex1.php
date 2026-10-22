<?php
    $name = "An";
    $score = 7;

    function getRank($score) {
        if (!is_numeric($score)) {
            throw new InvalidArgumentException("Score must be numeric");
        }

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

try {
    if (!is_numeric($score)) {
        throw new InvalidArgumentException("Score must be numeric");
    }
    if ($score > 10 || $score < 0) {
        throw new InvalidArgumentException("Score must be between 0 and 10");
    }
    echo "Name: ".$name."<br>Score: ".$score."<br>Rank: ".getRank($score);
} catch (InvalidArgumentException $e) {
    echo "error: " . $e->getMessage();
}
    
