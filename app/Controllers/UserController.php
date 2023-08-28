<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PostModel;
use App\Models\UserModel;

class UserController extends BaseController
{
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

    public function getPostsFromUser($userId): \CodeIgniter\HTTP\ResponseInterface {
        if($userId) {
            $postmodel = new PostModel();
            $postsByUserId = $postmodel->getPostsByUserId($userId);
            return $this->response->setJSON($postsByUserId);
        }
    }
}
