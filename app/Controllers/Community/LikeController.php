<?php

namespace App\Controllers\Community;

use App\Controllers\BaseController;
use App\Models\Community\LikeModel;

class LikeController extends BaseController
{
    //Create
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

    // Read
    public function getPostsLikedFromUser($userId): \CodeIgniter\HTTP\ResponseInterface
    {
        $likeModel = new LikeModel();
        return $this->response->setJSON($likeModel->getPostsLikedFromUser($userId, null, 'all'));
    }
    public function getCommentsLikedFromUser($userId): \CodeIgniter\HTTP\ResponseInterface
    {
        $likeModel = new LikeModel();
        return $this->response->setJSON($likeModel->getLikedComments($userId));
    }

    // Delete
    public function deleteLikedComment($commentId): \CodeIgniter\HTTP\ResponseInterface {
        if($this->request->is('delete')) {
            $user_id = $this->request->getVar("user_id");
            $likemodel = new LikeModel();
            return $this->response->setJSON($likemodel->deleteLikedComment($commentId, $user_id));
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
