<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

final class MiscController extends Controller
{
    public function albo(): Renderable
    {
        return view('misc.albo');
    }

    public function terms(): Renderable
    {
        return view('misc.terms');
    }
}
