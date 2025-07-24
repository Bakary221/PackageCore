<?php 

namespace App\core;

class Validator{

   private static array $errors = [];
   private static $instance = null;
   private static array $rules;

   public function __construct()
   {
       self::$errors = [];
       self::$rules = [
           "required" => function ($key, $value, $message = "Champ obligatoire") {
               if (empty($value)) {
                   self::addError($key, $message);
               }
           },
           "minLength" => function ($key, $value, $minLength, $message = "Trop court") {
               if (strlen($value) < $minLength) {
                   self::addError($key, $message);
               }
           },
           "isMail" => function ($key, $value, $message = "Email invalide") {
               if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                   self::addError($key, $message);
               }
           },
           "isPassword" => function ($key, $value, $message = "Mot de passe invalide") {
               if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $value)) {
                   self::addError($key, $message);
               }
           },
           "isSenegalPhone" => function ($key, $value, $message = "Numéro de téléphone invalide") {
               $value = preg_replace('/\D/', '', $value);
               $prefixes = ['70', '75', '76', '77', '78'];
               if (!(strlen($value) === 9 && in_array(substr($value, 0, 2), $prefixes))) {
                   self::addError($key, $message);
               }
           },
           "isCNI" => function ($key, $value, $message = "Numéro de CNI invalide") {
               $value = trim($value);
               $value = preg_replace('/\D/', '', $value);
               if (!preg_match('/^1\d{12}$/', $value)) {
                   self::addError($key, $message);
               }
           },
           "isNumeric" => function ($key, $value, $message = "Ce champ doit être numérique") {
               if (!is_numeric($value)) {
                   self::addError($key, $message);
               }
           },
       ];
   }

   public static function getInstance()
   {
       if (self::$instance === null) {
           self::$instance = new Validator();
       }
       return self::$instance;
   }

   public function validate(array $data, array $rules): bool
   {
       foreach ($rules as $field => $fieldRules) {
           $value = $data[$field] ?? null;

           foreach ($fieldRules as $rule) {
               if (is_string($rule)) {
                   $callback = self::$rules[$rule] ?? null;
                   if ($callback) {
                       $callback($field, $value);
                   }
               }
               elseif (is_array($rule)) {
                   $ruleName = $rule[0];
                   $params = array_slice($rule, 1);
                   $callback = self::$rules[$ruleName] ?? null;

                   if ($callback) {
                       $callback($field, $value, ...$params);
                   }
               }
           }
       }

       return empty(self::$errors);
   }

   public static function addError(string $field, string $message)
   {
       self::$errors[$field] = $message;
   }

   public static function getErrors()
   {
       return self::$errors;
   }

   public static function resetError(){
       self::$errors = [];
   }
}