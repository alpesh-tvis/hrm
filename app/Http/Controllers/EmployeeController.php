<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\WeekHour;
use Auth;
use Hash;
use Validator;
use DateTime;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
// use App\Events\Registered;
// use Illuminate\Foundation\Auth\RegistersUsers;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // use RegistersUsers;
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }    
    public function index()
    {

        $employee = Employee::get();
        $user = Auth::user();
        $user->hasVerifiedEmail();

        if($user->role == '2'){
            return view('admin.employee_list')->with('employee', $employee);
        }
        else
        {
            return view('admin.admin_main');
        }    

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $user = Auth::user();
        $user->hasVerifiedEmail();
        if($user->role == '2'){
            $emp_list = Employee::get();

        return view('admin.admin-dashbord')->with('emp_list', $emp_list);
        }
        else
        {
            return view('admin.admin_main');
        }
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:40',
            'company_email' => 'required|unique:employees',
            'full_name' => 'required|string|max:40',
            'password' => 'required|min:8'
        ]);

        // EID generate
        $employee = Employee::orderBy('eid', 'DESC')->first();
        $last_emp_id = $employee ? $employee->eid : 0;

        $code = str_pad($last_emp_id + 1, 3, "0", STR_PAD_LEFT);

        $data = $request->except('_token');
        $data['eid'] = $code;

        // 1. Employee create
        $employee = Employee::create($data);

        // 2. User create
        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->company_email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'total_hours'        => $request->total_working_hours,
            'min_full_day_hour'  => $request->min_full_day_hour,
            'min_half_day_hour'  => $request->min_half_day_hour,
            'max_carry_forward'  => $request->max_carry_forward,
        ]);


        event(new Registered($user));

        // 3. WeekHour create (only hourly employees)
        if ($request->role == 1 && $request->sift_type == 3) {

            $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
            $nextWeekEnd   = Carbon::now()->addWeek()->endOfWeek();

            WeekHour::create([
                'user_id' => $employee->id,
                'week_start_date' => $nextWeekStart,
                'week_end_date' => $nextWeekEnd,

                'total_hours' => $request->total_working_hours,
                'working_hours' => $request->total_working_hours,
                'remaining_hours' => 0,

            ]);
        }

        return redirect()->route('admin.create')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $emp = Employee::leftJoin('users', 'employees.company_email', '=', 'users.email')->find($id);
        $user = User::where('email', $emp->company_email)->first();
        $emp_list = Employee::get();
        $weekHour = WeekHour::where('user_id', $emp->id)
                        ->latest('id')
                        ->first();
        return view('admin.admin-dashbord')->with(compact('emp','emp_list','weekHour','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $request->validate([
            'first_name' => 'required|max:100',
            'full_name' => 'required|max:100',
            'upwork_password' => $request->upwork_profile == '1' ? 'required' : 'nullable',
            'upwork_username' => $request->upwork_profile == '1' ? 'required' : 'nullable',

        ]);

        // Find employee
        $employee = Employee::findOrFail($id);

        if($request->upwork_profile == '1'){
            $request->upwork_profile = '1';
        }else{
            $request->upwork_profile = '0';
        }
        
        $request->merge(['upwork_profile' => $request->upwork_profile == '1' ? '1' : '0']);
        
         $employee->update($request->except('_token', '_method'));

        // 3. Update user (linked via email)
        $user = User::where('email', $employee->company_email)->first();

        if ($user) {
            $user->update([
                'name' => $request->full_name,
                'email' => $request->company_email,
                'role' => $request->role,
                'total_hours'        => $request->total_working_hours,
                'min_full_day_hour'  => $request->min_full_day_hour,
                'min_half_day_hour'  => $request->min_half_day_hour,
                'max_carry_forward'  => $request->max_carry_forward,
            ]);
        }

        // 4. WeekHour update (next week se apply hoga)
        if ($request->role == 1 && $request->sift_type == 3) {

            $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
            $nextWeekEnd   = Carbon::now()->addWeek()->endOfWeek();

            WeekHour::update(
                [
                    'user_id' => $employee->id,
                    'week_start_date' => $nextWeekStart,
                ],
                [
                    'week_end_date' => $nextWeekEnd,

                    'total_hours' => $request->total_working_hours,
                    
                ]
            );
        }

        return redirect()->route('admin.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $emp = Employee::find($id);
        $user = User::where('email',$emp->company_email)->first();
        
        $emp->delete();
        $user->delete();
        return redirect()->route('admin.index')->with('success','Employee Delete successfully.');
    }
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

    public function showChangePasswordGet() {
        return view('auth.passwords.change-password');
    }

    public function changePasswordPost(Request $request) {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            
            return redirect()->back()->with("error","Your current password does not matches with the password.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            
            return redirect()->back()->with("error","New Password cannot be same as your current password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();

        return redirect()->back()->with("success","Password successfully changed!");
    }
    public function leave_add(){
        return view('leave.leave');
    }
    public function leave_post(Request $request){
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'leave_type' => 'required',
            'reason' => 'required',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $datetime1 = new DateTime($start_date);
        $datetime2 = new DateTime($end_date);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');
        // dd($days+1);
    }

    public function admin_upchange()
    {
        $users = User::where('role','1')->select('email')->get();
        // dd($users);
        return view('employee.users_change-password')->with('users',$users);
    }

    public function admin_upchange_post(Request $request){

        $validatedData = $request->validate([
            'user' => 'required',
            'new_password' => 'required|string|min:8|same:confirm_password',
        ]);

        
        #Update the new Password
        User::where('email',$request->user)->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return redirect()->back()->with("success","Password successfully changed!");
    }
}
