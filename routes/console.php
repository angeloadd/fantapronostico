<?php

declare(strict_types=1);

use App\Models\Game;
use App\Models\Tournament;

/*
 * FIFA World Cup 2026 — full UTC schedule (times converted from GMT+2, 104 matches)
 * Dead window (no matches): ~07:00–15:00 UTC daily
 *
 * #    Date (UTC)       Time UTC  Teams                              Stage
 * ---------------------------------------------------------------------------
 * GROUP STAGE
 *   1  Thu Jun 11 2026  19:00     Mexico vs South Africa             Group A
 *   2  Fri Jun 12 2026  02:00     South Korea vs Czechia             Group A
 *   3  Fri Jun 12 2026  19:00     Canada vs Bosnia & Herzegovina     Group B
 *   4  Sat Jun 13 2026  01:00     United States vs Paraguay          Group D
 *   5  Sat Jun 13 2026  19:00     Qatar vs Switzerland               Group B
 *   6  Sat Jun 13 2026  22:00     Brazil vs Morocco                  Group C
 *   7  Sun Jun 14 2026  01:00     Haiti vs Scotland                  Group C
 *   8  Sun Jun 14 2026  16:00     Australia vs Türkiye               Group D
 *   9  Sun Jun 14 2026  17:00     Germany vs Curaçao                 Group E
 *  10  Sun Jun 14 2026  20:00     Netherlands vs Japan               Group F
 *  11  Sun Jun 14 2026  23:00     Ivory Coast vs Ecuador             Group E
 *  12  Mon Jun 15 2026  02:00     Sweden vs Tunisia                  Group F
 *  13  Mon Jun 15 2026  16:00     Spain vs Cape Verde                Group H
 *  14  Mon Jun 15 2026  19:00     Belgium vs Egypt                   Group G
 *  15  Mon Jun 15 2026  22:00     Saudi Arabia vs Uruguay            Group H
 *  16  Tue Jun 16 2026  01:00     Iran vs New Zealand                Group G
 *  17  Tue Jun 16 2026  19:00     France vs Senegal                  Group I
 *  18  Tue Jun 16 2026  22:00     Iraq vs Norway                     Group I
 *  19  Wed Jun 17 2026  01:00     Argentina vs Algeria               Group J
 *  20  Wed Jun 17 2026  04:00     Austria vs Jordan                  Group J
 *  21  Wed Jun 17 2026  17:00     Portugal vs DR Congo               Group K
 *  22  Wed Jun 17 2026  20:00     England vs Croatia                 Group L
 *  23  Wed Jun 17 2026  23:00     Ghana vs Panama                    Group L
 *  24  Thu Jun 18 2026  02:00     Uzbekistan vs Colombia             Group K
 *  25  Thu Jun 18 2026  16:00     Czechia vs South Africa            Group A
 *  26  Thu Jun 18 2026  19:00     Switzerland vs Bosnia & Herzegovina Group B
 *  27  Thu Jun 18 2026  22:00     Canada vs Qatar                    Group B
 *  28  Fri Jun 19 2026  01:00     Mexico vs South Korea              Group A
 *  29  Fri Jun 19 2026  19:00     United States vs Australia         Group D
 *  30  Fri Jun 19 2026  22:00     Scotland vs Morocco                Group C
 *  31  Sat Jun 20 2026  00:30     Brazil vs Haiti                    Group C
 *  32  Sat Jun 20 2026  03:00     Türkiye vs Paraguay                Group D
 *  33  Sat Jun 20 2026  17:00     Netherlands vs Sweden              Group F
 *  34  Sat Jun 20 2026  20:00     Germany vs Ivory Coast             Group E
 *  35  Sun Jun 21 2026  00:00     Ecuador vs Curaçao                 Group E
 *  36  Sun Jun 21 2026  04:00     Tunisia vs Japan                   Group F
 *  37  Sun Jun 21 2026  16:00     Spain vs Saudi Arabia              Group H
 *  38  Sun Jun 21 2026  19:00     Belgium vs Iran                    Group G
 *  39  Sun Jun 21 2026  22:00     Uruguay vs Cape Verde              Group H
 *  40  Mon Jun 22 2026  01:00     New Zealand vs Egypt               Group G
 *  41  Mon Jun 22 2026  17:00     Argentina vs Austria               Group J
 *  42  Mon Jun 22 2026  21:00     France vs Iraq                     Group I
 *  43  Tue Jun 23 2026  00:00     Norway vs Senegal                  Group I
 *  44  Tue Jun 23 2026  03:00     Jordan vs Algeria                  Group J
 *  45  Tue Jun 23 2026  17:00     Portugal vs Uzbekistan             Group K
 *  46  Tue Jun 23 2026  20:00     England vs Ghana                   Group L
 *  47  Tue Jun 23 2026  23:00     Panama vs Croatia                  Group L
 *  48  Wed Jun 24 2026  02:00     Colombia vs DR Congo               Group K
 *  49  Wed Jun 24 2026  19:00     Switzerland vs Canada              Group B
 *  50  Wed Jun 24 2026  19:00     Bosnia & Herzegovina vs Qatar      Group B
 *  51  Wed Jun 24 2026  22:00     Scotland vs Brazil                 Group C
 *  52  Wed Jun 24 2026  22:00     Morocco vs Haiti                   Group C
 *  53  Thu Jun 25 2026  01:00     Czechia vs Mexico                  Group A
 *  54  Thu Jun 25 2026  01:00     South Africa vs South Korea        Group A
 *  55  Thu Jun 25 2026  20:00     Curaçao vs Ivory Coast             Group E
 *  56  Thu Jun 25 2026  20:00     Ecuador vs Germany                 Group E
 *  57  Thu Jun 25 2026  23:00     Japan vs Sweden                    Group F
 *  58  Thu Jun 25 2026  23:00     Tunisia vs Netherlands             Group F
 *  59  Fri Jun 26 2026  02:00     Türkiye vs United States           Group D
 *  60  Fri Jun 26 2026  02:00     Paraguay vs Australia              Group D
 *  61  Fri Jun 26 2026  19:00     Norway vs France                   Group I
 *  62  Fri Jun 26 2026  19:00     Senegal vs Iraq                    Group I
 *  63  Sat Jun 27 2026  00:00     Cape Verde vs Saudi Arabia         Group H
 *  64  Sat Jun 27 2026  00:00     Uruguay vs Spain                   Group H
 *  65  Sat Jun 27 2026  03:00     Egypt vs Iran                      Group G
 *  66  Sat Jun 27 2026  03:00     New Zealand vs Belgium             Group G
 *  67  Sat Jun 27 2026  21:00     Panama vs England                  Group L
 *  68  Sat Jun 27 2026  21:00     Croatia vs Ghana                   Group L
 *  69  Sat Jun 27 2026  23:30     Colombia vs Portugal               Group K
 *  70  Sat Jun 27 2026  23:30     DR Congo vs Uzbekistan             Group K
 *  71  Sun Jun 28 2026  04:00     Algeria vs Austria                 Group J
 *  72  Sun Jun 28 2026  04:00     Jordan vs Argentina                Group J
 * ---------------------------------------------------------------------------
 * ROUND OF 32
 *  73  Sun Jun 28 2026  19:00     TBD vs TBD                         R32
 *  74  Mon Jun 29 2026  17:00     TBD vs TBD                         R32
 *  75  Mon Jun 29 2026  20:30     TBD vs TBD                         R32
 *  76  Tue Jun 30 2026  01:00     TBD vs TBD                         R32
 *  77  Tue Jun 30 2026  17:00     TBD vs TBD                         R32
 *  78  Tue Jun 30 2026  21:00     TBD vs TBD                         R32
 *  79  Wed Jul 01 2026  01:00     TBD vs TBD                         R32
 *  80  Wed Jul 01 2026  16:00     TBD vs TBD                         R32
 *  81  Wed Jul 01 2026  20:00     TBD vs TBD                         R32
 *  82  Thu Jul 02 2026  00:00     TBD vs TBD                         R32
 *  83  Thu Jul 02 2026  19:00     TBD vs TBD                         R32
 *  84  Thu Jul 02 2026  23:00     TBD vs TBD                         R32
 *  85  Fri Jul 03 2026  03:00     TBD vs TBD                         R32
 *  86  Fri Jul 03 2026  18:00     TBD vs TBD                         R32
 *  87  Fri Jul 03 2026  22:00     TBD vs TBD                         R32
 *  88  Sat Jul 04 2026  01:30     TBD vs TBD                         R32
 * ---------------------------------------------------------------------------
 * ROUND OF 16
 *  89  Sat Jul 04 2026  17:00     TBD vs TBD                         R16
 *  90  Sat Jul 04 2026  21:00     TBD vs TBD                         R16
 *  91  Sun Jul 05 2026  20:00     TBD vs TBD                         R16
 *  92  Mon Jul 06 2026  00:00     TBD vs TBD                         R16
 *  93  Mon Jul 06 2026  19:00     TBD vs TBD                         R16
 *  94  Tue Jul 07 2026  00:00     TBD vs TBD                         R16
 *  95  Tue Jul 07 2026  16:00     TBD vs TBD                         R16
 *  96  Tue Jul 07 2026  20:00     TBD vs TBD                         R16
 * ---------------------------------------------------------------------------
 * QUARTER-FINALS
 *  97  Thu Jul 09 2026  20:00     TBD vs TBD                         QF
 *  98  Fri Jul 10 2026  19:00     TBD vs TBD                         QF
 *  99  Sat Jul 11 2026  21:00     TBD vs TBD                         QF
 * 100  Sun Jul 12 2026  01:00     TBD vs TBD                         QF
 * ---------------------------------------------------------------------------
 * SEMI-FINALS
 * 101  Tue Jul 14 2026  19:00     TBD vs TBD                         SF
 * 102  Wed Jul 15 2026  19:00     TBD vs TBD                         SF
 * ---------------------------------------------------------------------------
 * 103  Sat Jul 18 2026  21:00     TBD vs TBD                         3rd place
 * 104  Sun Jul 19 2026  19:00     TBD vs TBD                         FINAL
 */

$doScheduleBeforeTournamentIsFinished = static fn () => Tournament::first()
    ?->final_started_at
    ?->avoidMutation()
    ?->addDay()
    ?->isFuture();

// Daily maintenance runs in the dead window (07:00–15:00 UTC) when no matches are scheduled
Schedule::command('fp:tournament:add')
    ->dailyAt('08:00');

Schedule::command('fp:teams:get')
    ->dailyAt('08:10')
    ->when($doScheduleBeforeTournamentIsFinished);
Schedule::command('fp:games:get')
    ->dailyAt('08:15')
    ->when($doScheduleBeforeTournamentIsFinished);
Schedule::command('fp:players:get')
    ->dailyAt('08:20')
    ->when($doScheduleBeforeTournamentIsFinished);

Schedule::command('fp:games:set-ongoing')
    ->everyFifteenMinutes()
    ->when($doScheduleBeforeTournamentIsFinished);

Schedule::command('fp:games:goals:get')
    ->hourlyAt('5')
    ->when($doScheduleBeforeTournamentIsFinished);

Schedule::command('fp:topscorers:get')
    ->hourlyAt('10')
    ->when(
        static fn () => Tournament::first()?->final_started_at->isPast() && Tournament::first()
            ->final_started_at
            ->avoidMutation()
            ->addHours(6)
            ->isFuture() &&
            Game::all()->every('status', '=', 'finished')
    );
Schedule::command('fp:winner:get')
    ->everyTenMinutes()
    ->when(
        static fn () => Tournament::first()?->final_started_at->isPast() && Tournament::first()
            ->final_started_at
            ->avoidMutation()
            ->addHours(6)
            ->isFuture() &&
            Game::all()->every('status', '=', 'finished')
    );

Schedule::command('fp:bot:telegram')
    ->everyThirtyMinutes();
