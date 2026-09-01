<?php

namespace App\Http\Controllers;

use App\Models\UpworkTimer;
use App\Models\Project;
use Illuminate\Http\Request;
use Auth;

class UpworkTimerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();
        $projects = Project::select("projects.*")->whereNull("end_date")->whereRaw("find_in_set('".$user_id."',projects.employee_id)")->orderBy('project_name','asc')->get();

        
        return view('UpworkTimer.timer')->with(compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UpworkTimer  $upworkTimer
     * @return \Illuminate\Http\Response
     */
    public function show(UpworkTimer $upworkTimer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UpworkTimer  $upworkTimer
     * @return \Illuminate\Http\Response
     */
    public function edit(UpworkTimer $upworkTimer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UpworkTimer  $upworkTimer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UpworkTimer $upworkTimer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UpworkTimer  $upworkTimer
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpworkTimer $upworkTimer)
    {
        //
    }
}
