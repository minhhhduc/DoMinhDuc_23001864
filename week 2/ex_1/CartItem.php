<?php
class CartItem {
    private $name;
    private $price;
    private $quantity;

    public function __construct($name, $price, $quantity) {
        $this->setName($name);
        $this->setPrice($price);
        $this->setQuantity($quantity);
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setName($name) {
        if (!self::isNameValid($name)) {
            throw new InvalidArgumentException('Product name must be a non-empty string.');
        }

        $this->name = trim($name);
    }

    public function setPrice($price) {
        if (!self::isPriceValid($price)) {
            throw new InvalidArgumentException('Product price must be a number greater than 0.');
        }

        $this->price = $price;
    }

    public function setQuantity($quantity) {
        if (!self::isQuantityValid($quantity)) {
            throw new InvalidArgumentException('Quantity must be a positive integer.');
        }

        $this->quantity = $quantity;
    }

    public function getTotal() {
        return $this->price * $this->quantity;
    } 

    public function __toString() {
        return $this->name . ' - ' . $this->price . ' x ' . $this->quantity;
    }

    public static function isNameValid($name) {
        return is_string($name) && trim($name) !== '';
    }

    public static function isPriceValid($price) {
        return is_numeric($price) && $price > 0;
    }

    public static function isQuantityValid($quantity) {
        return filter_var($quantity, FILTER_VALIDATE_INT) !== false && $quantity > 0;
    }
}    
?>