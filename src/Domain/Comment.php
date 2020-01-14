<?php
    namespace Domain;

    class Comment extends Entity {
        
        private $content;
        private $creator;

        public function getContent() {
            return $this->content;
        }

        public function getCreator() {
            return $this->creator;
        }

        public function __construct($id, $content, $creator) {
            parent::__construct($id);
            $this->content = $content;
            $this->creator = $creator;
        }
    }