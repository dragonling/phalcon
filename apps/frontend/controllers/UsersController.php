<?php

namespace Wscn\Frontend\Controllers;

use Wscn\Frontend\Models\Users;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UsersController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Wscn\Frontend\Models\Users", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "id";

        $users = Users::find($parameters);
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any users");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $users,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displayes the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {

            $user = Users::findFirstByid($id);
            if (!$user) {
                $this->flash->error("user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "users",
                    "action" => "index"
                ));
            }

            $this->view->id = $user->id;

            $this->tag->setDefault("id", $user->id);
            $this->tag->setDefault("username", $user->username);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("mobile", $user->mobile);
            $this->tag->setDefault("status", $user->status);
            $this->tag->setDefault("flag", $user->flag);
            $this->tag->setDefault("screenName", $user->screenName);
            $this->tag->setDefault("salt", $user->salt);
            $this->tag->setDefault("firstName", $user->firstName);
            $this->tag->setDefault("lastName", $user->lastName);
            $this->tag->setDefault("password", $user->password);
            $this->tag->setDefault("oldPassword", $user->oldPassword);
            $this->tag->setDefault("lastUpdateTime", $user->lastUpdateTime);
            $this->tag->setDefault("gender", $user->gender);
            $this->tag->setDefault("avatar_id", $user->avatar_id);
            $this->tag->setDefault("avatar", $user->avatar);
            $this->tag->setDefault("timezone", $user->timezone);
            $this->tag->setDefault("registerTime", $user->registerTime);
            $this->tag->setDefault("lastLoginTime", $user->lastLoginTime);
            $this->tag->setDefault("language", $user->language);
            $this->tag->setDefault("setting", $user->setting);
            $this->tag->setDefault("inviteUserId", $user->inviteUserId);
            $this->tag->setDefault("onlineStatus", $user->onlineStatus);
            $this->tag->setDefault("lastFreshTime", $user->lastFreshTime);
            $this->tag->setDefault("viewCount", $user->viewCount);
            $this->tag->setDefault("registerIp", $user->registerIp);
            $this->tag->setDefault("lastLoginIp", $user->lastLoginIp);
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user = new Users();

        $user->username = $this->request->getPost("username");
        $user->email = $this->request->getPost("email", "email");
        $user->mobile = $this->request->getPost("mobile");
        $user->status = $this->request->getPost("status");
        $user->flag = $this->request->getPost("flag");
        $user->screenName = $this->request->getPost("screenName");
        $user->salt = $this->request->getPost("salt");
        $user->firstName = $this->request->getPost("firstName");
        $user->lastName = $this->request->getPost("lastName");
        $user->password = $this->request->getPost("password");
        $user->oldPassword = $this->request->getPost("oldPassword");
        $user->lastUpdateTime = $this->request->getPost("lastUpdateTime");
        $user->gender = $this->request->getPost("gender");
        $user->avatar_id = $this->request->getPost("avatar_id");
        $user->avatar = $this->request->getPost("avatar");
        $user->timezone = $this->request->getPost("timezone");
        $user->registerTime = $this->request->getPost("registerTime");
        $user->lastLoginTime = $this->request->getPost("lastLoginTime");
        $user->language = $this->request->getPost("language");
        $user->setting = $this->request->getPost("setting");
        $user->inviteUserId = $this->request->getPost("inviteUserId");
        $user->onlineStatus = $this->request->getPost("onlineStatus");
        $user->lastFreshTime = $this->request->getPost("lastFreshTime");
        $user->viewCount = $this->request->getPost("viewCount");
        $user->registerIp = $this->request->getPost("registerIp");
        $user->lastLoginIp = $this->request->getPost("lastLoginIp");
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "new"
            ));
        }

        $this->flash->success("user was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user->username = $this->request->getPost("username");
        $user->email = $this->request->getPost("email", "email");
        $user->mobile = $this->request->getPost("mobile");
        $user->status = $this->request->getPost("status");
        $user->flag = $this->request->getPost("flag");
        $user->screenName = $this->request->getPost("screenName");
        $user->salt = $this->request->getPost("salt");
        $user->firstName = $this->request->getPost("firstName");
        $user->lastName = $this->request->getPost("lastName");
        $user->password = $this->request->getPost("password");
        $user->oldPassword = $this->request->getPost("oldPassword");
        $user->lastUpdateTime = $this->request->getPost("lastUpdateTime");
        $user->gender = $this->request->getPost("gender");
        $user->avatar_id = $this->request->getPost("avatar_id");
        $user->avatar = $this->request->getPost("avatar");
        $user->timezone = $this->request->getPost("timezone");
        $user->registerTime = $this->request->getPost("registerTime");
        $user->lastLoginTime = $this->request->getPost("lastLoginTime");
        $user->language = $this->request->getPost("language");
        $user->setting = $this->request->getPost("setting");
        $user->inviteUserId = $this->request->getPost("inviteUserId");
        $user->onlineStatus = $this->request->getPost("onlineStatus");
        $user->lastFreshTime = $this->request->getPost("lastFreshTime");
        $user->viewCount = $this->request->getPost("viewCount");
        $user->registerIp = $this->request->getPost("registerIp");
        $user->lastLoginIp = $this->request->getPost("lastLoginIp");
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "edit",
                "params" => array($user->id)
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));
    }

}
