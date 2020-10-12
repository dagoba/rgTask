<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;

use App\Project;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::owned()
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return view('home', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);

        $project = Project::create([
            'title' => request('title'),
            'user_id' => auth()->id(),
        ]);

        return response($project->load('tasks'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);

        $project->title = request('title');
        $project->tasks_order = request('tasks_order') ?: $project->tasks_order;
        $project->save();

        return response($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response(null, 204);
    }
}
