<?php
    namespace BusinessLogic;

    final class ShoppingCart {
        private $session;

        public function __construct(\BusinessLogic\Session $session){
            $this->session = $session;
        }

        const SESSION_CART = 'cart';

        private function getCart() {
            return $this->session->getValue(self::SESSION_CART, array());
        }

        private function storeCart($cart) {
            $this->session->storeValue(self::SESSION_CART, $cart);
        }

        public function add($subredditId) { // TODO include number of items
            $c = $this->getCart();
            $c[$subredditId] = $subredditId; 
            $this->storeCart($c);
        }

        public function remove($subredditId) {
            $c = $this->getCart();
            unset($c[$subredditId]);
            $this->storeCart($c);
        }

        public function contains($subredditId) {
            return array_key_exists($subredditId, $this->getCart());
        }

        public function clear() {
            $this->storeCart(array());
        }

        public function size() {
            return sizeof($this->getCart());
        }

        public function getAll() {
            return $this->getCart();
        }
    }