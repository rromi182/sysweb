<?php

namespace App\Exceptions;

use Exception;

class EmpresaException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        return new self("Error al crear la empresa: {$message}", 500);
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        return new self("Error al actualizar la empresa: {$message}", 500);
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        return new self("Error al eliminar la empresa: {$message}", 500);
    }

    public static function notFound(string $message = 'Empresa no encontrada'): self
    {
        return new self($message, 404);
    }

    public static function duplicateRuc(string $ruc): self
    {
        return new self("Ya existe una empresa con el RUC: {$ruc}", 422);
    }

    public static function duplicateName(string $nombreEmpresa): self
    {
        return new self("Ya existe una empresa con el nombre: {$nombreEmpresa}", 422);
    }

    public static function duplicateRazonScoail(string $razonSocial): self
    {
        return new self("Ya existe una empresa con la razon social: {$razonSocial}", 422);
    }
}