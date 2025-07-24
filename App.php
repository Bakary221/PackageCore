<?php

namespace App\core;

use Symfony\Component\Yaml\Yaml;
use ReflectionClass;

class App {
    private static array $config = [];
    private static array $instances = [];

    public static function loadDependenciesFromYaml(string $file) {
        if (file_exists($file)) {
            $data = Yaml::parseFile($file);
            self::$config = $data['dependencies'] ?? [];
        }
    }

    public static function getDependency(string $className) {
        // Si déjà instanciée, on la renvoie
        if (isset(self::$instances[$className])) {
            return self::$instances[$className];
        }

        // Si la classe n'existe pas
        if (!class_exists($className)) {
            throw new \Exception("Classe $className introuvable");
        }

        $reflector = new ReflectionClass($className);
        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            // Pas de constructeur → simple instance
            $instance = new $className();
        } else {
            $params = $constructor->getParameters();
            $dependencies = [];

            foreach ($params as $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    $depClass = $type->getName(); // nom de la classe
                    $dependencies[] = self::getDependency($depClass); // injection récursive
                } else {
                    // Valeurs par défaut si existantes
                    $dependencies[] = $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
                }
            }
            // var_dump($reflector); die;
            if (method_exists($className, 'getInstance')) {
                $instance = $className::getInstance(...$dependencies);
            } else {
                $instance = $reflector->newInstanceArgs($dependencies);
            }
        }

        self::$instances[$className] = $instance;
        return $instance;
    }
}
