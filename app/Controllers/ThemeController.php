<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ThemeModel;

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
    // Read
    public function getAllThemes()
    {
        $themeModel = new ThemeModel();
        return $this->response->setJSON($themeModel->getAllThemes());
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
