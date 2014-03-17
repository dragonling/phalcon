<?php

namespace Eva\EvaOAuthServer\Controllers;

use League\OAuth2\Server as OAuthServer;

class AuthController extends ControllerBase
{
    protected $authserver;

    /**
    * Index action
    */
    public function authorizeAction()
    {
        // Initiate the request handler which deals with $_GET, $_POST, etc
        $request = new OAuthServer\Util\Request();

        // Initiate a new database connection
        $db = new OAuthServer\Storage\PDO\Db('mysql://root:582tsost@localhost/scrapy');

        // Create the auth server, the three parameters passed are references
        //  to the storage models
        $this->authserver = new OAuthServer\Authorization(
            new ClientModel,
            new SessionModel,
            new ScopeModel
        );

        // Enable the authorization code grant type
        $this->authserver->addGrantType(new OAuthServer\Grant\AuthCode());

    }

    public function tokenAction()
    {
    }

}
