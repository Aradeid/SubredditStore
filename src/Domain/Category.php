<?php
    namespace Domain;

    class Category extends Entity {
        
        private $name;
        private $titular;

        public function getName() {
            return $this->name;
        }

        public function getTitular() {
            return $this->titular;
        }

        public function __construct($id, $name, $titular) {
            parent::__construct($id);
            $this->name = $name;
            $this->titular = $titular;
        }
        
    }