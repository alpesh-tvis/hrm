<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Employee;
use App\Models\Statement;
use App\Models\Bid_status;
use App\Models\BidRelation;
use App\Models\Client;
use App\Models\Project;
use DataTables;
use Validator;
use Response;
use Carbon\Carbon;

class LeadController extends Controller
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

        if ($request->ajax()) {
            $user = Auth::user();
            $employee = Employee::where('company_email', $user->email)->first();
            $query = Lead::join('employees', 'leads.user_id', '=', 'employees.id')
                ->select('employees.first_name', 'leads.*');

            
            if ($request->filled('report_date')) {

                $ex_report_date = explode('-', $request->report_date);
                $start_date = Carbon::createFromFormat('d/m/Y', trim($ex_report_date[0]))->format('Y-m-d');
                $end_date = Carbon::createFromFormat('d/m/Y', trim($ex_report_date[1]))->format('Y-m-d');

                if($employee->reporting_person == '1'){
                    $query->whereBetween('leads.bid_date', [$start_date, $end_date]);
                }else{
                    $query->whereBetween('leads.bid_date', [$start_date, $end_date])
                    ->where('leads.user_id', $employee->id); 
                }    
            } else {

                $start_date = now()->subDays(6)->format('Y-m-d'); // last 7 days including today
                $end_date   = now()->format('Y-m-d');

                if($employee->reporting_person == '1'){
                    $query->whereBetween('leads.bid_date', [$start_date, $end_date]);
                }else{
                    $query->whereBetween('leads.bid_date', [$start_date, $end_date])
                          ->where('leads.user_id', $employee->id);
                }
            }

            // Filter by sales person if specified
            if ($request->has('sales_person') && $request->sales_person != 'all') {
                $query->where('leads.user_id', $request->sales_person);
            }

            // Filter by bid status
            if ($request->filled('bid_status')) {
                $query->where('leads.bid_status', $request->bid_status);
            }

            // Filter by bid source
            if ($request->filled('bid_source')) {
                $query->where('leads.bid_source', $request->bid_source);
            }

            $lead = $query->orderBy('leads.id', 'DESC')->get();

            // Modify bid status
            $chanels = ["Bidding", "Communication", "Offer", "Contract Start", "Contract Close success", "Contract Close Unsuccess", "Contract Pause", "Bid Close", "Viewed"];
            foreach ($lead as $lead_val) {
                $lead_val->bid_status = $chanels[$lead_val->bid_status];

                 $leadSources = [
                                    1 => "Upwork",
                                    2 => "LinkedIn",
                                    3 => "Clutch",
                                    4 => "Freelancer"
                                ];

                $lead_val->bid_source = $leadSources[$lead_val->bid_source] ?? '-';
            }

            return DataTables::of($lead)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $URLButton = "<a href='".$row->bid_url."' target='_blank' class='btn btn-sm btn-primary' data-bs-toggle='tooltip' data-bs-placement='top' title='Bid URL'><i class='fas fa-link'></i></a>";

                    $updateButton = "<a href='javascript:void(0)' class='btn btn-sm btn-success lead-update mr-1'  data-id='".$row->id."' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Bid'><i class='fas fa-edit'></i></a>".$URLButton."";
                    $viewButton = "<a href='javascript:void(0)' class='btn btn-sm btn-primary lead-view'  data-id='".$row->id."' data-bs-toggle='tooltip' data-bs-placement='top' title='View Bid'><i class='fas fa-eye'></i></a>".$URLButton."";
                    if ($row->bid_status == 'Bid Close' || $row->bid_status == 'Contract Pause' || $row->bid_status == 'Contract Close Unsuccess' || $row->bid_status == 'Contract Close success') {
                        return $viewButton;
                    } else {
                        return $updateButton;
                    }
                })
                ->editColumn('created_at', function ($date) {
                    return $date->created_at->format('d-m-Y H:i:s');
                }) 
                ->editColumn('bid_url', function ($item) {
                    return '<a href="'.$item->bid_url.'" target="_blank">'.$item->bid_url.'</a>';
                })
                ->addColumn('bid_source_label', function ($row) {
                    $leadSources = [
                        1 => "Upwork",
                        2 => "LinkedIn",
                        3 => "Clutch",
                        4 => "Freelancer"
                    ];

                    return $leadSources[$row->bid_source] ?? '-';
                })
                ->rawColumns(['bid_name','action'])
                ->make(true);
        }
        $user = Auth::user();

        $employee = Employee::where('company_email',$user->email)->first();
        $html = '';
        $emp = Employee::where('id',$user->id)->first();
            
        $userIdsWithNames = Lead::select('employees.id', 'employees.first_name')
            ->join('employees', 'leads.user_id', '=', 'employees.id')
            ->whereNull('employees.service_enddate')
            ->where('employees.id', '!=', 1)  
            ->groupBy('leads.user_id')
            ->distinct()
            ->orderBy('employees.first_name')
            ->pluck('employees.first_name','employees.id');

            // dd($userIdsWithNames);
        
        $html .= '<form id="filter_form" class="form-horizontal mb-0">';
            $html .= '<div class="modal-body">';
                $html .= '<div class="card-body">';
                    $html .= '<div class="row">';
                        if($emp->reporting_person == '1'){
                            $html .= '<div class="col-md-2 ">';
                                $html .= '<div class="form-group mb-0">';
                                    $html .= '<label for="sales_person">Sales Person</label>';
                                    $html .= '<select name="sales_person" id="sales_person" class="form-control">';
                                        $html .= '<option value="all">-- Select All --</option>';
                                            foreach($userIdsWithNames as $key => $name){
                                                $html .= '<option value="'.$key.'">'.$name.'</option>';
                                            }
                                    $html .= '</select>';
                                $html .= '</div>';
                            $html .= '</div>';
                        }  
                        $html .= '<div class="col-md-2">';
                            $html .= '<div class="form-group mb-0">';
                                $html .= '<label for="bid_status">Bid Status</label>';
                                $html .= '<select name="bid_status" id="bid_status" class="form-control">';
                                    $html .= '<option value="">-- All Status --</option>';
                                    $html .= '<option value="0">Bidding</option>';
                                    $html .= '<option value="1">Communication</option>';
                                    $html .= '<option value="2">Offer</option>';
                                    $html .= '<option value="3">Contract Start</option>';
                                    $html .= '<option value="4">Contract Close Success</option>';
                                    $html .= '<option value="5">Contract Close Unsuccess</option>';
                                    $html .= '<option value="6">Contract Pause</option>';
                                    $html .= '<option value="7">Bid Close</option>';
                                    $html .= '<option value="8">Viewed</option>';
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-2">';
                            $html .= '<div class="form-group mb-0">';
                                $html .= '<label for="bid_source">Bid Source</label>';
                                $html .= '<select name="bid_source" id="bid_source" class="form-control">';
                                    $html .= '<option value="">-- All Sources --</option>';
                                    $html .= '<option value="1">Upwork</option>';
                                    $html .= '<option value="2">LinkedIn</option>';
                                    $html .= '<option value="3">Clutch</option>';
                                    $html .= '<option value="4">Freelancer</option>';
                                $html .= '</select>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-2">';
                            $html .= '<div class="form-group mb-0">';
                                $html .= '<label for="date_range" style="width:100%;">Select Date</label>';
                                $html .= '<input type="text" name="report_date" value="" class="mb-2 form-control">';
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-2">';
                            $html .= '<div class="form-group mb-0">';
                                $html .= '<label for="bid_submit">&nbsp;</label>';
                                $html .= '<button type="submit" id="get_reports" class="btn btn-primary form-control"> Get Bids </button>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</form>';  

        if($user->role == '2' || $employee->department == '1'){
            return view('lead.add_edit')->with('html', $html);
        }
        else
        {
            return view('admin.admin_main');
        }
        // return view('lead.add_edit');
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
        $employee = Employee::where('company_email',$user->email)->first();

        if($user->role == '2' || $employee->department == '1'){
            return view('lead.add_edit');
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
        // dd($request->all());
        $exists_bid_status = Bid_status::where('bid_id',$request->lead_id)->where('bid_status',$request->bid_status)->exists();
        $user = Auth::user();
        
        if($request->bid_status === '4' || $request->bid_status === '5' || $request->bid_status === '6'){
            $BidRelation_get = BidRelation::where('bid_id',$request->lead_id)->first();
                if(!empty($BidRelation_get->project_id)){
                    $project = Project::where('id',$BidRelation_get->project_id)->update([
                        'end_date' => $request->end_date 
                    ]);
                }    
            
        }          
        if(!empty($request->lead_id)){
            $validator =  Validator::make($request->all(),[
                'bid_name' => 'required|string|max:255',
                'bid_url' => 'required|url|max:255',
                'bid_status' => 'required',
                'bid_source' => 'required',
                'bid_reason' => $request->bid_status === '7' || $request->bid_status === '6' || $request->bid_status === '5' ? 'required': 'nullable',
                'company' => $request->bid_status === '3' ? 'required': 'nullable',
            ],
            [],
            [
                'company' => 'Client Name',
            ]);

            

            if($validator->fails()){
                return response()->json([
                    "error" => $validator->errors()->all()
                ]);
            }
        }
        if(empty($request->lead_id_post) && empty($request->lead_id)){
            // lead_id_post
                $validated = $request->validate([
                    'bid_name' => 'required|string|max:255',
                    'bid_url' => 'required|url|max:255|unique:leads,bid_url'
                ]);
        }
        $request->user_id = $user->id;

        // Lead insert
        if(!empty($request->lead_id)){
            if($request->bid_status === '3'){
                

                if(!empty($request->company))
                {
                    $company = Client::create([
                        'company' => $request->company
                    
                    ]);

                    Lead::where('id', $request->lead_id)->update([
                       'bid_name' => $request->bid_name,
                       'bid_url' => $request->bid_url,
                       'bid_status' => $request->bid_status,
                       'client_id'=> $request->bid_status === '3' ? $company->id : '',
               
                    ]);
                }
                if(!empty($request->project))
                {

                    $check_project = Project::where('id',$request->project)->exists();
                    if($check_project == true){
                        // dd('exists');
                        $project_id = $request->project;
                    }else{
                        $project_create = Project::create([
                            'project_name' => $request->project
                        ]);
                        $project_id = $project_create->id;
                    }
                    
                    BidRelation::create([
                        'id' => $request->lead_id,
                        'bid_id' => $request->lead_id,
                        'project_id' => $project_id,
                        'client_id' => $company->id
                    ]);
                    Project::where('id',$project_id)->update([
                        'start_date' => $request->start_date 
                    ]);
                    
                    
                }
                  
                
            }        
            Lead::where('id', $request->lead_id)->update([
               'bid_name' => $request->bid_name,
               'bid_url' => $request->bid_url,
               'bid_status' => $request->bid_status,
               'bid_source' => $request->bid_source,
               'bid_reason'=> $request->bid_status === '7' || $request->bid_status === '6' || $request->bid_status === '5' ? $request->bid_reason : '',
            ]);

            if($exists_bid_status == false){
            
                $bid_status = Bid_status::create([
                    'bid_id' => $request->lead_id,
                    'user_id' => $request->user_id,
                    'bid_status' => $request->bid_status,
                    'bid_date' => $request->bid_date
                ]);
            }
            return response()->json(['success' => 'Bid  updated successfully.']);
        }
        else
        {
            $insert_lead = Lead::create([
                'bid_name' => $request->bid_name,
                'bid_url' => $request->bid_url,
                'bid_status' => 0,
                'bid_source' => $request->bid_source,
                'user_id' => $request->user_id,
                'bid_date' => $request->bid_date,
            ]);

            $bid_status = Bid_status::create([
                'bid_id' => $insert_lead->id,
                'user_id' => $request->user_id,
                'bid_status' => 0,
                'bid_date' => $request->bid_date,
            ]);
            return redirect()->route('bids.index')->with('success','Bid Created Successfully.');
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,  $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lead_id=Lead::find($id);
        $lead_id->project = Project::where('created_at','>',$lead_id->created_at)->get();
        
        if($lead_id->bid_status == '7' || $lead_id->bid_status == '4' || $lead_id->bid_status == '5' || $lead_id->bid_status == '6')
        {

            $lead_id1=lead::join('bid_status','leads.id','=','bid_status.bid_id')->where('bid_status.bid_id','=',$lead_id->id)->orderBy('bid_status.id', 'asc')->get();
            

            $status_str = '';

            $status_arr = ["Bidding", "Communication", "Offer","Contract Start","Contract Close success","Contract Close Unsuccess","Contract Pause","Bid Close", "Viewed"];
              
            foreach($lead_id1 as $l_id1){
                $status_str .= $status_arr[$l_id1->bid_status] . ' → '; 
                
            }
               
            $status_str = trim($status_str, ' → ');
            return Response::json(array(
                'bid_details' => $lead_id,
                'bid_stat' => $status_str
            ));
        }    
        return Response::json($lead_id);
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
}
