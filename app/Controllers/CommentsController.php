<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommentModel;

class CommentsController extends BaseController
{
    public function index()
    {
        echo 'ok';
    }
    public function createComment($postId) {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["content", "images", "videos"]);
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->createComment($postId, $json));
        }
    }

    public function updateComment($commentId) {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["content", "images", "videos"]);
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->updateComment($commentId, $json));
        }
    }

    public function deleteComment($commentId) {
        if($this->request->is('delete')) {
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->deleteComment($commentId));
        }
    }
}
