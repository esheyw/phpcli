<?php
class FluidRouterRedisCache implements FluidRouterCacheEngine {
    protected $r;
    protected $salt = null;

    const DSALT = 'NANITES';

    function __construct(Redis $handle) {
        $this->r = $handle;
    }

    public function get($key) {
        return $this->r->get($key);
    }

    public function set($key, $value, $expire = null) {
        $out = null;
        if (isset($expire)) {
            $out = $this->r->set($key, $value, $expire);
        }
        else {
            $out = $this->r->set($key, $value);
        }
        return $out;
    }

    public function exists($key) {
        return $this->r->exists($key);
    }

    public function hash($str, $salt = self::DSALT) {
        #if a custom default salt has been set, use that instead of the class const
        if (isset($this->salt)) {
            $salt = $this->salt;
        }

        if (isset($this->hashFunc) && function_exists($this->hashFunc)) {
            $func = $this->hashFunc;
            return $func($str, $salt);
        }
        else {
            return $this->_hash($str, $salt);
        }
    }

    public function setHashFunc($name)
    {
        if (!function_exists($name)) throw new InvalidArgumentException('Provided hash function doesn\'t exist!');
        return $this->hashFunc = $name;
    }

    public function setSalt($salt)
    {
        if (!is_string($salt)) throw new InvalidArgumentException('Salt must be a string.');
        return $this->salt = $salt;
    }



    #insecure fallback hash function
    private function _hash($str, $salt = self::DSALT)
    {
        #if a custom default salt has been set, use that instead of the class const
        if (isset($this->salt))
        {
            $salt = $this->salt;
        }

        return sha1($salt.$str);
    }
}
?>