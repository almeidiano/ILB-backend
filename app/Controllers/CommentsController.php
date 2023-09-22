<?php

namespace App\Controllers;

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

    // Create
    public function createComment($postId) {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["user_id", "text"]);

            // Validação
            // $validateImages = $this->validate([
            //     'images' => 'uploaded[images]|max_size[images,5000]|is_image[images]'
            // ]);

            // // Imagens

            // $allImages = [];

            // if($validateImages) {
            //     if ($imagefiles = $this->request->getFiles()) {
            //         try {
            //             foreach ($imagefiles['images'] as $img) {
            //                 if ($img->isValid() && ! $img->hasMoved()) {
            //                     $newName = $img->getRandomName();
            //                     $img->move(ROOTPATH.'uploads/images', $newName);
            //                 }

            //                 // Selecionando as strings dadas acima e colocando-as num array.
            //                 $imageFile = substr($newName, "0");
            //                 $allImages[] = ['url' => $this->baseUrl.'uploads/images/'.$imageFile];
            //             }
            //         }Catch(Exception $e) {
            //             throw new Exception("Ocorreu um erro fatal ao enviar as imagens. Erro técnico: ".$e->getMessage(), 500);
            //         }
            //     }
            // }

            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->createComment($postId, $json));
            // return $this->response->setJSON($commentmodel->createComment($postId, $json, $allImages));
        }
    }

    // Update
    public function updateComment($commentId) {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["content", "images", "videos"]);
            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->updateComment($commentId, $json));
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
