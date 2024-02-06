<?php

namespace App\Controllers\CMS;

use App\Models\CMS\FooterModel;
use App\Controllers\BaseController;

class FooterController extends BaseController
{
		private function validateValuesFromHybridType($target, $content): bool {
			switch ($target) {
				case 'mainNavigation':
					foreach($content as $obj) {
						if(property_exists($obj, 'text') && property_exists($obj, 'children')) {
							return true;
						}else return false;
					}
				break;
				case 'socialMedia':
					foreach($content as $obj) {
						if(property_exists($obj, 'url') && property_exists($obj, 'icon')) {
							return true;
						}else return false;
					}
				break;
				case 'weddingDistricties':
					foreach($content as $obj) {
						if(property_exists($obj, 'url') && property_exists($obj, 'text')) {
							return true;
						}else return false;
					}
				break;
				case 'legal':
					foreach($content as $obj) {
						if(property_exists($obj, 'url') && property_exists($obj, 'text')) {
							return true;
						}else return false;
					}
				break;
				case 'contact':
					foreach($content as $obj) {
						if(property_exists($obj, 'type') && property_exists($obj, 'text')) {
							return true;
						}else return false;
					}
				break;
			}
		}

    public function getFooterInfo(): \CodeIgniter\HTTP\ResponseInterface
    {
        $footerInfoModel = new FooterModel();
        return $this->response->setJSON($footerInfoModel->getFooterInfo());
    }

    public function updateFooterMainNavigation(): \CodeIgniter\HTTP\ResponseInterface
    {
        if($this->request->is('put')) {
            // $footerInfoModel = new FooterModel();
            // $analyze = $this->request->getVar(["type", "target"]);

            // // se o tipo da análise da variável for híbrido, é necessário existir 
            // // o parametro content no input com os dados corretos
            // if($analyze['type'] === 'hybrid') {
            //   $content = $this->request->getVar("content");

            //   if($this->validateValuesFromHybridType($analyze['target'], $content) === true) {
            //     return $this->response->setJSON($footerInfoModel->updateFooterAnchor($content, $analyze['target']));
            //   }
            // }

            // // se o tipo da análise da variável for único, é necessário existir o parametro text no input
            // if($analyze['type'] === 'unique') {
            //     $text = $this->request->getVar("text");
            //     return $this->response->setJSON($footerInfoModel->updateFooterAnchor($text, $analyze['target']));
            // }
			$footerInfoModel = new FooterModel();
            $json = $this->request->getVar("mainNavigation");
			return $this->response->setJSON($footerInfoModel->updateFooterMainNavigation($json));
		}
    }

	// public function updateFooterMainNavigation(): \CodeIgniter\HTTP\ResponseInterface
    // {

	// }
}
