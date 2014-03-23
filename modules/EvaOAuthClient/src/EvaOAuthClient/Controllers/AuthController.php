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
        $oauth->initAdapter(ucfirst($service), $oauthStr);

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
        $oauth->initAdapter(ucfirst($service), $oauthStr);
        $session = $this->getDI()->get('session');
        $requestToken = $session->get('request-token');

        if(!$requestToken) {
            return $this->response->redirect($url->get("/auth/request/$service/$oauthStr"), true);
        }
        $accessToken = $oauth->getAdapter()->getAccessToken($_GET, $requestToken);
        $accessTokenArray = $oauth->getAdapter()->accessTokenToArray($accessToken);
        $session->set('access-token', $accessTokenArray);
        $session->remove('request-token');

        $this->response->redirect('/auth/register');
    }

    public function registerAction()
    {
        $session = $this->getDI()->get('session');
        $accessToken = $session->get('access-token');
        if(!$accessToken) {
            return $this->response->redirect($this->getDI()->get('config')->user->registerFailedRedirectUri);
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
        try {
            $user->register();
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->user->registerFailedRedirectUri);
        }
    }

}
