<?php
    namespace Controllers;

    class Categories extends \Framework\Controller {
        
        private $dataLayer;
        private $shoppingCart;
        private $authenticationManager;

        public function __construct(\DataLayer\DataLayer $dataLayer, \BusinessLogic\ShoppingCart $shoppingCart, \BusinessLogic\AuthenticationManager $authenticationManager) {
            $this->dataLayer = $dataLayer;
            $this->shoppingCart = $shoppingCart;
            $this->authenticationManager = $authenticationManager;
        }
        
        public function GET_Index() {
            return $this->renderView('categoryList', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'categories' => $this->dataLayer->getCategories()
            ));
        }

        public function GET_Search() {
            return $this->renderView('subredditSearch', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'url' => $this->getParam(self::PARAM_URL),
                'subreddits' => $this->hasParam(self::PARAM_URL) ? 
                    $this->dataLayer->getSubredditsForSearchCriteria($this->getParam(self::PARAM_URL)) : 
                    null, 'cart' => $this->shoppingCart->getAll(),
                    'context' => $this->buildActionLink('Search', 'Subreddits', array(self::PARAM_URL => $this->getParam(self::PARAM_URL)))
            ));
        }
    }