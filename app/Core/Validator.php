<?php

namespace App\Core;

use App\Models\User;

class Validator
{
  protected array $errors = [];
  protected array $data = [];

  /**
   * Main validate method
   */
  public function validate(array $data, array $rules): array
  {
    $this->data = $data;
    $this->errors = [];

    foreach ($rules as $field => $ruleString) {
      $rulesArr = explode("|", $ruleString);

      foreach ($rulesArr as $rule) {
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

        // ---------------- MIN LENGTH ----------------
        if (str_starts_with($rule, "len:")) {
          $min = (int) explode(":", $rule)[1];
          if (strlen($data[$field] ?? "") < $min) {
            $this->addError(
              $field,
              ucfirst($field) . " must be at least {$min} characters"
            );
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
          $confirmField = $field . "_confirmation";

          // OR if you want confirm_password instead:
          if (
            !isset($data["confirm_password"]) ||
            ($data[$field] ?? null) !== ($data["confirm_password"] ?? null)
          ) {
            $this->addError(
              $field,
              ucfirst($field) . " does not match confirmation"
            );
          }
        }
      }
    }

    return [
      "status" => empty($this->errors),
      "errors" => $this->errors,
    ];
  }

  /**
   * UNIQUE validation (ORM based)
   * Rule: unique:users,email
   */
  protected function validateUnique(string $field, string $rule): void
  {
    $parts = explode(":", $rule);
    if (!isset($parts[1])) {
      return;
    }

    [$table, $column] = explode(",", $parts[1]);

    $modelClass = $this->getModelClassFromTable($table);
    if (!$modelClass) {
      return;
    }

    $exists = $modelClass
      ::query()
      ->where($column, $this->data[$field] ?? null)
      ->first();

    if ($exists) {
      $this->addError($field, ucfirst($field) . " already exists");
    }
  }

  /**
   * EXISTS validation (ORM based)
   * Rule: exists:users,email
   */
  protected function validateExists(string $field, string $rule): void
  {
    $parts = explode(":", $rule);
    if (!isset($parts[1])) {
      return;
    }

    [$table, $column] = explode(",", $parts[1]);

    $modelClass = $this->getModelClassFromTable($table);
    if (!$modelClass) {
      return;
    }

    $record = $modelClass
      ::query()
      ->where($column, $this->data[$field] ?? null)
      ->first();

    if (!$record) {
      $this->addError($field, ucfirst($field) . " does not exist");
    }
  }

  /**
   * Map table → Model
   */
  protected function getModelClassFromTable(string $table): ?string
  {
    // users -> User, voters -> Voter
    $model = "App\\Models\\" . ucfirst(rtrim($table, "s"));

    if (class_exists($model)) {
      return $model;
    }

    return null;
  }

  /**
   * Add error (one per field)
   */
  protected function addError(string $field, string $message): void
  {
    if (!isset($this->errors[$field])) {
      $this->errors[$field] = $message;
    }
  }
}
