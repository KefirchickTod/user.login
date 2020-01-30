<?php


namespace App;


use App\Models\Model;
use App\Structure\Structure;

/** @property Model $model
 * @property Model $table
 */
trait ModelTrait
{
    
    protected $fillable = [];
    
    protected function find($id){
        return $this->where("id = ".(int)$id);
    }

    protected function where(string $where){
        $table = $this->model->getTable();
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

    public function fill(array $data){
        $fill = [];

        foreach ($data as $name => $value){
            if(in_array($name, $this->fillable)){
                $fill[$name] = $value;
            }
        }
        $this->fillable = $fill;
        return $this;
    }
    
    public function save(){
        DB::getInstance()->dynamicInsert($this->table, $this->fillable);
    }


}