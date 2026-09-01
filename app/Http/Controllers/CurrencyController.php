<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Currency;
use App\Models\Rate;
use DB;
use Schema;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::all();
        
        return view("currency.index", compact("currencies"));
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('currency.add');
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
            'name' => 'required',
            'code' => 'required|unique:currency,code',
            'html_symbol' => 'required',
        ]);
        
        Currency::create($request->all());

        $table = 'rates';
        $columnName = strtolower($validated['code']);

        // Add column if it doesn't exist
        if (!Schema::hasColumn($table, $columnName)) {
            try {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columnName) {
                    $tableBlueprint->string($columnName)->nullable();
                });
            } catch (\Exception $e) {
                // Ignore if column was created by another request
            }
        }

        return redirect()->route('currency.index')->with('success', 'Currency created successfully.');
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
        $currency = Currency::findOrFail($id); // Fetch the currency or fail with 404

        return view('currency.add', compact('currency')); // Pass data to view
        
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
        $validated = $request->validate([
            'name' => 'required',
            // 'code' => 'required',
            'html_symbol' => 'required',
        ]);

        $currency = Currency::findOrFail($id);
        $currency->update($validated);

        return redirect()->route('currency.index')->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->delete(); // This will soft delete

        return redirect()->route('currency.index')->with('success', 'Currency deleted successfully.');
    }
}
