<?php
    namespace Domain;

    class Subreddit extends Entity {
        
        private $categoryId;
        private $url;
        private $submitter;
        private $multiplier;
        private $price;

        public function getCategoryId() {
            return $this->categoryId;
        }

        public function getUrl() {
            return $this->url;
        }

        public function getSubmitter() {
            return $this->submitter;
        }

        public function getDescription() {
            return $this->description;
        }

        public function getMultiplier() {
            return $this->multiplier;
        }

        public function getPrice() {
            return $this->price;
        }

        public function __construct($id, $categoryId, $url, $description, $submitter, $multiplier, $subCount) {
            parent::__construct($id);
            $this->categoryId = $categoryId;
            $this->url = $url;
            $this->description = $description;
            $this->submitter = $submitter;
            $this->multiplier = $multiplier;
            $this->price = ceil($multiplier * $subCount / 100000) / 100;
        }
    }