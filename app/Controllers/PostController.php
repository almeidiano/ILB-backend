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

            // Validação
            $validateImages = $this->validate([
                'image' => 'uploaded[image]|max_size[image,5000]|is_image[image]'
            ]);

            // Imagens
            if($validateImages) {
                $image = $this->request->getFile('image');

                if (! $image->hasMoved()) {
                    $imageName = $image->getRandomName();
                    $imagePath = 'https://ilovebrides.almeidiano.dev/uploads/images/'.$imageName;
                    
                    try {
                        //Image manipulation
                        $imageManager = \Config\Services::image()
                        ->withFile($image)
                        ->resize(550, 550, true, 'height')
                        // ->text('Copyright 2017 My Photo Co', [
                        //     'color'      => '#fff',
                        //     'opacity'    => 0.5,
                        //     'withShadow' => true,
                        //     'hAlign'     => 'center',
                        //     'vAlign'     => 'bottom',
                        //     'fontSize'   => 20,
                        // ])
                        ->save(ROOTPATH.'uploads/images/'.$imageName);
                    } catch (\Throwable $th) {
                        exit('Ocorreu um erro ao manipular a imagem: '.$th->getMessage());
                    }

                    // $image->move(ROOTPATH.'uploads/images', $imageName);

                    $json = $this->request->getVar(["title", "content", "public", "theme_id", "author_id"]);
                    $postmodel = new PostModel();
                    return $this->response->setJSON($postmodel->createPost($json, $imagePath));
                }

                exit($image);
            }
        }
    }

    // Read
    public function getAllPosts() {
        $postmodel = new PostModel();
        $posts = $postmodel->getAllPosts();
        return $this->response->setJSON($posts);
    }

    public function getAllRecommendedPosts() {
        $postmodel = new PostModel();
        $posts = $postmodel->getAllRecommendedPosts();

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
            // Validação
            $validateImages = $this->validate([
                'image' => 'uploaded[image]|max_size[image,5000]|is_image[image]'
            ]);

            global $imagePath;

            if($validateImages) {
                $image = $this->request->getFile('image');
                if (! $image->hasMoved()) {
                    $imageName = $image->getRandomName();
                    $imagePath = ROOTPATH.'uploads/images/'.$imageName;
    
                    try {
                        //Image manipulation
                        $imageManager = \Config\Services::image()
                        ->withFile($image)
                        ->resize(550, 550, true, 'height')
                        // ->text('Copyright 2017 My Photo Co', [
                        //     'color'      => '#fff',
                        //     'opacity'    => 0.5,
                        //     'withShadow' => true,
                        //     'hAlign'     => 'center',
                        //     'vAlign'     => 'bottom',
                        //     'fontSize'   => 20,
                        // ])
                        ->save(ROOTPATH.'uploads/images/'.$imageName);

                        try {
                            $json = $this->request->getVar(["title", "content", "public"]);
                            $postmodel = new PostModel();
                            return $this->response->setJSON($postmodel->updatePost($postId, $json, $imagePath));  
                        } catch (\Throwable $th) {
                            exit('erro: '.$th->getMessage());
                        } 
                    } catch (\Throwable $th) {
                        exit('Ocorreu um erro ao manipular a imagem: '.$th->getMessage());
                    }             
                }
            }
            
            try {
                $json = $this->request->getVar(["title", "content", "public"]);
                $postmodel = new PostModel();
                return $this->response->setJSON($postmodel->updatePost($postId, $json, $imagePath));  
            } catch (\Throwable $th) {
                exit('erro: '.$th->getMessage());
            } 
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
