<?php

namespace App\Controllers\Community;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use Exception;

class CommentsController extends BaseController
{
    private string $baseUrl;

    public function __construct() {
        $this->baseUrl = getEnv('app.baseURL');
    }

    public function getComment($commentId, $postId, $userId) {
        $commentmodel = new CommentModel();
        return $this->response->setJSON($commentmodel->getComment($commentId, $postId, $userId));
    }

    // Read 
    public function getAllCommentsFromPost($postId) {
        $commentmodel = new CommentModel();
        return $this->response->setJSON($commentmodel->getAllCommentsFromPost($postId));
    }

    // Create
    public function createComment($postId) {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["user_id", "text"]);

            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->createComment($postId, $json));
            // return $this->response->setJSON($commentmodel->createComment($postId, $json, $allImages));
        }
    }

    // Update
    public function updateComment($commentId) {
        if($this->request->is('put')) {
            $content = $this->request->getVar("content");
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->updateComment($commentId, $content));
        }
    }

    // Delete
    public function deleteComment($commentId) {
        if($this->request->is('delete')) {
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->deleteComment($commentId));
        }
    }
}
