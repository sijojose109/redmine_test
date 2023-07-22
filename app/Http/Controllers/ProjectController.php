<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ProjectController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $projectsQuery = Project::where('status', 1);
                if($request->name && $request->name !== '') {
                    $projectsQuery->where('name','like','%'.$request->name.'%'); 
                }
                $projects=$projectsQuery->paginate(10);
            }
            else
            {
                $projects = $this->redmineProjects($request);
            }
            return response()->json($projects);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function redmineProjects($request)
    {
        if ($request->get('page')) {
            $pageno = $request->get('page');
        } else {
            $pageno = 1;
        }

        $limit = 10;
        $offset = ($pageno-1) * $limit;
        $to=($offset+($limit-1));
        $url=Config::get('app.redmine_url_with_auth').'/projects.json?limit='.$limit.'&offset='.$offset;
        if($request->name && $request->name !== '') {
            $url.='&name=~'.$request->name;
        }
        $response = Http::get($url);
        $totalPages = ceil($response['total_count'] / $limit);
        $currentPageUrl = Config::get('app.url').'/api/projects?page='.$pageno;
        $nextPageCalculated = $pageno+1;
        $nextPageUrl = null;
        if($nextPageCalculated<=$totalPages)
        {
            $nextPageUrl = Config::get('app.url').'/api/projects?page='.$nextPageCalculated;
        }
        $prevPageCalculated = $pageno-1;
        $prevPageUrl = null;
        if($prevPageCalculated>0)
        {
            $prevPageUrl = Config::get('app.url').'/api/projects?page='.$prevPageCalculated;
        }
        $links[0] = ['url'=>$prevPageUrl,'label'=>'« Previous','active'=>false];
        $links[1] = ['url'=>$currentPageUrl,'label'=>$pageno,'active'=>true];
        $links[2] = ['url'=>$nextPageUrl,'label'=>'Next »','active'=>false];

        $responseArray = [];
        $responseArray = [
            'current_page'=> (int)$pageno,
            'data'=>$response['projects'],
            'first_page_url'=>Config::get('app.url').'/api/projects?page=1',
            'from'=>$offset,
            'last_page'=>$totalPages,
            'last_page_url'=>Config::get('app.url').'/api/projects?page='.$totalPages,
            'links'=>$links,
            'next_page_url'=>$nextPageUrl,
            'path'=>Config::get('app.url').'/api/projects',
            'per_page'=>$limit,
            'prev_page_url'=>$prevPageUrl,
            'to'=>$to,
            'total'=>($response['total_count'])
        ];
        return $responseArray;
    }

    public function redmineSingleProject($id)
    {
        $url=Config::get('app.redmine_url_with_auth').'/projects.json?id='.$id;
        return Http::get($url);
    }

    public function show($id)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $projects = Project::find($id);
                return response()->json($projects);
            }
            else
            {
                $projectsRedmine = $this->redmineSingleProject($id);
                $projects = [];
                if($projectsRedmine['projects'][0]) {
                    $projects = $projectsRedmine['projects'][0];
                }
            }
            return response()->json($projects);
        } catch (Throwable $e) {
            return false;
        }
    }
}
