<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Ranking\RankingCalculatorInterface;
use Illuminate\Contracts\Support\Renderable;

final class MiscController extends Controller
{
    public function __construct(private readonly RankingCalculatorInterface $calculator) {}

    public function albo(): Renderable
    {
        return view('misc.albo');
    }

    public function terms(): Renderable
    {
        return view('misc.terms');
    }
}
