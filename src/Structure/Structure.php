<?php
namespace App\Structure;

use App\Structure\ProvideRegister;
use App\Structure\ProvideSetter;
use App\Interfaces\StructurePatternInterface;

class Structure
{

    const dirToStructures =   'Core/ProvideStructures/';
    /**
     * пространсто для класів паттернів
     */
    const namespace = '\App\Core\ProvideStructures\\';

    public $memory;
    public $time;
    private $foundClasses = [];

    protected $mainName;
    /**
     * @var array
     */
    public $setting = [];
    /**
     * @var array
     */
    public $get = [];
    /**
     * @var \Error
     */
    private $error;
    /**
     * @var array
     */
    protected $classes = [];
    /**
     * @var ProvideSetter
     */
    private $setter;
    /**
     * @var null|array
     */
    protected $structure;


    private static  $instance;

    function __construct(array $structure = null)
    {
        if ($structure) {
            $this->structure = null;
        }
        $this->memory = memory_get_usage();
        $this->time = microtime(true);
    }

    private function validation(array $value): bool
    {
        if (isset($value['setting']) || isset($value['get']) || isset($value['class'])) {
            return true;
        }
        return false;
    }

    /**
     * @param array|null $structure
     * @return \App\Structure\Structure
     * створюєм власний екзепляр за статичним методом
     */
    public static function creats()
    {
        if(self::$instance && self::$instance instanceof Structure){
            return  self::$instance;
        }
        self::$instance = new static();
        return self::$instance;
    }

    private function getStructureClass(array $value, string $name): StructurePatternInterface
    {
        $value['class'] = isset($value['class']) ? $value['class'] : 'auto';
        $class = self::namespace . ($value['class'] === 'auto' ? 'bc' . ucfirst($name) : $value['class']);
        if (class_exists($class)) {
            return new $class;
        }
        $class = $this->findClassByValue($value['get'][0]);

        return new $class;
    }

    /**
     * @param array $structure
     * @return bool
     * Створуєм структура за настройками
     */
    protected function creat(array $structure): bool
    {
        try {
            foreach ($structure as $name => $value) {
                if ($this->validation($value) == false) {
                    continue;
                }
                $this->mainName = !$this->mainName ? $name : $this->mainName;
                if (isset($value['setting']['join'])) {
                    $this->creat($value['setting']['join']);
                }

                $this->get[$name] = $value['get'] = is_array($value['get']) ? array_unique($value['get']) : [$value['get']];
                $this->classes[$name] = $this->getStructureClass($value, $name);

                if (isset($value['setting'])) {
                    $this->setting($value['setting'], $name);
                }
            }

        } catch (\Error $error) {
            $this->error = $error;
            error_log("line = {$error->getLine()}, massage = {$error->getMessage()}, key = {$name}");
//            var_dump([
//                'line' => $error->getLine(),
//                'structure' => $structure,
//                'massage' => $error->getMessage(),
//                'key' => $name,
//
//            ]);
            unset($structure[$name]);
            unset($this->structure[$name]);
            return $this->creat($structure);
        }
        return true;
    }

    /**
     * @param array|null $structure
     * @return $this
     * Додаємо настройки для структури
     */
    public function set(array $structure = null)
    {
        $this->creat($structure ? $structure : $this->structure);
        return $this;
    }

    public function name(string $name)
    {
        $this->mainName = $name;
        return $this;
    }

    public function delete($key)
    {
        foreach ([$this->classes, $this->get, $this->setting] as &$value) {
            if (isset($value[$key])) {
                unset($value[$key]);
            }
        }
        ProvideRegister::removeData($key);
    }

    /**
     * @param array $setting
     * @param string $key
     * Додаємо настройки для запроса
     */
    public function setting(array $setting, string $key)
    {
        $this->setting[$key] = $setting;
    }

    /**
     * @param string $key
     * @param bool $queryArray
     * @return array|null
     * получаємо дані або null
     */
    public function get(string $key = 'user', bool $queryArray = false): array
    {
        if ($this->isEmpty($key)) {
            return [];
        }
        try {
            $this->setter = new ProvideSetter($this, $key);
        } catch (\Exception $exception) {
            error_log($exception);
            return [];
        }
        if ($queryArray == true) {
            return $this->setter->getValue(true);
        }
        $result = ProvideRegister::get($key) && !isset(ProvideRegister::get($key)[0]['size']) ? ProvideRegister::get($key) : $this->setter->getValue();
        ProvideRegister::set($key, $result);
        return $result;
    }

    /**
     * @param \Closure $callback
     * @param string $key
     * @return mixed
     * виконуэмо callback функцію для даних з базиданих
     */
    public function getData(\Closure $callback, string $key = 'user')
    {
        return call_user_func($callback, $this->get($key), $this->setter);
    }

    /**
     * @param $value
     * @return string|null
     * Проскановуємо дерикторію з структурами і повертаємо імя структири яке містить $value
     */
    public function findClassByValue(string $value)
    {
        $this->foundClasses = ProvideRegister::getFoundClasses() ?: $this->foundClasses;
        if ($this->foundClasses) {
            foreach ($this->foundClasses as $className => $pattern) {
                if (array_key_exists($value, $pattern)) {
                    return self::namespace . $className;
                }
            }
            return null;
        } else {
            $files = array_diff(scandir(self::dirToStructures), ['..', '.', '']);

            foreach ($files as $file) {
                try {
                    $file = explode('.', $file)[0];
                    $classNameSpace = self::namespace . $file;
                    $cls = new $classNameSpace;
                    if (method_exists($classNameSpace, 'getPattern')) {
                        /** @var $cls StructurePatternInterface $pattern */
                        $pattern = $cls->getPattern();
                        ProvideRegister::setFoundClasses($file, $pattern);
                        $this->foundClasses[$file] = $pattern;
                    }
                } catch (\Error $error) {
                    error_log($error);
                    continue;
                }
            }
        }
        return $this->findClassByValue($value);

    }

    /**
     * @param $key
     * @return StructurePatternInterface
     * повертаємо обєкт паттерн
     */
    public function classes($key = null): StructurePatternInterface
    {
        if (!$key) {
            throw new \Error("Null given in " . __METHOD__);

        }

        if (!$key && key($this->classes)) {
            return $this->classes(key($this->classes));
        }

        if (!isset($this->classes[$key])) {
            foreach (array_keys($this->classes) as $name) {
                if ($this->classes[$name]->getFactoryName() == $key) {
                    return $this->classes[$name];
                }
            }
            if ($this->structure[$key]) {
                $class = $this->findClassByValue($this->structure[$key]['get'][0]);
                return new $class;
            }
            //return new ($this->findClassByValue($this->structure[$key]['get'][0]));
        }
        return $this->classes[$key];

    }

    public function isEmpty(string $key = ''): bool
    {
        if (!$key) {
            return $this->isEmpty(key($this->classes));
        }
        if (!isset($this->classes[$key])) {
            foreach (array_keys($this->classes) as $name) {
                if ($this->classes[$name]->getFactoryName() == $key) {
                    return false;
                }
            }
        }
        return !isset($this->classes[$key]);
    }

    /**
     * @return \Error получаємо поилки
     */
    public function error(): \Error
    {
        return $this->error;
    }

    public function pattern(): array
    {
        $list = [];
        foreach ($this->classes as $item) {
            /** @var $item StructurePatternInterface */
            $list = array_merge($list, $item->getPattern());
        }
        return $list;
    }

    public function findBySelector(string $selector)
    {
        $result = [];
        foreach ($this->classes as $name => $class) {
            /** @var $class StructurePatternInterface */
            $pattern = $class->getPattern();
            foreach ($pattern as $name_selector => $value) {
                if (is_array($value) && isset($value['select'])) {
                    if (trim($value['select']) == trim($selector) || stristr($selector, $value['select']) !== false) {
                        $result[] = $name_selector;
                    }
                }
            }
        }
        return !$result ? null : array_unique($result);
    }
}