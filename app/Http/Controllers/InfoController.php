<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class InfoController extends Controller
{
    public function __invoke(): string
    {
        return 'Dienst Testen API';
    }
}
