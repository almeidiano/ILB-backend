<?php

namespace App\Controllers;

use App\Models\LikeModel;
use App\Models\PostModel;

class PostController extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        echo 'ok';
    }

    //Create
    public function createPost()
    {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["title", "content", "public", "theme_id", "author_id"]);
            $postmodel = new PostModel();
            return $this->response->setJSON($postmodel->createPost($json));
        }
    }

    // Read
    public function getAllPosts() {
        $postmodel = new PostModel();
        $posts = $postmodel->getAllPosts();
        return $this->response->setJSON($posts);
    }

    public function getPost($id) {
        $userId = $this->request->getGet("user_id");
        $postmodel = new PostModel();

        if($userId) {
            return $this->response->setJSON($postmodel->getInteractedPostFromUserId($id, $userId));
        }else {
            return $this->response->setJSON($postmodel->getPost($id));
        }
    }

    private function getInteractedPostFromUserId($postId, $userId) {
        $postmodel = new PostModel();
        return $this->response->setJSON($postmodel->getInteractedPostFromUserId($postId, $userId));
    }

    //Update
    public function updatePost($postId) {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["title", "content", "public"]);
            $postmodel = new PostModel();
            return $this->response->setJSON($postmodel->updatePost($postId, $json));
        }
    }

    //Delete
    public function deletePost($postId): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('delete')) {
            $postmodel = new PostModel();
            return $this->response->setJSON($postmodel->deletePost($postId));
        }
    }
}
