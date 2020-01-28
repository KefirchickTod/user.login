<?php


class ServerData
{

    /**
     * @var ServerData
     */
    public static $instance;

    protected $server;

    function __construct()
    {
        $this->server = $_SERVER;
    }

    public function get(string $key, $default = null)
    {
        return isset($this->server[$key]) ? $this->server[$key] : $default;
    }

    public function delete($key): bool
    {
        if (isset($this->server[$key])) {
            unset($this->server[$key]);
            return true;
        }
        return false;
    }

    public function update($key, $value)
    {
        $this->server[$key] = $value;
    }

    public function is(string $key){
        return isset($this->server[$key]);
    }

    public static function instance()
    {
        if (self::$instance instanceof ServerData) {
            return self::$instance;
        }
        self::$instance = new ServerData();
        return self::$instance;
    }
}