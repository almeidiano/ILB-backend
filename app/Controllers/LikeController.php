<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LikeModel;

class LikeController extends BaseController
{
    public function getPostsLikedFromUser($userId): \CodeIgniter\HTTP\ResponseInterface
    {
        $likeModel = new LikeModel();
        return $this->response->setJSON($likeModel->getPostsLikedFromUser($userId));
    }

    public function likePost($postId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('post')) {
            $user_id = $this->request->getVar("user_id");
            $likemodel = new LikeModel();
            return $this->response->setJSON($likemodel->likePost($postId, $user_id));
        }
    }

    public function likeComment($commentId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('post')) {
            $user_id = $this->request->getVar("user_id");
            $likemodel = new LikeModel();
            return $this->response->setJSON($likemodel->likeComment($commentId, $user_id));
        }
    }

    public function deleteLikedPost($postId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('delete')) {
            $user_id = $this->request->getVar("user_id");
            $likemodel = new LikeModel();
            return $this->response->setJSON($likemodel->deleteLikedPost($postId, $user_id));
        }
    }
}
