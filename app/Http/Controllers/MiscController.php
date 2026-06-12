<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Contracts\Support\Renderable;
use IntlDateFormatter;

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

    public function impressum(): Renderable
    {
        return view('misc.impressum');
    }

    public function tec(): Renderable
    {
        return view('misc.tec', [
            'lastUpdate' => str(new IntlDateFormatter('it_IT', pattern: 'd MMMM YYYY')->format(new DateTimeImmutable('2026-06-01')))->title(),
        ]);
    }
}
