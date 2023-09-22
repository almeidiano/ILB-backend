<?php
namespace App\Libraries;
class Simplifier
{
    public static function simplify($model, $method)
    {
        if($model && $method) {
            if (method_exists($model, $method)) {
                return $model->$method($parameters);
            } else {
                // Lida com casos em que o método não existe no modelo.
                return null; // Ou pode lançar uma exceção ou tratar de outra forma, conforme necessário.
            }
        }else {
            throw new \Exception('Adicione modelo ou método para a função simplify', 500);
        }
    }
}