<?php


namespace App;


class Recourse
{

    const rescource = '..\resources\\';

    /**
     * @var string
     */
    private $layots = '';
    /**
     * @var string
     */
    private $page = '';
    /**
     * @var array $param
     */
    private $param;
    /**
     * @var string[] $names
     */
    private $names;

    public function __construct($names = [], $param = [])
    {
        $this->names = $names;
        $this->param = $param;
    }

    public function set($names = [], $param = [])
    {
        $this->names = $names;
        $this->param = $param;
    }

    protected function parse()
    {
        foreach ($this->names as &$name) {
            $name = str_replace('.', '/', $name);
        }
        return $this;
    }

    private function replace($key, $value): string
    {

        if (is_string($key) && !is_int($key)) {
            $this->layots = preg_replace('/{%_' . $key . '_%}/', $value, $this->layots);

            return $this->layots;
        }
        return $value;
    }

    /**
     * @return mixed|string
     * @throws \ErrorException
     */
    public function render(): string
    {
        $this->parse();
        if ($this->param) {
            extract($this->param);
        }
        try {
            foreach ($this->names as $replace => $path) {
                $path = !file_exists("$path.php") ? self::rescource . "$path.php" : "$path.php";
                if (file_exists($path)) {

                    ob_start();
                    include_once($path);
                    $content = ob_get_clean();

                    $this->page .= $this->replace($replace, $content);
                } else {

                    throw new \Error("File not found in path " . $path);
                }
            }
        } catch (\Error $error) {
            var_dump($error->getMessage());
            error_log($error);
        }
        $this->layots = '';
        $page = preg_replace( "/{%_content_%}/", '' ,$this->page);
        $this->page = '';
        return $page;
    }


    public function layout($link)
    {
        $link = "layots." . $link;
        $link = str_replace('.', '/', $link);
        $this->layots = file_get_contents(self::rescource . "$link.php");
        return $this;
    }

    public function __toString()
    {
        try {
            ob_get_clean();
            return $this->render();
        } catch (\ErrorException $e) {
            var_dump($e);
            error_log($e->getMessage());
            return '';
        }
    }

}