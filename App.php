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
            // var_dump(self::$config); die;
        }
    }

    public static function getDependency(string $category, string $key) {
        // Vérifie si la dépendance existe dans la config
        if (!isset(self::$config[$category][$key])) {
        // Essaye juste le nom court (ex: SecurityController)
        $shortKey = substr(strrchr($key, "\\"), 1); 
        if (!isset(self::$config[$category][$shortKey])) {
            throw new \Exception("Dépendance '$key' introuvable dans la catégorie '$category'");
        }
        $key = $shortKey;
    }

        $className = self::$config[$category][$key];

        // Si déjà instanciée, on la renvoie
        if (isset(self::$instances[$className])) {
            return self::$instances[$className];
        }

        // Si la classe n'existe pas
        if (!class_exists($className)) {
            throw new \Exception("Classe $className introuvable");
        }

        $reflector = new \ReflectionClass($className);
        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            $instance = new $className();
        } else {
            $params = $constructor->getParameters();
            $dependencies = [];

            foreach ($params as $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    // Recherche la dépendance dans toutes les catégories
                    $depClass = $type->getName();
                    $found = false;
                    foreach (self::$config as $cat => $deps) {
                        $depKey = array_search($depClass, $deps, true);
                        if ($depKey !== false) {
                            $dependencies[] = self::getDependency($cat, $depKey);
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $dependencies[] = null;
                    }
                } else {
                    if($className==='PDO'){
                        $dependencies =[DSN, USER,PASS];
                        break;
                    };
                    $dependencies[] = $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
                }
            }
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