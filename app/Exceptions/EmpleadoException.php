<?php

namespace App\Exceptions;

use Exception;

class EmpleadoException extends Exception
{
    public static function duplicateCode(string $code): self
    {
        return new self("El código de empleado '{$code}' ya está registrado.");
    }

    public static function creationFailed(string $message, array $context = []): self
    {
        return new self("Error al crear empleado: {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        return new self("Error al actualizar empleado: {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        return new self("Error al eliminar empleado: {$message}");
    }
}