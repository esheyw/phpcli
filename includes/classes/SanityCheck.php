<?php
abstract class SanityCheck { # Sanity Check
    const DNAME = '-Undefined-';
    const TYPES = [
        "boolean",
        "integer",
        "double",
        "string",
        "array",
        "object",
        "resource",
        "NULL"
    ];
    public static function check($var, $type, $name = self::DNAME) {
        if (!in_array($type, self::TYPES, true) && is_object($var)) { #assume checking for class
            if (!is_a($var, $type)) throw new InvalidArgumentException('Expected a "'.$type.'", not a "'.get_class($var).'".');
        }
        if (gettype($var) !== $type) throw new InvalidArgumentException('Expected '.$name.' to be '.$type.', not '.gettype($var).'.');
        return true;
    }
    public static function cStr($var, $name = self::DNAME) {
        if (self::check($var, 'string', $name)) return $var;
    }
    public static function cInt($var, $name = self::DNAME) {
        if (self::check($var, 'integer', $name)) return $var;
    }
    public static function cArr ($var, $name = self::DNAME) {
        if (self::check($var, 'array', $name)) return $var;
    }
    public static function cBool ($var, $name = self::DNAME) {
        if (self::check($var, 'boolean', $name)) return $var;
    }
    public static function cDub ($var, $name = self::DNAME) {
        if (self::check($var, 'double', $name)) return $var;
    }
    public static function cRes ($var, $name = self::DNAME) {
        if (self::check($var, 'resource', $name)) return $var;
    }
}
?>