<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ShiftSettings;
use Illuminate\Http\Request;
use Auth;

class ShiftSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);

        $this->middleware(function ($request, $next) {

            if (auth()->user()->role != 2) {
                return redirect()->route('dashboard.index');
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $user = Auth::user();

        $get_sales_persons = Employee::where('department', '1')
            ->where('sift_type', '2')
            ->where('service_enddate', null)
            ->orderBy('first_name', 'asc')
            ->get();

        // Fetch all shifts for the listed employees and group them by employee_name
        $shifts = ShiftSettings::whereIn('employee_name', $get_sales_persons->pluck('id'))->get()->groupBy('employee_name');

        if($user->role == '2'){
            return view('shift-settings.shift_settings', compact('get_sales_persons', 'shifts'));
        }
        else
        {
            return redirect()->route('dashboard.index');
        }    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();

        // Loop through each shift
        foreach (['shift1', 'shift2'] as $shiftKey) {
            $shiftData = $data[$shiftKey];

            // Prepare the update data
            $updateData = [];
            foreach ($shiftData as $day => $time) {
                $updateData[$day] = $time;
            }

            // Update or create the record
            ShiftSettings::updateOrCreate(
                [
                    'shift' => $shiftData['shift'],
                    'employee_name' => $data['employee_id']
                ],
                $updateData
            );
        }
        return redirect()->route('shift-settings.index')->with('message','Record Update Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
