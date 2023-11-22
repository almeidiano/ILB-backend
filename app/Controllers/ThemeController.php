<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\PostModel;

class ThemeController extends BaseController
{
    // Create
    public function createTheme() {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["name", "isPublic"]);
            $themeModel = new ThemeModel();
            return $this->response->setJSON($themeModel->createTheme($json));
        }
    }
    public function enterTheme($themeId) {
        if($this->request->is('post')) {
            $userId = $this->request->getVar("userId");
            $themeModel = new ThemeModel();
            return $this->response->setJSON($themeModel->enterTheme($themeId, $userId));
        }
        // try {
        //     exit('ok');
        // } catch (\Throwable $th) {
        //     exit('erro: '.$th->getMessage());
        // }
    }
    // Read
    public function getAllThemes()
    {
        $themeModel = new ThemeModel();
        return $this->response->setJSON($themeModel->getAllThemes());
    }
    public function getTheme($id) {        
        $themeModel = new ThemeModel();
        return $this->response->setJSON($themeModel->getTheme($id));
    }
    public function getPostByThemeId($themeId, $postId) {    
        $postmodel = new PostModel();
        $themeModel = new ThemeModel();
        return $this->response->setJSON($postmodel->getPostByThemeId($postId, $themeId));
    }
    public function getPostsByThemeId($themeId) {    
        $postmodel = new PostModel();
        return $this->response->setJSON($postmodel->getPostsByThemeId($themeId));
    }
    public function getAllPendingUsersFromThemes() {    
        $themeModel = new ThemeModel();
        return $this->response->setJSON($themeModel->getAllPendingUsersFromThemes());
    }
    public function checkIfUserBelongsToPrivateTheme($themeId) {    
        if($this->request->is('post')) {
            $userId = $this->request->getVar("userId");
            $themeModel = new ThemeModel();
            return $this->response->setJSON($themeModel->checkIfUserBelongsToPrivateTheme($themeId, $userId));
        }
    }
    // Update
    public function updateTheme($themeID)
    {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["name", "private"]);
            $themeModel = new ThemeModel();
            return $this->response->setJSON($themeModel->updateTheme($json, $themeID));
        }
    }
    // Delete
    public function deleteTheme($themeID)
    {
        if($this->request->is('delete')) {
            $themeModel = new ThemeModel();
            return $this->response->setJSON($themeModel->deleteTheme($themeID));
        }
    }
}
