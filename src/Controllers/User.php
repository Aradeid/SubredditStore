<?php
    namespace Controllers;

    class User extends \Framework\Controller {
    
        private $authenticationManager;

        const PARAM_USER_NAME = 'un';
        const PARAM_PASSWORD = 'pwd';
        const PARAM_PASSWORD_CONFIRM = 'pwd-conf';

        public function __construct(\BusinessLogic\AuthenticationManager $authenticationManager) {
            $this->authenticationManager = $authenticationManager;
        }
    
        public function GET_LogIn() {
            if ($this->authenticationManager->isAuthenticated()) {
                return $this->redirect('Index', 'Home');
            }
            return $this->renderView('userLogin', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'userName' => $this->getParam(self::PARAM_USER_NAME)
            ));
        }

        public function POST_LogIn() {
            if (!$this->authenticationManager->authenticate(
                $this->getParam(self::PARAM_USER_NAME), 
                $this->getParam(self::PARAM_PASSWORD)
                )) {
                return $this->renderView('userLogin', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => $this->getParam(self::PARAM_USER_NAME),
                    'errors' => array('Invalid user name or password.')
                ));
            }
            return $this->redirect('Index', 'Home');
        }

        public function GET_Register() {
            if ($this->authenticationManager->isAuthenticated()) {
                return $this->redirect('Index', 'Home');
            }
            return $this->renderView('userRegister', array(
                'user' => $this->authenticationManager->getAuthenticatedUser(),
                'userName' => $this->getParam(self::PARAM_USER_NAME)
            ));
        }

        public function POST_Register() {
            if ($this->authenticationManager->confirmUser(
                $this->getParam(self::PARAM_USER_NAME)
                )) {
                return $this->renderView('userRegister', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => $this->getParam(self::PARAM_USER_NAME),
                    'errors' => array('This username already in use, please register under a different username.')
                ));
            }
            if (strlen($this->getParam(self::PARAM_PASSWORD)) < 8) {
                return $this->renderView('userRegister', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => $this->getParam(self::PARAM_USER_NAME),
                    'errors' => array('The password must be at least 8 characters long.')
                ));
            }
            if ($this->getParam(self::PARAM_PASSWORD) !== $this->getParam(self::PARAM_PASSWORD_CONFIRM)) {
                return $this->renderView('userRegister', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => $this->getParam(self::PARAM_USER_NAME),
                    'errors' => array('The \'Password\' and \'Confim Password\' fields must be identical to register.')
                ));
            }
            if (!$this->authenticationManager->register(
                $this->getParam(self::PARAM_USER_NAME), 
                $this->getParam(self::PARAM_PASSWORD)
                )) {
                return $this->renderView('userRegister', array(
                    'user' => $this->authenticationManager->getAuthenticatedUser(),
                    'userName' => $this->getParam(self::PARAM_USER_NAME),
                    'errors' => array('Invalid user name or password.')
                ));
            }
            return $this->redirect('Index', 'Home');
        }

        public function POST_LogOut() {
            $this->authenticationManager->signOut();
            return $this->redirect('Index', 'Home');
        }
    }
