<?php
class Movie {
    private $id;
    private $title;
    private $price;
    private $totalSeats;
    private $availableSeats;

    public function __construct($id, $title, $price, $totalSeats) {
        $this->setId($id);
        $this->setTitle($title);
        $this->setPrice($price);
        $this->setTotalSeats($totalSeats);
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getTotalSeats() {
        return $this->totalSeats;
    }

    public function getAvailableSeats() {
        return $this->availableSeats;
    }

    public function setId($id) {
        if (!self::isIdValid($id)) {
            throw new InvalidArgumentException('Movie ID must be a positive integer or a non-empty string.');
        }

        $this->id = is_string($id) ? trim($id) : $id;
    }

    public function setTitle($title) {
        if (!self::isTitleValid($title)) {
            throw new InvalidArgumentException('Movie title must be a non-empty string.');
        }

        $this->title = trim($title);
    }

    public function setPrice($price) {
        if (!self::isPriceValid($price)) {
            throw new InvalidArgumentException('Ticket price must be greater than 0.');
        }

        $this->price = $price;
    }

    public function setTotalSeats($totalSeats) {
        if (!self::isSeatCountValid($totalSeats)) {
            throw new InvalidArgumentException('Total seats must be a positive integer.');
        }

        $soldSeats = isset($this->availableSeats) ? $this->getSoldSeats() : 0;
        if ($totalSeats < $soldSeats) {
            throw new InvalidArgumentException('Total seats cannot be less than sold seats.');
        }

        $this->totalSeats = $totalSeats;
        $this->availableSeats = $totalSeats - $soldSeats;
    }

    public function setAvailableSeats($availableSeats) {
        if (!is_int($availableSeats) || $availableSeats < 0 || $availableSeats > $this->totalSeats) {
            throw new InvalidArgumentException('Available seats must be between 0 and total seats.');
        }

        $this->availableSeats = $availableSeats;
    }

    public function bookTicket($quantity) {
        if (!self::isSeatCountValid($quantity)) {
            throw new InvalidArgumentException('Ticket quantity must be a positive integer.');
        }
        if ($quantity > $this->getAvailableSeats()) {
            throw new RuntimeException('Not enough available seats.');
        }

        $this->setAvailableSeats($this->getAvailableSeats() - $quantity);
    }

    public function cancelTicket($quantity) {
        if (!self::isSeatCountValid($quantity)) {
            throw new InvalidArgumentException('Ticket quantity must be a positive integer.');
        }
        if ($quantity > $this->getSoldSeats()) {
            throw new RuntimeException('Cannot cancel more tickets than sold.');
        }

        $this->setAvailableSeats($this->getAvailableSeats() + $quantity);
    }

    public function getSoldSeats() {
        return $this->getTotalSeats() - $this->getAvailableSeats();
    }

    public function getRevenue() {
        return $this->getSoldSeats() * $this->getPrice();
    }

    public function displayInfo() {
        echo 'ID: ' . $this->getId() . '<br>';
        echo 'Title: ' . $this->getTitle() . '<br>';
        echo 'Ticket price: ' . $this->getPrice() . '<br>';
        echo 'Total seats: ' . $this->getTotalSeats() . '<br>';
        echo 'Available seats: ' . $this->getAvailableSeats() . '<br>';
        echo 'Sold seats: ' . $this->getSoldSeats() . '<br>';
        echo 'Revenue: ' . $this->getRevenue() . '<br>';
    }

    public static function isIdValid($id) {
        return (is_int($id) && $id > 0) || (is_string($id) && trim($id) !== '');
    }

    public static function isTitleValid($title) {
        return is_string($title) && trim($title) !== '';
    }

    public static function isPriceValid($price) {
        return is_numeric($price) && $price > 0;
    }

    public static function isSeatCountValid($quantity) {
        return is_int($quantity) && $quantity > 0;
    }
}

function findMovieById($movies, $id) {
    foreach ($movies as $movie) {
        if ($movie instanceof Movie && $movie->getId() === $id) {
            return $movie;
        }
    }

    return null;
}

function getTotalRevenue($movies) {
    $totalRevenue = 0;
    foreach ($movies as $movie) {
        if ($movie instanceof Movie) {
            $totalRevenue += $movie->getRevenue();
        }
    }

    return $totalRevenue;
}

function getBestSellingMovie($movies) {
    $bestMovie = null;

    foreach ($movies as $movie) {
        if ($movie instanceof Movie && ($bestMovie === null || $movie->getSoldSeats() > $bestMovie->getSoldSeats())) {
            $bestMovie = $movie;
        }
    }

    return $bestMovie;
}
