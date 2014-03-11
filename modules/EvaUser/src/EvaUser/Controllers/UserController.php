<?php

namespace Eva\EvaUser\Controllers;


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for eva_user_users
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "EvaUserUsers", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "id";

        $eva_user_users = EvaUserUsers::find($parameters);
        if (count($eva_user_users) == 0) {
            $this->flash->notice("The search did not find any eva_user_users");

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $eva_user_users,
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
     * Edits a eva_user_user
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {

            $eva_user_user = EvaUserUsers::findFirstByid($id);
            if (!$eva_user_user) {
                $this->flash->error("eva_user_user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "eva_user_users",
                    "action" => "index"
                ));
            }

            $this->view->id = $eva_user_user->id;

            $this->tag->setDefault("id", $eva_user_user->id);
            $this->tag->setDefault("username", $eva_user_user->username);
            $this->tag->setDefault("email", $eva_user_user->email);
            $this->tag->setDefault("mobile", $eva_user_user->mobile);
            $this->tag->setDefault("status", $eva_user_user->status);
            $this->tag->setDefault("flag", $eva_user_user->flag);
            $this->tag->setDefault("screenName", $eva_user_user->screenName);
            $this->tag->setDefault("salt", $eva_user_user->salt);
            $this->tag->setDefault("firstName", $eva_user_user->firstName);
            $this->tag->setDefault("lastName", $eva_user_user->lastName);
            $this->tag->setDefault("password", $eva_user_user->password);
            $this->tag->setDefault("oldPassword", $eva_user_user->oldPassword);
            $this->tag->setDefault("lastUpdateTime", $eva_user_user->lastUpdateTime);
            $this->tag->setDefault("gender", $eva_user_user->gender);
            $this->tag->setDefault("avatar_id", $eva_user_user->avatar_id);
            $this->tag->setDefault("avatar", $eva_user_user->avatar);
            $this->tag->setDefault("timezone", $eva_user_user->timezone);
            $this->tag->setDefault("registerTime", $eva_user_user->registerTime);
            $this->tag->setDefault("lastLoginTime", $eva_user_user->lastLoginTime);
            $this->tag->setDefault("language", $eva_user_user->language);
            $this->tag->setDefault("setting", $eva_user_user->setting);
            $this->tag->setDefault("inviteUserId", $eva_user_user->inviteUserId);
            $this->tag->setDefault("onlineStatus", $eva_user_user->onlineStatus);
            $this->tag->setDefault("lastFreshTime", $eva_user_user->lastFreshTime);
            $this->tag->setDefault("viewCount", $eva_user_user->viewCount);
            $this->tag->setDefault("registerIp", $eva_user_user->registerIp);
            $this->tag->setDefault("lastLoginIp", $eva_user_user->lastLoginIp);
            
        }
    }

    /**
     * Creates a new eva_user_user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "index"
            ));
        }

        $eva_user_user = new EvaUserUsers();

        $eva_user_user->username = $this->request->getPost("username");
        $eva_user_user->email = $this->request->getPost("email", "email");
        $eva_user_user->mobile = $this->request->getPost("mobile");
        $eva_user_user->status = $this->request->getPost("status");
        $eva_user_user->flag = $this->request->getPost("flag");
        $eva_user_user->screenName = $this->request->getPost("screenName");
        $eva_user_user->salt = $this->request->getPost("salt");
        $eva_user_user->firstName = $this->request->getPost("firstName");
        $eva_user_user->lastName = $this->request->getPost("lastName");
        $eva_user_user->password = $this->request->getPost("password");
        $eva_user_user->oldPassword = $this->request->getPost("oldPassword");
        $eva_user_user->lastUpdateTime = $this->request->getPost("lastUpdateTime");
        $eva_user_user->gender = $this->request->getPost("gender");
        $eva_user_user->avatar_id = $this->request->getPost("avatar_id");
        $eva_user_user->avatar = $this->request->getPost("avatar");
        $eva_user_user->timezone = $this->request->getPost("timezone");
        $eva_user_user->registerTime = $this->request->getPost("registerTime");
        $eva_user_user->lastLoginTime = $this->request->getPost("lastLoginTime");
        $eva_user_user->language = $this->request->getPost("language");
        $eva_user_user->setting = $this->request->getPost("setting");
        $eva_user_user->inviteUserId = $this->request->getPost("inviteUserId");
        $eva_user_user->onlineStatus = $this->request->getPost("onlineStatus");
        $eva_user_user->lastFreshTime = $this->request->getPost("lastFreshTime");
        $eva_user_user->viewCount = $this->request->getPost("viewCount");
        $eva_user_user->registerIp = $this->request->getPost("registerIp");
        $eva_user_user->lastLoginIp = $this->request->getPost("lastLoginIp");
        

        if (!$eva_user_user->save()) {
            foreach ($eva_user_user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "new"
            ));
        }

        $this->flash->success("eva_user_user was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "eva_user_users",
            "action" => "index"
        ));

    }

    /**
     * Saves a eva_user_user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $eva_user_user = EvaUserUsers::findFirstByid($id);
        if (!$eva_user_user) {
            $this->flash->error("eva_user_user does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "index"
            ));
        }

        $eva_user_user->username = $this->request->getPost("username");
        $eva_user_user->email = $this->request->getPost("email", "email");
        $eva_user_user->mobile = $this->request->getPost("mobile");
        $eva_user_user->status = $this->request->getPost("status");
        $eva_user_user->flag = $this->request->getPost("flag");
        $eva_user_user->screenName = $this->request->getPost("screenName");
        $eva_user_user->salt = $this->request->getPost("salt");
        $eva_user_user->firstName = $this->request->getPost("firstName");
        $eva_user_user->lastName = $this->request->getPost("lastName");
        $eva_user_user->password = $this->request->getPost("password");
        $eva_user_user->oldPassword = $this->request->getPost("oldPassword");
        $eva_user_user->lastUpdateTime = $this->request->getPost("lastUpdateTime");
        $eva_user_user->gender = $this->request->getPost("gender");
        $eva_user_user->avatar_id = $this->request->getPost("avatar_id");
        $eva_user_user->avatar = $this->request->getPost("avatar");
        $eva_user_user->timezone = $this->request->getPost("timezone");
        $eva_user_user->registerTime = $this->request->getPost("registerTime");
        $eva_user_user->lastLoginTime = $this->request->getPost("lastLoginTime");
        $eva_user_user->language = $this->request->getPost("language");
        $eva_user_user->setting = $this->request->getPost("setting");
        $eva_user_user->inviteUserId = $this->request->getPost("inviteUserId");
        $eva_user_user->onlineStatus = $this->request->getPost("onlineStatus");
        $eva_user_user->lastFreshTime = $this->request->getPost("lastFreshTime");
        $eva_user_user->viewCount = $this->request->getPost("viewCount");
        $eva_user_user->registerIp = $this->request->getPost("registerIp");
        $eva_user_user->lastLoginIp = $this->request->getPost("lastLoginIp");
        

        if (!$eva_user_user->save()) {

            foreach ($eva_user_user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "edit",
                "params" => array($eva_user_user->id)
            ));
        }

        $this->flash->success("eva_user_user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "eva_user_users",
            "action" => "index"
        ));

    }

    /**
     * Deletes a eva_user_user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $eva_user_user = EvaUserUsers::findFirstByid($id);
        if (!$eva_user_user) {
            $this->flash->error("eva_user_user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "index"
            ));
        }

        if (!$eva_user_user->delete()) {

            foreach ($eva_user_user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "eva_user_users",
                "action" => "search"
            ));
        }

        $this->flash->success("eva_user_user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "eva_user_users",
            "action" => "index"
        ));
    }

}
