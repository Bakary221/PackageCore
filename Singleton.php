<?php

namespace App\core;

use ReflectionClass;

class Singleton {
    private static array $instances = [];

    public static function getInstance(...$args): static {
        $class = static::class;

        if (!isset(self::$instances[$class])) {
            $reflector = new ReflectionClass($class);

            // On crée l'objet sans appeler le constructeur
            $instance = $reflector->newInstanceWithoutConstructor();

            // On appelle le constructeur privé/protégé manuellement
            $constructor = $reflector->getConstructor();
            if ($constructor) {
                $constructor->setAccessible(true);
                $constructor->invokeArgs($instance, $args);
            }

            self::$instances[$class] = $instance;
        }

        return self::$instances[$class];
    }

    protected static function removeInstance(): void {        $class = static::class;
        if (isset(self::$instances[$class])) {
            unset(self::$instances[$class]);
        }
    }
}
