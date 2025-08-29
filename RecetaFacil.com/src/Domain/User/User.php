<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private int $id;
    private string $nombreUsuario;
    private string $email;
    private string $contrasena;
    private string $fechaCreacion;

    public function __construct(?int $id, string $nombreUsuario, string $email, string $contrasena, string $fechaCreacion)
    {
        $this->id = $id;
        $this->nombreUsuario = strtolower($nombreUsuario);
        $this->contrasena = $contrasena;
        $this->email = $email;
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getnombreUsuario(): string
    {
        return $this->nombreUsuario;
    }

    public function getContrasena(): string
    {
        return $this->contrasena;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nombreUsuario' => $this->nombreUsuario,
            'contrasena' => $this->contrasena,
            'email' => $this->email,
            'fechaCreacion'=> $this->fechaCreacion
        ];
    }
}
