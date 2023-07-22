<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Config;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $dataSource = Config::get('app.data_source');
            if($dataSource == 'db') {
                $projectCategory = Category::select('id', 'name')->where('project_id', $request->project)->where('status',1)->get();
                $category['issue_categories'] = $projectCategory;
            }
            else
            {
                $category = $this->redmineProjectCategories($request);
            }
            return response()->json($category);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function redmineProjectCategories($request)
    {
        $url=Config::get('app.redmine_url_with_auth').'/projects/'.$request->project.'/issue_categories.json';
        $response = Http::get($url);
        return $response->json();
    }
}
