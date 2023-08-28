<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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
            $json = $this->request->getVar(["title", "content", "districtName", "restricted"]);
            $postmodel = new PostModel();
            return $this->response->setJSON($postmodel->createPost($json));
        }
    }

    // Read
    public function getAllPosts(): \CodeIgniter\HTTP\ResponseInterface {
        $postmodel = new PostModel();
        $posts = $postmodel->getAllPosts();

        // return view('welcome_message');
        return $this->response->setJSON($posts);
    }

    public function getPost($id): \CodeIgniter\HTTP\ResponseInterface {
        $postmodel = new PostModel();
        $post = $postmodel->getPost($id);

        // return view('welcome_message');
        return $this->response->setJSON($post);
    }

    //Update
    public function updatePost($postId) {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["title", "content", "districtName", "restricted"]);
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
