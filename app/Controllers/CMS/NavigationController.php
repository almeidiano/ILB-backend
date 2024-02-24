<?php

namespace App\Controllers\CMS;

use App\Models\CMS\NavigationModel;
use App\Controllers\BaseController;

class NavigationController extends BaseController
{
    public function getNavInfo(): \CodeIgniter\HTTP\ResponseInterface
    {
        $navigationInfoModel = new NavigationModel();
        return $this->response->setJSON($navigationInfoModel->getNavInfo());
    }

    public function addNavItem(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["url", "text", "children"]);
            $navigationInfoModel = new NavigationModel();
            return $this->response->setJSON($navigationInfoModel->addNavItem($json));
        }
    }

    public function update(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('put')) {
            $json = $this->request->getVar(["items", "navigationLogo"]);
            $navigationInfoModel = new NavigationModel();
            return $this->response->setJSON($navigationInfoModel->updateNavItem($json));
        }
    }

    public function deleteNavItem(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('delete')) {
            $json = $this->request->getVar(["url", "text"]);
            $navigationInfoModel = new NavigationModel();
            return $this->response->setJSON($navigationInfoModel->deleteNavItem($json));
        }
    }
}
