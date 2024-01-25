<?php

namespace App\Controllers\CMS;

use App\Models\CMS\UserModel;
use App\Controllers\BaseController;

class UserController extends BaseController
{
    private function checkIfCredentialsAreNull($credentials) {
        foreach ($credentials as $key => $value) {
            if(empty($value)) {
                exit("Conteúdo não especificado");
            }
        }
    }
        
    /**
     * createUser
     * CREATE
     * @return CodeIgniter\HTTP\ResponseInterface
     */

    public function createUser(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('post')) {
            $json = $this->request->getVar(["name", "username", "password", "email", "role"]);
            if(!$this->checkIfCredentialsAreNull($json)) {
                $usermodel = new UserModel();
                return $this->response->setJSON($usermodel->createUser($json));
            } 
        }
    }

    /**
     * getUser
     * READ
     * @return CodeIgniter\HTTP\ResponseInterface
     */

    public function getUser($id): \CodeIgniter\HTTP\ResponseInterface
    {
        $usermodel = new UserModel();
        return $this->response->setJSON($usermodel->getUser($id));
    }

    /**
     * getAllUsers
     * READ
     * @return CodeIgniter\HTTP\ResponseInterface
     */

     public function getAllUsers(): \CodeIgniter\HTTP\ResponseInterface
     {
         $usermodel = new UserModel();
         return $this->response->setJSON($usermodel->getAllUsers());
     }

    /**
     * updateUser
     * UPDATE
     * @return CodeIgniter\HTTP\ResponseInterface
     */
     public function updateUser($id)
     {
         if($this->request->is('put')) {
            $json = $this->request->getVar(["name", "username", "password", "email", "role"]);

            if(!$this->checkIfCredentialsAreNull($json)) {
                $usermodel = new UserModel();
                return $this->response->setJSON($usermodel->updateUser($json, $id));
            }
         }
     }

    /**
     * deleteUser
     * DELETE
     * @return CodeIgniter\HTTP\ResponseInterface
     */
    public function deleteUser($id)
    {
        if($this->request->is('delete')) {
            $usermodel = new UserModel();
            return $this->response->setJSON($usermodel->deleteUser($id));
        }
    }
}
