<?php 
    $items = [
        ['name' => 'Keyboard', 'price' => 50],
        ['name' => 'Mouse', 'price' => 25],
        ['name' => 'Headset', 'price' => 75],
        ['name' => 'Monitor', 'price' => 200],
        ['name' => 'Webcam', 'price' => 120],
    ];

    function calculateTotal($items) {
        if (!is_array($items)) {
            throw new InvalidArgumentException("Items must be an array");
        }

        $total = 0;
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['price'])) {
                throw new InvalidArgumentException("Item must have a price");
            }

            if (!is_numeric($item['price']) || $item['price'] < 0) {
                throw new InvalidArgumentException("Price must be a number greater than or equal to 0");
            }

            $total += $item['price'];
        }

        return $total;
    }

    function findMaxPrice($items) {
        if (!is_array($items)) {
            throw new InvalidArgumentException("Items must be an array");
        }

        $maxItem = null;
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['price'])) {
                throw new InvalidArgumentException("Item must have a price");
            }

            if (!is_numeric($item['price']) || $item['price'] < 0) {
                throw new InvalidArgumentException("Price must be a number greater than or equal to 0");
            }

            if ($maxItem === null || $item['price'] > $maxItem['price']) {
                $maxItem = $item;
            }
        }

        return $maxItem;
    }

try {
    $total = calculateTotal($items);
    $maxItem = findMaxPrice($items);

    if ($maxItem === null) {
        echo "No items";
    } else {
        echo "Total: " . $total . "<br>";
        echo "Max price: " . $maxItem['name'] . " - " . $maxItem['price'];
    }
} catch (InvalidArgumentException $e) {
    echo "error: " . $e->getMessage();
}
