<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use Config;
use Illuminate\Support\Facades\Http;

class IssuesController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $issuesQuery = Issue::with('project')
                ->with('category')
                ->with('status')
                ->with('tracker')
                ->where('is_deleted', 0)
                ->where('project_id',$request->get('project'));
                $request->get('status') ? $issuesQuery = $issuesQuery->where('status',$request->get('status')) : null;
                $request->get('tracker') ? $issuesQuery = $issuesQuery->where('tracker',$request->get('tracker')) : null;
                $request->get('subject') ? $issuesQuery = $issuesQuery->where('subject', 'like', '%'.$request->get('subject').'%') : null;
                $issues = $issuesQuery->paginate(10);
            } else {
                $issues = $this->redmineIssues($request);
            }
            return response()->json($issues);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function save(Request $request)
    {
        $dataSource = Config::get('app.data_source');
        try {
            if($dataSource == 'db') {

                $issue = new Issue();
                $issue->project_id = $request->project_id;
                $issue->tracker = $request->tracker;
                $issue->subject = $request->subject;
                $issue->description = $request->description;
                $issue->status = $request->status;
                $issue->category_id = $request->category;
                $issue->priority = $request->priority;
                $issue->is_deleted = 0;
                $issue->save();
                return response()->json(['status'=>'success', 'error'=>false, 'message'=>'Issue has been created successfuly.']);
            }
            else {
                return $this->createRedmineIssue($request);
            }
        } catch (Throwable $e) {
            report($e);     
            return false;
        }
    }

    public function destroy(Request $request)
    {
        $dataSource = Config::get('app.data_source');
        try {
            if($dataSource == 'db') {
                $issue = Issue::find($request->id);
                $issue->is_deleted = 1;
                $issue->save();
                return response()->json(['status'=>'success', 'error'=>false, 'message'=>'Issue has been deleted successfuly.']);
            } else {
                return $this->deleteRedmineIsuue($request);
            }
        } catch (Throwable $e) {
            report($e);     
            return false;
        }
    }

    public function redmineIssues($request)
    {
        if ($request->get('page')) {
            $pageno = $request->get('page');
        } else {
            $pageno = 1;
        }
        $limit = 10;
        $offset = ($pageno-1) * $limit;
        $to=($offset+($limit-1));
        $url=Config::get('app.redmine_url_with_auth').'/issues.json?limit='.$limit.'&offset='.$offset;
        if($request->project) {
            $url.='&project_id='.$request->project;
        }
        if($request->status) {
            $url.='&status_id='.$request->status;
        }
        if($request->tracker) {
            $url.='&tracker_id='.$request->tracker;
        }
        if($request->subject) {
            $url.='&subject='.'~'.$request->subject;
        }
        $response = Http::get($url);
        $totalPages = ceil($response['total_count'] / $limit);
        $currentPageUrl = Config::get('app.url').'/api/issues?page='.$pageno;
        $nextPageCalculated = $pageno+1;
        $nextPageUrl = null;
        if($nextPageCalculated<=$totalPages)
        {
            $nextPageUrl = Config::get('app.url').'/api/issues?page='.$nextPageCalculated;
        }
        $prevPageCalculated = $pageno-1;
        $prevPageUrl = null;
        if($prevPageCalculated>0)
        {
            $prevPageUrl = Config::get('app.url').'/api/issues?page='.$prevPageCalculated;
        }
        $links[0] = ['url'=>$prevPageUrl,'label'=>'« Previous','active'=>false];
        $links[1] = ['url'=>$currentPageUrl,'label'=>$pageno,'active'=>true];
        $links[2] = ['url'=>$nextPageUrl,'label'=>'Next »','active'=>false];
        $responseArray = [];
        $responseArray = [
            'current_page'=> (int)$pageno,
            'data'=>$response['issues'],
            'first_page_url'=>Config::get('app.url').'/api/issues?page=1',
            'from'=>$offset,
            'last_page'=>$totalPages,
            'last_page_url'=>Config::get('app.url').'/api/issues?page='.$totalPages,
            'links'=>$links,
            'next_page_url'=>$nextPageUrl,
            'path'=>Config::get('app.url').'/api/issues',
            'per_page'=>$limit,
            'prev_page_url'=>$prevPageUrl,
            'to'=>$to,
            'total'=>($response['total_count'])
        ];
        return $responseArray;
    }

    public function createRedmineIssue($request)
    {
        $url = Config::get('app.redmine_url_with_auth').'/issues.json';
        $params['issue'] = [
            'subject'=>$request->subject,
            'project_id'=>$request->project_id,
            'tracker_id'=>$request->tracker,
            'status_id'=>$request->status,
            'priority_id'=>$request->priority
        ];
        if(($request->category) && ($request->category != null))
        {
            $params['category_id']=$request->category;
        }
        if(($request->description) && ($request->description != null))
        {
            $params['description']=$request->description;
        }
        $response = Http::post(Config::get('app.redmine_url_with_auth').'/issues.json', $params);
        $resArray['status']=$response->status();
        $resArray['response'] = $response->json();
        $resArray['issue'] = $response['issue'];
        if(($response->status() >= 200) && (!empty($response['issue'])))
        {
            return response()->json(['status'=>'success', 'error'=>false, 'message'=>'Issue has been created successfuly.']);
        }
        else {
            abort(500, "Error in redmine API.");
        }
    }

    public function deleteRedmineIsuue($request)
    {
        $url = Config::get('app.redmine_url_with_auth').'/issues/'.$request->id.'.json';
        $response = Http::delete($url);
        if($response->status() >= 200) {
            return $response;
        } else{
            abort(500, "Error in redmine API.");
        }
    }
}
