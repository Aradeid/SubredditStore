<?php
    namespace Controllers;

    class Subreddits extends \Framework\Controller {

        const PARAM_SUBREDDIT_ID = 'sid';
        const PARAM_CATEGORY_ID = 'cid';
        const PARAM_URL = 'url';
        const PARAM_DESCRIPTION = 'desc';
        const PARAM_MULTIPLIER = 'mult';
        const PARAM_RATING = 'rval';
        const PARAM_COMMENT = 'cval';
        
        private $dataLayer;
        private $shoppingCart;
        private $authenticationManager;

        public function __construct(\DataLayer\DataLayer $dataLayer, \BusinessLogic\ShoppingCart $shoppingCart, \BusinessLogic\AuthenticationManager $authenticationManager) {
            $this->dataLayer = $dataLayer;
            $this->shoppingCart = $shoppingCart;
            $this->authenticationManager = $authenticationManager;
        }
        
        public function GET_Index() {
            return $this->renderView('subredditList', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'categories' => $this->dataLayer->getCategories(),
                'selectedCategoryId' => $this->getParam(self::PARAM_CATEGORY_ID),
                'subreddits' => $this->hasParam(self::PARAM_CATEGORY_ID) ?
                    $this->dataLayer->getSubredditsForCategory($this->getParam(self::PARAM_CATEGORY_ID)) : 
                    $this->dataLayer->getSubreddits(), 
                    'cart' => $this->shoppingCart->getAll(),
                    'context' => $this->buildActionLink('Index', 'Subreddits', array(self::PARAM_CATEGORY_ID => $this->getParam(self::PARAM_CATEGORY_ID)))
            ));
        }

        public function GET_Search() {
            return $this->renderView('subredditSearch', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'url' => $this->getParam(self::PARAM_URL),
                'subreddits' => $this->hasParam(self::PARAM_URL) ? 
                    $this->dataLayer->getSubredditsForSearchCriteria($this->getParam(self::PARAM_URL)) : 
                    null,
                'cart' => $this->shoppingCart->getAll(),
                'context' => $this->buildActionLink('Search', 'Subreddits', array(self::PARAM_URL => $this->getParam(self::PARAM_URL)))
            ));
        }

        public function GET_View() {
            return $this->renderView('subredditView', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'subreddit' => $this->hasParam(self::PARAM_SUBREDDIT_ID) ?
                    $this->dataLayer->getSubredditForId((int)$this->getParam(self::PARAM_SUBREDDIT_ID)) :
                    null,
                'rating' => $this->hasParam(self::PARAM_SUBREDDIT_ID) ?
                    $this->dataLayer->getRatingForSubreddit((int)$this->getParam(self::PARAM_SUBREDDIT_ID)) :
                    null,
                'ownRating' => $this->hasParam(self::PARAM_SUBREDDIT_ID) ?
                    ($this->authenticationManager->isAuthenticated() ?
                        $this->dataLayer->getRatingForSubredditAndUser((int)$this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId()) : null)
                    : null,
                'comments' => $this->hasParam(self::PARAM_SUBREDDIT_ID) ?
                    $this->dataLayer->getCommentsForSubreddit((int)$this->getParam(self::PARAM_SUBREDDIT_ID)) :
                    null,
                'cart' => $this->shoppingCart->getAll(),
                'context' => $this->buildActionLink('Index', 'Subreddits', array(self::PARAM_SUBREDDIT_ID => $this->getParam(self::PARAM_CATEGORY_ID)))
            ));
        }

        public function POST_Rate() {
            if (!$this->authenticationManager->isAuthenticated()) {
                return $this->renderView('userLogin', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => null,
                    'errors' => array('You must be authenticated in order to rate a subreddit.')
                ));
            }
            if (!$this->dataLayer->getSubredditForId((int)$this->getParam(self::PARAM_SUBREDDIT_ID))) {
                return $this->renderView('home', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'errors' => array('Requested subreddit doesn\'t exist.')
                ));
            }
            if (((int)$this->getParam(self::PARAM_RATING) > 5) || ((int)$this->getParam(self::PARAM_RATING) < 1)) {
                return $this->renderView('home', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'errors' => array('Rating value out of range.')
                ));
            }
            if ($this->dataLayer->getRatingForSubredditAndUser((int)$this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId()) !== 0) {
                $this->dataLayer->updateRatingForSubredditAndUser((int)$this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId(), (int)$this->getParam(self::PARAM_RATING));
            } else {
                $this->dataLayer->postRatingForSubredditAndUser((int)$this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId(), (int)$this->getParam(self::PARAM_RATING));
            }
            return $this->redirect('View', 'Subreddits', array(
                'sid' => $this->getParam(self::PARAM_SUBREDDIT_ID)
            ));
        }

        public function POST_Comment() {
            if (!$this->authenticationManager->isAuthenticated()) {
                return $this->renderView('userLogin', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => null,
                    'errors' => array('You must be authenticated in order to rate a subreddit.')
                ));
            }
            if (!$this->dataLayer->getSubredditForId((int)$this->getParam(self::PARAM_SUBREDDIT_ID))) {
                return $this->renderView('home', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'errors' => array('Requested subreddit doesn\'t exist.')
                ));
            }
            if ($this->dataLayer->getCommentForSubredditAndUser($this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId()) !== null) {
                $this->dataLayer->updateCommentForSubredditAndUser($this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId(), $this->getParam(self::PARAM_COMMENT));
            } else {
                $this->dataLayer->postCommentForSubredditAndUser($this->getParam(self::PARAM_SUBREDDIT_ID), $this->authenticationManager->getAuthenticatedUser()->getId(), $this->getParam(self::PARAM_COMMENT));
            }
            return $this->redirect('View', 'Subreddits', array(
                'sid' => $this->getParam(self::PARAM_SUBREDDIT_ID)
            ));
        }

        public function GET_Register() {
            if (!$this->authenticationManager->isAuthenticated()) {
                return $this->renderView('userLogin', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => null,
                    'errors' => array('You must be authenticated in order to add a subreddit.')
                ));
            }
            $subreddit = null;
            if ($this->hasParam(self::PARAM_SUBREDDIT_ID)) {
                $subreddit = $this->dataLayer->getSubredditForId($this->getParam(self::PARAM_SUBREDDIT_ID));
            }
            return $this->renderView('subredditRegister', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'categories' => $this->dataLayer->getCategories(),
                'subreddit' => $subreddit
            ));
        }

        public function POST_Register() {
            $url = $this->getParam(self::PARAM_URL);
            $url = ((strpos($url, "r/") !== false) && (strpos($url, "r/") < strlen($url) - 2))
                ? substr($url, strpos($url, "r/") + 2) 
                : $url;
            $url = str_replace("/", "", $url);
            if (!$this->authenticationManager->isAuthenticated()) {
                return $this->renderView('userLogin', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => null,
                    'errors' => array('You must be authenticated in order to add a subreddit.')
                ));
            }
            if ((int)$this->dataLayer->getSubscribersForUrl($url) < 1000) {
                return $this->renderView('subredditRegister', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'categories' => $this->dataLayer->getCategories(),
                    'errors' => array('There seems to be a problem while accessing the subreddit. Please note that all subreddits with less than 1000 subscribers are ignored.')
                ));
            }
            $sub = $this->dataLayer->getSubredditForUrl($url);
            if ($sub != null) {
                if ($sub->getSubmitter()->getId() == $this->authenticationManager->getAuthenticatedUser()->getId()) {
                    $sub = $this->dataLayer->updateSubreddit(
                        $url,
                        $this->authenticationManager->getAuthenticatedUser()->getId(),
                        $this->getParam(self::PARAM_CATEGORY_ID),
                        $this->getParam(self::PARAM_MULTIPLIER),
                        $this->getParam(self::PARAM_DESCRIPTION)
                        );
                } else {
                    return $this->renderView('subredditRegister', array(
                        'user' => $this->authenticationManager->getAuthenticatedUser(),
                        'categories' => $this->dataLayer->getCategories(),
                        'errors' => array('Subreddit already exists.')
                        ));
                }
            } else {
                $sub = $this->dataLayer->postSubreddit(
                    $url,
                    $this->authenticationManager->getAuthenticatedUser()->getId(),
                    $this->getParam(self::PARAM_CATEGORY_ID),
                    $this->getParam(self::PARAM_MULTIPLIER),
                    nl2br(htmlentities($this->getParam(self::PARAM_DESCRIPTION)))
                    );
            }
            return $this->redirect('View', 'Subreddits', array(
                'sid' => $sub->getId()
            ));
        }
    }