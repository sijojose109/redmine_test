<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Status;

class StatusController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $statuses_db = Status::where('status', 1)->get();
                $statuses = ['issue_statuses'=>$statuses_db];
            }
            else
            {
                $statuses = $this->redmineStatuses($request);
            }
            return response()->json($statuses);
        } catch (Throwable $e) {
            return false;
        }
    }


    public function redmineStatuses($request)
    {
        $url=Config::get('app.redmine_url_with_auth').'/issue_statuses.json';
        $response = Http::get($url);
        return $response->json();
    }

}
