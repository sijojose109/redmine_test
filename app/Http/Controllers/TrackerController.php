<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tracker;
use Config;
use Illuminate\Support\Facades\Http;

class TrackerController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $trackerAll = Tracker::with('default_status')->get();
                $tracker['trackers'] = $trackerAll;
            }
            else
            {
                $tracker = $this->redmineTrackers($request);
            }
            return response()->json($tracker);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function redmineTrackers(Request $request)
    {
        $url=Config::get('app.redmine_url_with_auth').'/trackers.json';
        $response = Http::get($url);
        return $response->json();
    }

}
