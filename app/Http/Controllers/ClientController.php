<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Auth;

class ClientController extends Controller
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
        $cli = Client::where('company','!=','')->get();
        $user = Auth::user();
        
        if($user->role == 2 || $user->id == 8){
            return view('admin.client_list')->with('cli', $cli);
        }
        else
        {
            return view('admin.admin_main');
        }   
        // return view('admin.client_list')->with('cli', $cli);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.client_add_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'first_name' => 'required|string|max:40',
        //     'last_name' => 'required|string|max:40',
        //     'company' => 'required|max:50',
        //     'b_name' => 'required|max:50',
        //     'billing_address' => 'required'
        // ]);
        $validator = $request->validate([
            'first_name' => 'required|string|max:40',
            'last_name' => 'required|string|max:40',
            'company' => 'required',
            'b_name' => 'required|max:50',
            'billing_address' => 'required'
        ], 
        [], 
        [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company' => 'Billing Company Name',
            'b_name' => 'Billing Name',
            'billing_address' => 'Billing Address'
            //Customize Attributes Name
        ]);
        

        Client::create($request->except('_token'));
        return redirect()->route('client.index')->with('success','Client created successfully.');
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
        $clis = Client::find($id);
        return view('admin.client_add_edit')->with('clis',$clis);
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
        $client = Client::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:40',
            'last_name' => 'required|string|max:40',
            'company' => 'required',
            'b_name' => 'required|max:50',
            'billing_address' => 'required'
        ]);
        
        $client->update($request->all());
        return redirect()->route('client.index')->with('success','Client Update successfully.');
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

        $emp = Client::find($id);
        $emp->delete();
        return redirect()->route('client.index')->with('success','Client Delete successfully.');
    }
}
