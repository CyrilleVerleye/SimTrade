<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use App\Models\Profile;
use App\Models\Trade;

use App\Services\APIFetch;

class DashboardController extends Controller
{
    protected $apiFetch;

    public function __construct(APIFetch $apiFetch)
    {
        $this->apiFetch = $apiFetch;
    }


    public function index()
    {
        $user_id = Auth::id();
        $wallet = (float) (Profile::getWallet($user_id));
        $openTrades = Trade::getOpenTrades($user_id);

        $totalAssets =  0;
        foreach ($openTrades as $openTrade) {
            $totalAssets += $openTrade->quantity * $this->apiFetch->getSpecificClosePrice($openTrade->symbol);
        }

        return Inertia::render('Dashboard', ['barsData' => fn () => $this->apiFetch->getHistoricalBars(), 'barsWeekData' => fn () => $this->apiFetch->getHistoricalBarsWeek(), 'barsDayData' => fn () => $this->apiFetch->getHistoricalBarsDay(), 'barsLatestData' => fn () => $this->apiFetch->getLastestBars(), 'wallet' => $wallet, 'openTrades' => $openTrades, 'totalAssets' => $totalAssets]);
    }
}
