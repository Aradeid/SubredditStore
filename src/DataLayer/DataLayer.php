<?php
    namespace DataLayer;

    interface DataLayer {
        public function getCategories();
        public function getSubredditsForCategory($categoryId);
        public function getSubredditsForSearchCriteria($title);
        public function createOrder($userId, $subredditIds, $nameOnCard, $cardNumber);
        public function getUser($id);
        public function getUserForUserName($userName);
        public function getUserForUserNameAndPassword($userName, $password);
    }