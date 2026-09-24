<?php
include __DIR__ . '/CartItem.php';

class ShoppingCart {
    private $items = [];

    public function __construct($items = []) {
        if (!self::isItemsValid($items)) {
            throw new InvalidArgumentException('Items must be an array.');
        }
        
        foreach ($items as $item) {
            $this->addItems($item);
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function addItems($item) {
        if (!self::isItemValid($item)) {
            throw new InvalidArgumentException('Item must be a CartItem object.');
        }

        $this->items[] = $item;
    }

    public function getItem($index) {
        if (!$this->isIndexValid($index)) {
            throw new OutOfBoundsException('Invalid item index.');
        }

        return $this->items[$index];
    }

    public function setItem($index, $newItem) {
        if (!$this->isIndexValid($index)) {
            throw new OutOfBoundsException('Invalid item index.');
        }
        if (!self::isItemValid($newItem)) {
            throw new InvalidArgumentException('New item must be a CartItem object.');
        }

        $this->items[$index] = $newItem;
    }

    public function removeItem($name) {
        if (!CartItem::isNameValid($name)) {
            throw new InvalidArgumentException('Product name must be a non-empty string.');
        }

        $name = trim($name);

        foreach ($this->items as $index => $item) {
            if ($item->getName() === $name) {
                unset($this->items[$index]);
                $this->items = array_values($this->items);
                return;
            }
        }

        throw new RuntimeException('Product not found.');
    }

    public function calculateTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function getLength() {
        return count($this->items);
    }

    public function __toString() {
        return implode(PHP_EOL, $this->items);
    }

    public function isEmpty() {
        return empty($this->items);
    }

    private static function isItemsValid($items) {
        return is_array($items);
    }

    private static function isItemValid($item) {
        return $item instanceof CartItem;
    }

    private function isIndexValid($index) {
        return is_int($index) && $index >= 0 && $index < $this->getLength();
    }

}
?>