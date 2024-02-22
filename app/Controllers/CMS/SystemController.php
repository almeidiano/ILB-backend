<?php

namespace App\Controllers\CMS;

use App\Models\CMS\SystemModel;
use App\Controllers\BaseController;

class SystemController extends BaseController
{
    public function getSystemInfo(): \CodeIgniter\HTTP\ResponseInterface
    {
        $sysModel = new SystemModel();
        return $this->response->setJSON($sysModel->getSystemInfo());
    }

    public function updateSystemInfo(): \CodeIgniter\HTTP\ResponseInterface {
        $sysModel = new SystemModel();

        $json = $this->request->getVar(["websiteTitle", "contactNumber", "websiteEmail", "websiteDescription", "websiteKeywords"]);
        return $this->response->setJSON($sysModel->updateSystemInfo($json));
    }
}
