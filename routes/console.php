<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\Tournament;

$doScheduleBeforeTournamentIsFinished = static fn () => Tournament::first()
    ?->final_started_at
    ?->addHours(6)
    ?->isFuture();

// Schedule::command('fp:teams:get')
//    ->dailyAt('04:10')
//    ->when($doScheduleBeforeTournamentIsFinished);
// Schedule::command('fp:games:get')
//    ->dailyAt('04:15')
//    ->when($doScheduleBeforeTournamentIsFinished);
// Schedule::command('fp:players:get')
//    ->dailyAt('04:20')
//    ->when($doScheduleBeforeTournamentIsFinished);
//
// Schedule::command('fp:games:set-ongoing')
//    ->hourlyAt('5')
//    ->when($doScheduleBeforeTournamentIsFinished);
//
// Schedule::command('fp:games:goals:get')
//    ->hourly()
//    ->when($doScheduleBeforeTournamentIsFinished);
//
// Schedule::command('fp:topscorers:get')
//    ->hourlyAt('10')
//    ->when(
//        static fn () => Tournament::first()?->final_started_at->isPast() && Tournament::first()
//            ->final_started_at
//            ->addHours(6)
//            ->isFuture() &&
//            Game::all()->every('status', '=', 'finished')
//    );
// Schedule::command('fp:fetch:champions')
//    ->everyTenMinutes()
//    ->when(
//        static fn () => Tournament::first()?->final_started_at->isPast() && Tournament::first()
//            ->final_started_at
//            ->addHours(6)
//            ->isFuture() &&
//            Game::all()->every('status', '=', 'finished')
//    );

// Schedule::command('fp:bot:telegram')
//    ->everyThirtyMinutes();
