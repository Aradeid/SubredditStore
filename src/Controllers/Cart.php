<?php
    namespace Controllers;

    class Cart extends \Framework\Controller {

        const PARAM_SUBREDDIT_ID = 'sid';
        const PARAM_CONTEXT = 'ctx';

        private $shoppingCart;

        public function __construct(\BusinessLogic\ShoppingCart $shoppingCart) {
            $this->shoppingCart = $shoppingCart;
        }

        public function POST_Add() {
            $this->shoppingCart->add($this->getParam(self::PARAM_SUBREDDIT_ID));
            return $this->redirectToUrl($this->getParam(self::PARAM_CONTEXT));
        }

        public function POST_Remove() {
            $this->shoppingCart->remove($this->getParam(self::PARAM_SUBREDDIT_ID));
            return $this->redirectToUrl($this->getParam(self::PARAM_CONTEXT));
        }

    }