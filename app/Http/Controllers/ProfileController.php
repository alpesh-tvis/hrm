<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Employee;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }  
    public function index()
    {
        //
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
       $user_email = Auth::user()->email;
       
       $request->validate([
            'first_name' => 'required|string|max:40',
            'last_name' => 'required|string|max:40',
            'full_name' => 'required|string|max:40',
            'profile_image' => 'image|mimes:jpeg,png,jpg|max:100'
        ]);


        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            // $path = $request->file('profile_image')->store('profile_image', 'public');
            $image = $request->profile_image;
            $image_new_name = time() . '.' . $image->getClientOriginalExtension();
            $path = $request->profile_image->move('profile_image', $image_new_name);
            Employee::where('company_email',$user_email)->update(['profile_image' => $path]);
            
        }
        Employee::where('company_email',$user_email)->update($request->except(['_token', 'profile_image']));

        return redirect()->route('profile')->with('success','Profile Updated Successfully.');
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

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profile(){
        $user_email = Auth::user()->email;
        $get_user = Employee::where('company_email',$user_email)->first();
        $reporting_person = Employee::where('id',$get_user->reporting_person)->first();
        return view('profile.profile')->with(compact('get_user','reporting_person'));
    }
}
