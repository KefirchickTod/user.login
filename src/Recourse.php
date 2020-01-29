<?php


namespace App;


class Recourse
{

    const baseDir = '..\resources\\';

    private $template = "<html>";

    private $resourse;

    private $parram;

    public function add(array $recourse)
    {
        $this->resourse = $recourse;
        return $this;
    }


    private function parse()
    {
        foreach ($this->resourse as &$name) {
            $name = str_replace('.', '/', $name);
        }

    }

    public function addParram(array $parram)
    {
        $this->parram = $parram;
    }

    public function dir()
    {
        if ($this->parram) {
            extract($this->parram);
        }
        foreach ($this->resourse as $value) {

            $value = self::baseDir . $value . '.php';
            if (file_exists($value)) {
                $this->template .= include_once($value);
            } else {
                throw new \Error("!File_exits for file " . $value);
            }
        }
    }

    public function __toString()
    {

        $this->parse();
        $this->dir();
        $this->template .= "</html>";
        return $this->template;
    }

}