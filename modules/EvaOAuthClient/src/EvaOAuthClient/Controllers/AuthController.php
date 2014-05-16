<?php

namespace Eva\EvaOAuthClient\Controllers;

use Eva\EvaOAuthClient\Models;
use Eva\EvaUser\Models as UserModels;
use EvaOAuth\Service as OAuthService;

class AuthController extends ControllerBase
{

    /**
     * Index action
     */
    public function requestAction()
    {
        $service = $this->dispatcher->getParam('service');
        $oauthStr = $this->dispatcher->getParam('auth');
        $oauthStr = $oauthStr === 'oauth1' ? 'oauth1' : 'oauth2';
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');
        $callback = $url->get("/auth/access/$service/$oauthStr");

        $oauth = new OAuthService();
        $oauth->setOptions(array(
            'callbackUrl' => $callback ,
            'consumerKey' => $config->oauth->$oauthStr->$service->consumer_key,
            'consumerSecret' => $config->oauth->$oauthStr->$service->consumer_secret,
        ));
        $oauth->initAdapter($service, $oauthStr);
        OAuthService::setHttpClientOptions(array(
            'timeout' => 2
        ));

        $session = $this->getDI()->get('session');
        $session->remove('request-token');

        $requestToken = $oauth->getAdapter()->getRequestToken();

        $session->set('request-token', $requestToken);
        $requestTokenUrl = $oauth->getAdapter()->getRequestTokenUrl();
        $this->view->disable();
        $this->response->redirect($requestTokenUrl, true);
    }

    public function accessAction()
    {
        $service = $this->dispatcher->getParam('service');
        $oauthStr = $this->dispatcher->getParam('auth');
        $oauthStr = $oauthStr === 'oauth1' ? 'oauth1' : 'oauth2';
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');
        $callback = $url->get("/auth/access/$service/$oauthStr");

        $oauth = new OAuthService();
        $oauth->setOptions(array(
            'callbackUrl' => $callback,
            'consumerKey' => $config->oauth->$oauthStr->$service->consumer_key,
            'consumerSecret' => $config->oauth->$oauthStr->$service->consumer_secret,
        ));
        $oauth->initAdapter($service, $oauthStr);
        OAuthService::setHttpClientOptions(array(
            'timeout' => 2
        ));
        $session = $this->getDI()->get('session');
        $requestToken = $session->get('request-token');

        if (!$requestToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }

        try {
            $accessToken = $oauth->getAdapter()->getAccessToken($_GET, $requestToken);
            $accessTokenArray = $oauth->getAdapter()->accessTokenToArray($accessToken);
            $session->set('access-token', $accessTokenArray);
            $session->remove('request-token');
        } catch (\Exception $e) {
            $this->flashSession->error('ERR_OAUTH_AUTHORIZATION_FAILED');
            $this->ignoreException($e);

            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }

        $user = new Models\Login();
        try {
            if ($user->loginWithAccessToken($accessTokenArray)) {
                return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
            } else {
                return $this->response->redirect('/auth/register');
            }
        } catch (\Exception $e) {
            $this->displayException($e, $user->getMessages());

            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }

    }

    public function registerAction()
    {
        $session = $this->getDI()->get('session');
        $accessToken = $session->get('access-token');
        if (!$accessToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }
        $this->view->token = $accessToken;
        $this->view->suggestUsername = $this->getSuggestUsername($accessToken);
        $email = isset($accessToken['remoteEmail']) ? $accessToken['remoteEmail'] : '';
        $this->view->suggestEmail = $email;

        if ($email) {
            $userManager = new UserModels\User();
            $userManager->assign(array(
                'email' => $email,
            ));
            if ($userManager->isExist()) {
                $user = new Models\Login();
                $user->assign(array(
                    'email' => $email,
                ));
                $user->connectWithExistEmail($accessToken);
                $this->flashSession->success('SUCCESS_OAUTH_AUTO_CONNECT_EXIST_EMAIL');

                return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
            }
        }

        if (!$this->request->isPost()) {
            return;
        }

        $user = new Models\Login();
        $user->assign(array(
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
        ));

        $this->view->suggestEmail = isset($accessToken['remoteEmail']) ? $accessToken['remoteEmail'] : '';
        try {
            $user->register();
            $session->remove('access-token');
            $this->flashSession->success('SUCCESS_OAUTH_USER_REGISTERED');

            return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
        } catch (\Exception $e) {
            $this->displayException($e, $user->getMessages());

            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }
    }

    public function getSuggestUsername($accessToken)
    {
        $suggestUsername = '';
        if (isset($accessToken['remoteUserName']) && $accessToken['remoteUserName']) {
            $suggestUsername = $accessToken['remoteUserName'];
        } elseif (isset($accessToken['remoteNickName']) && $accessToken['remoteNickName']) {
            $suggestUsername = $accessToken['remoteNickName'];
        }
        $suggestUsername = str_replace(' ', '', $suggestUsername);
        if (!$suggestUsername || !preg_match('/^[0-9a-zA-Z]+$/', $suggestUsername)) {
            return '';
        }

        return $suggestUsername;
    }

    public function loginAction()
    {
        $this->view->setTemplateAfter('login');
        $this->view->pick('auth/register');

        $session = $this->getDI()->get('session');
        $accessToken = $session->get('access-token');
        if (!$accessToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }
        $this->view->token = $accessToken;

        if (!$this->request->isPost()) {
            return;
        }

        $user = new Models\Login();
        $identify = $this->request->getPost('identify');
        if (false === strpos($identify, '@')) {
            $user->assign(array(
                'username' => $identify,
                'password' => $this->request->getPost('password'),
            ));
        } else {
            $user->assign(array(
                'email' => $identify,
                'password' => $this->request->getPost('password'),
            ));
        }
        $session = $this->getDI()->get('session');
        try {
            $user->connectWithPassword($accessToken);
            $this->flashSession->success('SUCCESS_OAUTH_USER_CONNECTED');
            $session->remove('access-token');

            return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
        } catch (\Exception $e) {
            $this->displayException($e, $user->getMessages());

            return $this->response->redirect($this->getDI()->get('config')->oauth->loginFailedRedirectUri);
        }
    }

}
