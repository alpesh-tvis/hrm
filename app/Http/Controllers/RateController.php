<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Rate;
use Auth;
use Validator;
use Carbon\Carbon;

class RateController extends Controller
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
    public function index(Request $request)
    {
        //$rates = Rate::all();
        $user = Auth::user();
        // dd($rates);
        
        $model = '\App\Models\Rate';
        $tableName = (new $model)->getTable();

        $tableColumns = Schema::getColumnListing($tableName);
        // dd($tableColumns);
        $exclude = ["id", "rate_date", "price","created_at","updated_at","currency"];
        $results = array_values(array_diff($tableColumns, $exclude));
        
        $currentDate = Carbon::now();

        $currentMonth = $currentDate->format('m');

        if ($currentDate->month >= 4) {

            $currentFY = $currentDate->year . '-' . ($currentDate->year + 1);

        } else {

            $currentFY = ($currentDate->year - 1) . '-' . $currentDate->year;
        }

        [$startYear, $endYear] = explode('-', $currentFY);

        $rates = Rate::whereMonth('rate_date', $currentMonth)
            ->where(function ($q) use ($startYear, $endYear, $currentMonth) {

                if ($currentMonth >= 4) {

                    $q->whereYear('rate_date', $startYear);

                } else {

                    $q->whereYear('rate_date', $endYear);
                }

            })
            ->orderBy('rate_date', 'desc')
            ->get();

        if ($request->ajax()) {

    // =========================
    // FY LIST
    // =========================
    if ($request->type == 'fy_list') {

        $dates = Rate::select('rate_date')->get();

        $fys = [];

        foreach ($dates as $d) {

            $date = Carbon::parse($d->rate_date);

            $fy = ($date->month >= 4)
                ? $date->year . '-' . ($date->year + 1)
                : ($date->year - 1) . '-' . $date->year;

            $fys[$fy] = $fy;
        }

        krsort($fys);

        return response()->json(array_values($fys));
    }

    // =========================
    // MONTH LIST
    // =========================
    if ($request->type == 'months') {

        return response()->json([
            ['value'=>'04','label'=>'April'],
            ['value'=>'05','label'=>'May'],
            ['value'=>'06','label'=>'June'],
            ['value'=>'07','label'=>'July'],
            ['value'=>'08','label'=>'August'],
            ['value'=>'09','label'=>'September'],
            ['value'=>'10','label'=>'October'],
            ['value'=>'11','label'=>'November'],
            ['value'=>'12','label'=>'December'],
            ['value'=>'01','label'=>'January'],
            ['value'=>'02','label'=>'February'],
            ['value'=>'03','label'=>'March'],
        ]);
    }

    // =========================
    // FILTERED RATES
    // =========================
    if ($request->type == 'rates') {

            $fy = $request->fy;
            $month = $request->month;

            [$startYear, $endYear] = explode('-', $fy);

            if ($month >= 4) {

                $year = $startYear;

            } else {

                $year = $endYear;
            }

            $rates = Rate::whereYear('rate_date', $year)
                ->whereMonth('rate_date', $month)
                ->orderBy('rate_date', 'desc')
                ->get();

            return response()->json($rates);
        }
    }    

        if($user->role == '2'){
            return view('rates.rates_list',compact('rates','results','currentFY','currentMonth'));
        }
        else
        {
            // return view('admin.admin_main');
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
        return view('rates.rate_form');
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
            'rates_import' => 'required'
        ]);
        $file = $request->file('rates_import');

        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152; 

        // Check file extension
        if(in_array(strtolower($extension),$valid_extension)){

            // Check file size
            if($fileSize <= $maxFileSize){

                // File upload location
                $location = 'uploads';

                // Upload file
                $file->move(public_path()."/uploads/", $file->getClientOriginalName());

                // Import CSV 
                $filepath = public_path($location."/".$filename);

                // Reading file
                $file = fopen($filepath,"r");

                $importData_arr = array();
                $i = 0;
                if ($file !== false) {
                    $rates_headers = fgetcsv($file);
                    $rates_table = 'rates';
                    $excludedFields = ['rate_date', 'price', 'date'];
                    $headers = [];
                    // Step 1: Check and Add Missing Columns
                    foreach ($rates_headers as $column) {
                        $column = trim(strtolower($column));

                        $headers[] = $column;
                        
                        // Skip columns that should not be modified or created
                        if (in_array($column, $excludedFields)) {
                            continue;
                        }
                        
                        if (!Schema::hasColumn($rates_table, $column)) {
                            Schema::table($rates_table, function (Blueprint $table) use ($column) {
                                $table->text($column)->nullable();
                            });
                        }
                    }
                }

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata );
                 
                    for ($c=0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;

                }
                fclose($file);
                
                foreach($importData_arr as $importData){
                    
                    $date = str_replace('/', '-', $importData[0]);
                    $newDate = date("Y-m-d", strtotime($date));
                    
                    $check_rates_exits =  Rate::where('rate_date', $newDate )->first();

                    if(!$check_rates_exits){

                        $usd_price = $importData[1];
                        $gbp_price = $importData[2];
                        $euro_price = $importData[3];
                        $yen_price = $importData[4];
                        $sgd_price = null;
                        if(array_key_exists(5,$importData)){
                            $sgd_price = $importData[5];
                        }
                        
                        Rate::create([
                            'rate_date' => $newDate,
                            'price' => $importData[1],
                            'usd' => $usd_price,
                            'gbp' => $gbp_price,
                            'eur' => $euro_price,
                            'yen' => $yen_price,
                            'sgd' => $sgd_price
                        ]);
                    }
                }

                $path = public_path("uploads/".$filename);
                unlink($path);    
                return redirect()->route('rates.create')->with('success','Rates Import Successfully.');
                
            }else{
                return redirect()->route('rates.create')->with('error','Please upload file less than 2MB'); 
            }
        }
        else {
            return redirect()->route('rates.create')->with('error','Invalid file type. Please upload csv file'); 
        }
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
        //
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
    public function rate_sample(){
        
        $file_path = public_path('uploads/sample_file/BankRate_sample.csv');
        return response()->download( $file_path);
        
    }
    
    public function add_rate(Request $request){
        
        // dd($request->all());
        $validator =  Validator::make($request->all(),[
            // 'rate_date' => 'required|unique:rates,rate_date',
            // 'rate_date' => 'required',
            // 'price' => 'required'
            'rate_date' => $request->rate_id ? 'required' : 'required|unique:rates,rate_date',
            // 'price' => 'required|numeric'
        ]);
        
        if($validator->fails()){
            return response()->json([
                "error" => $validator->errors()->all()
            ]);
        }

        $ignoreFields = ['_token', 'rate_id'];
        $data = $request->except($ignoreFields);

        if (array_key_exists('usd', $data) && empty($data['usd'])) {
            $data['usd'] = $data['price'] ?? null;
        }
        if ($request->filled('usd')) {
            $data['price'] = $request->usd;
        }
        // dd($data);
        if ($request->rate_id) {
            Rate::where('id', $request->rate_id)->update($data);
            $message = 'Rate updated successfully.';
        } else {
            Rate::create($data);
            $message = 'Rate created successfully.';
        }    
  
        return response()->json(['success' => $message]);
    }
}
