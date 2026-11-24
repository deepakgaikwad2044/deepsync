<?php

namespace App\Core;

use App\Models\User;

class Validator
{
  protected array $errors = [];
  protected array $data = [];

  public function validate(array $data, array $rules): array
  {
    $this->data = $data;
    $this->errors = [];

    foreach ($rules as $field => $ruleString) {

      $rulesArr = explode("|", $ruleString);

      foreach ($rulesArr as $rule) {

        $ruleName = explode(":", $rule)[0];

        // ---------------- REQUIRED ----------------
        if ($rule === "required") {
          if (!isset($data[$field]) || trim($data[$field]) === "") {
            $this->addError($field, ucfirst($field) . " is required");
          }
        }

        // ---------------- EMAIL ----------------
        if ($rule === "email" && !empty($data[$field])) {
          if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Invalid email address");
          }
        }

        // ---------------- LEN ----------------
        if (str_starts_with($rule, "len:")) {
          $min = (int) explode(":", $rule)[1];

          if (strlen($data[$field] ?? "") < $min) {
            $this->addError($field, ucfirst($field) . " must be at least {$min} characters");
          }
        }

        // ---------------- IMAGE ----------------
        if ($rule === "image") {
          $this->validateImage($field);
        }

        // ---------------- MAX (string or file) ----------------
   if (str_starts_with($rule, "max:")) {

  $value = explode(":", $rule)[1];
  $input = $data[$field] ?? null;

  // convert human readable size to bytes
  $limit = $this->convertToBytes($value);

  if (is_array($input) && isset($input["size"])) {

    if ($input["size"] > $limit) {
      $this->addError(
        $field,
        ucfirst($field) . " must be under " . $value
      );
    }
  } else {
    if (strlen($input ?? "") > $limit) {
      $this->addError(
        $field,
        ucfirst($field) . " must not exceed " . $value
      );
    }
  }
}

        // ---------------- MIMES ----------------
     if (str_starts_with($rule, "mimes:")) {
  $allowed = explode(",", explode(":", $rule)[1]);
  $allowed = array_map('strtolower', array_map('trim', $allowed));

  $file = $data[$field] ?? null;

  if ($file && isset($file["name"])) {

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
      $this->addError(
        $field,
        ucfirst($field) . " must be of type: " . implode(", ", $allowed)
      );
    }
  }
}

        // ---------------- UNIQUE ----------------
        if (str_starts_with($rule, "unique")) {
          $this->validateUnique($field, $rule);
        }

        // ---------------- EXISTS ----------------
        if (str_starts_with($rule, "exists")) {
          $this->validateExists($field, $rule);
        }

        // ---------------- CONFIRMED ----------------
        if ($rule === "confirmed") {
          if (
            !isset($data["confirm_password"]) ||
            ($data[$field] ?? null) !== ($data["confirm_password"] ?? null)
          ) {
            $this->addError($field, ucfirst($field) . " does not match confirmation");
          }
        }
      }
    }

    return [
      "status" => empty($this->errors),
      "errors" => $this->errors,
    ];
  }

  // ---------------- IMAGE VALIDATION ----------------
  protected function validateImage(string $field): void
  {
    $file = $this->data[$field] ?? null;

    if (!$file || !isset($file["error"]) || $file["error"] !== UPLOAD_ERR_OK) {
      $this->addError($field, ucfirst($field) . " upload failed");
      return;
    }

    $allowed = ["image/jpeg", "image/png", "image/jpg", "image/webp"];
    $mime = mime_content_type($file["tmp_name"]);

    if (!in_array($mime, $allowed)) {
      $this->addError($field, "Only JPG, PNG, WEBP allowed");
    }
  }
  
  
  
  protected function convertToBytes(string $value): int
{
  $value = trim($value);

  $unit = strtolower(substr($value, -2));
  $number = (int) $value;

  return match ($unit) {
    "kb" => $number * 1024,
    "mb" => $number * 1024 * 1024,
    "gb" => $number * 1024 * 1024 * 1024,
    default => (int) $value, // fallback raw bytes
  };
}

  // ---------------- UNIQUE ----------------
  protected function validateUnique(string $field, string $rule): void
  {
    $parts = explode(":", $rule);
    if (!isset($parts[1])) return;

    [$table, $column] = explode(",", $parts[1]);

    $modelClass = $this->getModelClassFromTable($table);
    if (!$modelClass) return;

    $exists = $modelClass::query()
      ->where($column, $this->data[$field] ?? null)
      ->first();

    if ($exists) {
      $this->addError($field, ucfirst($field) . " already exists");
    }
  }

  // ---------------- EXISTS ----------------
  protected function validateExists(string $field, string $rule): void
  {
    $parts = explode(":", $rule);
    if (!isset($parts[1])) return;

    [$table, $column] = explode(",", $parts[1]);

    $modelClass = $this->getModelClassFromTable($table);
    if (!$modelClass) return;

    $record = $modelClass::query()
      ->where($column, $this->data[$field] ?? null)
      ->first();

    if (!$record) {
      $this->addError($field, ucfirst($field) . " does not exist");
    }
  }

  // ---------------- MODEL MAP ----------------
  protected function getModelClassFromTable(string $table): ?string
  {
    $model = "App\\Models\\" . ucfirst(rtrim($table, "s"));

    return class_exists($model) ? $model : null;
  }

  // ---------------- ERROR ----------------
  protected function addError(string $field, string $message): void
  {
    if (!isset($this->errors[$field])) {
      $this->errors[$field] = $message;
    }
  }
}
