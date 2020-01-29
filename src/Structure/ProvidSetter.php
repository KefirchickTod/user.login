<?php

namespace App\Structure;


use App\DB;



use App\Structure\ProvideQueryGenerate;
use App\Structure\Structure;
use PDO;

class ProvideSetter
{

    /*
     * @var PDO and DB
     * */
    private $database;

    private $parser;

    /**
     * @var Structure
     */
    private $structure;


    private $key;

    function __construct(Structure $structure, string $key)
    {
        $this->database = DB::getInstance();
        $this->structure = $structure;
        $this->key = $key;
        $this->parser = new ProvideQueryGenerate($structure->classes($key), $structure->get[$key],
            isset($structure->setting[$key]) ? $structure->setting[$key] : []);

    }


    /**
     * @param bool $array
     * @return array
     */
    public function getValue($array = false): array
    {
        return $array === true ? $this->parser->getQuery(true) : $this->filter($this->parser->getQuery());
    }

    /**
     * @param $query
     * @return array
     */
    private function filter($query) : array
    {
        $result = [];
        $forClean = $this->database->querySql($query)->fetchAll(PDO::FETCH_ASSOC);
        $forClean = $this->array_unique_multidimensional($forClean);
        if ($forClean) {
            foreach ($forClean as $value) {
                foreach ($value as $name => $v) {
                    if (!is_int($name)) {
                        $data[$name] = $v;
                    }
                }
                $result[] = $data;

            }
        } else {
            $result = $forClean;
        }

        return $result;
    }

    /**
     * @param $input
     * @return array
     */
    private function array_unique_multidimensional($input): array
    {
        $serialized = array_map('serialize', $input);
        $unique = array_unique($serialized);
        return array_intersect_key($input, $unique);
    }

}