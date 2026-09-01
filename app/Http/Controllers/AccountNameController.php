<?php

namespace App\Http\Controllers;

use App\Models\AccountName;
use Illuminate\Http\Request;
use Auth;

class AccountNameController extends Controller
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
        $account_name = AccountName::get();
        $user = Auth::user();
        
        if($user->role == '2'){
            return view('accountname.accountname_list')->with(compact('account_name'));
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
        return view('accountname.accountname_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'accountname' => 'required|unique:account_names'
        ]);

        AccountName::create($request->all());
        return redirect()->route('accountname.index')->with('success','Account Name created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function show(AccountName $accountName)
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
        $account_name = AccountName::find($id);
        return view('accountname.accountname_form')->with(compact('account_name'));
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
        $account_name = AccountName::findOrFail($id);
        $request->validate([
            'accountname' => 'required|unique:account_names,accountname,'.$id,
        ]);
        
        $account_name->update($request->all());
        return redirect()->route('accountname.index')->with('success','Account Name Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountName  $accountName
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountName $accountName)
    {
        //
    }
}
