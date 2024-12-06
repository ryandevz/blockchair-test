<?php
namespace App\Http\Interfaces;

interface ResponseInterface
{
    public function withStatus(int $code): self;
    public function withHeader(string $name, string $value): self;
    public function withBody(mixed $data): self;
    public function send(): void;
}