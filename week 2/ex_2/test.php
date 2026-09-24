<?php
include __DIR__ . '/Movie.php';

function assertTest($condition, $message) {
    if (!$condition) {
        throw new RuntimeException('Test failed: ' . $message);
    }

    echo 'PASS: ' . $message . '<br>';
}

function assertException($callback, $expectedMessage, $message) {
    try {
        $callback();
        assertTest(false, $message);
    } catch (Throwable $error) {
        assertTest($error->getMessage() === $expectedMessage, $message);
    }
}

try {
    $avengers = new Movie(1, 'Avengers', 100000, 100);
    $avatar = new Movie(2, 'Avatar', 120000, 80);
    $batman = new Movie(3, 'Batman', 90000, 120);
    $movies = [$avengers, $avatar, $batman];

    $avengers->bookTicket(10);
    $avatar->bookTicket(15);
    $avengers->cancelTicket(3);

    assertTest($avengers->getSoldSeats() === 7, 'Avengers booking and cancellation are correct');
    assertTest($avatar->getSoldSeats() === 15, 'Avatar booking is correct');
    assertTest(getTotalRevenue($movies) === 2500000, 'Total revenue is correct');
    assertTest(getBestSellingMovie($movies) === $avatar, 'Avatar is the best-selling movie');
    assertTest(findMovieById($movies, 1) === $avengers, 'Movie is found by ID');
    assertTest(findMovieById($movies, 99) === null, 'Missing movie returns null');

    echo '<hr><strong>Movie information</strong><br>';
    foreach ($movies as $movie) {
        $movie->displayInfo();
        echo '<br>';
    }

    assertException(function() use ($avengers) {
        $avengers->bookTicket(0);
    }, 'Ticket quantity must be a positive integer.', 'Booking 0 tickets is rejected');

    assertException(function() use ($avengers) {
        $avengers->bookTicket(94);
    }, 'Not enough available seats.', 'Booking more than available seats is rejected');

    assertException(function() use ($avengers) {
        $avengers->cancelTicket(0);
    }, 'Ticket quantity must be a positive integer.', 'Cancelling 0 tickets is rejected');

    assertException(function() use ($avengers) {
        $avengers->cancelTicket(8);
    }, 'Cannot cancel more tickets than sold.', 'Cancelling more than sold tickets is rejected');

    assertTest(findMovieById([], 1) === null, 'findMovieById handles an empty list');
    assertTest(getTotalRevenue([]) === 0, 'getTotalRevenue handles an empty list');
    assertTest(getBestSellingMovie([]) === null, 'getBestSellingMovie handles an empty list');
} catch (Throwable $error) {
    echo $error->getMessage() . '<br>';
    exit(1);
}
