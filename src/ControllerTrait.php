<?php


namespace App;

use App\Interfaces\ControllerInterface;
use App\Structure\Structure;

/** @property ControllerInterface $controller
 */
trait ControllerTrait
{
    protected function find($id){
        return $this->where("id = ".(int)$id);
    }

    protected function where(string $where){
        $table = $this->controller->getTable();
        return Structure::creats()->set(
            [
                $table =>
                    [
                        'get' => ['all'],
                        'class' => $table,
                        'setting' =>
                            [
                                'where' => $where
                            ]
                    ]
            ]
        )->getData(function ($row){
            if(!empty($row)){
                $row = $row[0];
                return (object)$row;
            }
            return false;
        }, '');
    }
}