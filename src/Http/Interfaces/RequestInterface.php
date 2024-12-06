<?php
namespace App\Http\Interfaces;

interface RequestInterface
{
    public function getMethod(): string;
    public function getUri(): string;
    public function getHeaders(): array;
    public function getParams(): array;
}