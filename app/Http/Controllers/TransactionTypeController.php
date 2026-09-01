<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionType;
use Auth;
class TransactionTypeController extends Controller
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
        $TransactionType = TransactionType::get();
        $user = Auth::user();
        
        if($user->role == '2'){
            return view('transaction-type.transaction_type_list')->with('TransactionType', $TransactionType);
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
        return view('transaction-type.transaction_type');
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
            'type' => 'required|unique:transaction_types',
        ]);

        TransactionType::create($request->all());
        return redirect()->route('transaction-type.index')->with('success','Transaction Type created successfully.');
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
        $TransactionType = TransactionType::find($id);
        return view('transaction-type.transaction_type')->with('TransactionType',$TransactionType);
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
        $TransactionType = TransactionType::findOrFail($id);
        $request->validate([
            'type' => 'required|unique:transaction_types,type,'.$id,
        ]);
        
        $TransactionType->update($request->all());
        return redirect()->route('transaction-type.index')->with('success','Type Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tran_type = TransactionType::find($id);
        $tran_type->delete();
        return redirect()->route('transaction-type.index')->with('success','Transaction Type Delete successfully.');
    }
}
