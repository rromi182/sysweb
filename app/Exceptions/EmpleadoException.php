<?php

namespace App\Exceptions;

use Exception;

class EmpleadoException extends Exception
{
    public array $context;

    public function __construct(string $message, array $context = [])
    {
        parent::__construct($message);
        $this->context = $context;
    }

    public static function duplicateCode(string $code): self
    {
        return new self("El código de empleado '{$code}' ya existe en esta empresa.", ['code' => $code]);
    }

    public static function creationFailed(string $reason, array $context = []): self
    {
        return new self("Error al crear el empleado: {$reason}", $context);
    }

    public static function updateFailed(string $reason, array $context = []): self
    {
        return new self("Error al actualizar el empleado: {$reason}", $context);
    }

    public static function deletionFailed(string $reason, array $context = []): self
    {
        return new self("Error al eliminar el empleado: {$reason}", $context);
    }

    public static function invalidStatus(string $action, string $status, array $context = []): self
    {
        return new self("No se puede {$action} un empleado con estado '{$status}'.", $context);
    }

    public static function missingReference(string $field, array $context = []): self
    {
        return new self("Falta el campo obligatorio: {$field}.", $context);
    }
}