<?php

namespace Eva\EvaOAuthClient\Controllers;


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
        $config = $this->getDi()->get('config');
        $url = $this->getDi()->get('url');
        $callback = $url->get("/auth/access/$service/$oauthStr");

        $oauth = new OAuthService();
        $oauth->setOptions(array(
            'callbackUrl' => $callback ,
            'consumerKey' => $config->oauth->$oauthStr->$service->consumer_key,
            'consumerSecret' => $config->oauth->$oauthStr->$service->consumer_secret,
        ));
        $oauth->initAdapter(ucfirst($service), $oauthStr);

        $requestToken = $oauth->getAdapter()->getRequestToken();
        $oauth->getStorage()->saveRequestToken($requestToken);
        $requestTokenUrl = $oauth->getAdapter()->getRequestTokenUrl();
        
        $this->view->disable();
        $this->response->redirect($requestTokenUrl, true);
    }

    public function accessAction()
    {
        $service = $this->dispatcher->getParam('service');
        $oauthStr = $this->dispatcher->getParam('auth');
        $oauthStr = $oauthStr === 'oauth1' ? 'oauth1' : 'oauth2';
        $config = $this->getDi()->get('config');
        $url = $this->getDi()->get('url');
        $callback = $url->get("/auth/access/$service/$oauthStr");

        $oauth = new OAuthService();
        $oauth->setOptions(array(
            'callbackUrl' => $callback,
            'consumerKey' => $config->oauth->$oauthStr->$service->consumer_key,
            'consumerSecret' => $config->oauth->$oauthStr->$service->consumer_secret,
        ));
        $oauth->initAdapter(ucfirst($service), $oauthStr);
        $requestToken = $oauth->getStorage()->getRequestToken();
        $accessToken = $oauth->getAdapter()->getAccessToken($_GET, $requestToken);
        $accessTokenArray = $oauth->getAdapter()->accessTokenToArray($accessToken);
        $oauth->getStorage()->saveAccessToken($accessTokenArray);
        $oauth->getStorage()->clearRequestToken();
        p($accessTokenArray);
    }

}
