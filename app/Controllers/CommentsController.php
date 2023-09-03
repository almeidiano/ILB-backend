<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use Exception;

class CommentsController extends BaseController
{
    public string $baseUrl;
    public function index()
    {
        echo 'ok';
    }

    public function __construct() {
        $this->baseUrl = getEnv('app.baseURL');
    }

    public function createComment($postId) {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["user_id", "content"]);

            // Validação
            $validateImages = $this->validate([
                'images' => 'uploaded[images]|max_size[images,5000]|is_image[images]'
            ]);

            // Imagens

            $allImages = [];

            if($validateImages) {
                if ($imagefiles = $this->request->getFiles()) {
                    try {
                        foreach ($imagefiles['images'] as $img) {
                            if ($img->isValid() && ! $img->hasMoved()) {
                                $newName = $img->getRandomName();
                                $img->move(ROOTPATH.'uploads/images', $newName);
                            }

                            // Selecionando as strings dadas acima e colocando-as num array.
                            $imageFile = substr($newName, "0");
                            $allImages[] = ['url' => $this->baseUrl.'uploads/images/'.$imageFile];
                        }
                    }Catch(Exception $e) {
                        throw new Exception("Ocorreu um erro fatal ao enviar as imagens. Erro técnico: ".$e->getMessage(), 500);
                    }
                }
            }

            $commentmodel = new CommentModel();
            return $this->response->setJSON($commentmodel->createComment($postId, $json, $allImages));
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
