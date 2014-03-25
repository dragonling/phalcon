<?php

namespace Eva\EvaOAuthClient\Controllers;


use Eva\EvaOAuthClient\Models;
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
            'timeout' => 1
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
            'timeout' => 1
        ));
        $session = $this->getDI()->get('session');
        $requestToken = $session->get('request-token');

        if(!$requestToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }

        try {
            $accessToken = $oauth->getAdapter()->getAccessToken($_GET, $requestToken);
            $accessTokenArray = $oauth->getAdapter()->accessTokenToArray($accessToken);
            $session->set('access-token', $accessTokenArray);
            $session->remove('request-token');
        } catch(\Exception $e) {
            $this->flashSession->error('ERR_OAUTH_AUTHORIZATION_FAILED');
            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }

        $user = new Models\Login();
        try {
            if($user->loginWithAccessToken($accessTokenArray)) {
                return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
            } else {
                return $this->response->redirect('/auth/register');
            }
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }

    }

    public function registerAction()
    {
        $session = $this->getDI()->get('session');
        $accessToken = $session->get('access-token');
        if(!$accessToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }
        $this->view->token = $accessToken;

        if (!$this->request->isPost()) {
            return;
        }

        $user = new Models\Login();
        $user->assign(array(
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
        ));
        $session = $this->getDI()->get('session');
        try {
            $user->register();
            $this->flashSession->success('Login Success');
            $session->remove('access-token');
            return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->oauth->registerFailedRedirectUri);
        }
    }


    public function loginAction()
    {
        $this->view->setTemplateAfter('login');
        $this->view->pick('auth/register');

        $session = $this->getDI()->get('session');
        $accessToken = $session->get('access-token');
        if(!$accessToken) {
            return $this->response->redirect($this->getDI()->get('config')->oauth->authFailedRedirectUri);
        }
        $this->view->token = $accessToken;

        if (!$this->request->isPost()) {
            return;
        }

        $user = new Models\Login();
        $identify = $this->request->getPost('identify');
        if(false === strpos($identify, '@')) {
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
            $user->connect($accessToken);
            $this->flashSession->success('Connect Success');
            //$session->remove('access-token');
            //return $this->response->redirect($this->getDI()->get('config')->oauth->loginSuccessRedirectUri);
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            //return $this->response->redirect($this->getDI()->get('config')->oauth->loginFailedRedirectUri);
        }
    }

}
