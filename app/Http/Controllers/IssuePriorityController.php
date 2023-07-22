<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IssuePriority;
use Config;
use Illuminate\Support\Facades\Http;

class IssuePriorityController extends Controller
{
    //
    public function index()
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $issue_priorities['issue_priorities'] = IssuePriority::where('active', 1)->get();
                return response()->json($issue_priorities);
            } else {
                return $issue_priorities = $this->issuePrioritiesRedmine();
            }
        } catch (Throwable $e) {
            return false;
        }
    }

    public function issuePrioritiesRedmine() {
        $url=Config::get('app.redmine_url_with_auth').'/enumerations/issue_priorities.json';
        $response = Http::get($url);
        return $response->json();
    }
}
