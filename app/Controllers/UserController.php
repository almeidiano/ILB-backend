<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Models\UserModel;
use MongoDB\BSON\ObjectId;

class UserController extends BaseController
{

    //Create
    public function savePost($postId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('post')) {
            $user_id = $this->request->getVar("user_id");
            $usermodel = new UserModel();
            return $this->response->setJSON($usermodel->savePost($postId, $user_id));
        }
    }

    //Read
    public function getInteractedPostFromUserId($userId, $postId): \CodeIgniter\HTTP\ResponseInterface {
            $postmodel = new PostModel();
            return $this->response->setJSON($postmodel->getInteractedPostFromUserId($userId, $postId));
    }

    public function getPostsByUserId($userId) {
        $postmodel = new PostModel();
        return $this->response->setJSON($postmodel->getPostsByUserId($userId));
    }
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('post')) {
            $usermodel = new UserModel();
            $email = $this->request->getVar("email");
            $password = $this->request->getVar("password");
            $user = $usermodel->login($email, $password);

            // return view('welcome_message');
            return $this->response->setJSON($user);
        }
    }
    public function getUserInfo($name, $email) {
        if($this->request->is('post')) {
            $usermodel = new UserModel();
            $name = $this->request->getVar("name");
            $email = $this->request->getVar("email");
            return $this->response->setJSON($usermodel->getUserByNameAndEmail($name, $email));
        }
    }
    public function getPostsSavedByUser($userId) {
        $postmodel = new PostModel();
        return $this->response->setJSON($postmodel->getPostsSavedByUser($userId, null, 'all'));
    }

    public function getPostsCommentedByUser($userId): \CodeIgniter\HTTP\ResponseInterface {
        $postmodel = new PostModel();
        return $this->response->setJSON($postmodel->getPostsCommentedByUser($userId));
    }

    //Update

    //Delete
    public function deleteSavedPost($postId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('delete')) {
            $user_id = $this->request->getVar("user_id");
            $userModel = new UserModel();
            return $this->response->setJson($userModel->deleteSavedPost($postId, $user_id));
        }
    }

    public function getUser($userId) {
        if ($userId) try {
            $usermodel = new UserModel();
            return $this->response->setJSON($usermodel->getUser($userId));
        } catch (Exception $e) {
            exit("O user especificado com id: ".$userId." não existe");
        }
    }

    public function getUserInteractedPosts($userId) {
        exit('ok');
    }
}
