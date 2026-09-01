<?php

namespace App\Http\Controllers;


use App\Models\MailRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Carbon\Carbon;
use App\Mail\MailRequestMail;


class MailRequestController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index()
    {
        $userid    = auth()->user()->id;
        $user_data = auth()->user();
        $requests_data = MailRequest::with('employee');
        //echo "<pre>"; print_r($requests_data);

        if ($user_data->role == 1) {
            $requests_data = $requests_data->where('user_id', $userid);
        }

        $get_emp_data = Employee::where('id', $userid)->first();
        //echo "<pre>"; print_r($get_emp_data); die();

        if ($get_emp_data->reporting_person == 1 && $user_data->role != '2') {

            // Sales person
            if ($get_emp_data->department == 1) {
                $all_emp          = Employee::where('service_enddate', null)->where('reporting_person', $userid)->pluck('id')->toArray();
                $all_emp[$userid] = $userid;
                $requests         = MailRequest::whereIn('user_id', $all_emp)->get();
                $e_leader         = 'sales';

                // User list: all team members from Employee table
                $distinct_user_data = Employee::whereIn('id', $all_emp)
                    ->select('id', 'first_name', 'last_name')
                    ->get()
                    ->map(fn($e) => [
                        'user_id' => $e->id,
                        'name'    => trim($e->first_name . ' ' . $e->last_name),
                    ])->values();
            }

            // Operation team
            if ($get_emp_data->department == 2) {
                $all_emp   = Employee::where('service_enddate', null)->where('reporting_person', $userid)->pluck('id')->toArray();
                $all_emp[] = $userid;
                $requests  = MailRequest::whereIn('user_id', $all_emp)->get();
                $e_leader  = 'tl';

                // User list: all team members from Employee table
                $distinct_user_data = Employee::whereIn('id', $all_emp)
                    ->select('id', 'first_name', 'last_name')
                    ->get()
                    ->map(fn($e) => [
                        'user_id' => $e->id,
                        'name'    => trim($e->first_name . ' ' . $e->last_name),
                    ])->values();
            }

        } else {
            $requests = $requests_data->get();
            $e_leader = 'emp';

            if ($user_data->role == 2) {
                $e_leader = 'admin';

                // Admin: all active employees
                $distinct_user_data = Employee::whereNull('service_enddate')
                    ->select('id', 'first_name', 'last_name')
                    ->get()
                    ->map(fn($e) => [
                        'user_id' => $e->id,
                        'name'    => trim($e->first_name . ' ' . $e->last_name),
                    ])->values();
            } else {
                // Regular employee: no dropdown needed
                $distinct_user_data = collect();
            }
        }

        $get_request = $requests->map(function ($request) {
            return [
                'id'           => $request->id,
                'subject'      => $request->subject,
                'user_id'      => $request->user_id,
                'reason'       => ucwords(str_replace('_', ' ', $request->reason)),
                'request_date' => Carbon::parse($request->request_date)->format('d-m-Y'),
                'status'       => ucfirst($request->status),
                'name'         => $request->employee
                    ? $request->employee->first_name . ' ' . $request->employee->last_name
                    : 'Unknown',
                'description'  => $request->description,
                'created_at'   => $request->created_at,
                'updated_at'   => $request->updated_at,
            ];
        });

        $distinct_reason_data = $get_request->unique('reason')->map(function ($item) {
            return [
                'reason' => ucfirst($item['reason']),
            ];
        })->values();

        $distinct_status_data = $get_request->unique('status')->map(function ($item) {
            return [
                'status' => ucfirst($item['status']),
            ];
        })->values();

        return view('request.index', [
            'requests'  => $get_request,
            'user_list' => $distinct_user_data,
            'leader'    => $e_leader,
            'reasons'   => $distinct_reason_data,
            'status'    => $distinct_status_data
        ]);
    }

    public function create()
    {
        return view('request.request-mail');
    }

    public function store(Request $request)
    {
        $admin_Email = "parth@tvistech.com";
        $user_id     = Auth::id();

        $validated = $request->validate([
            'reason'       => 'required',
            'subject'      => 'required',
            'description'  => 'required',
            'request_date' => $request->reason == 'late_coming' ? 'required|date' : 'nullable|date',
        ]);

        $request_mail = MailRequest::create([
            'user_id'      => $user_id,
            'reason'       => $request->reason,
            'subject'      => $request->subject,
            'request_date' => $request->request_date,
            'description'  => $request->description
        ]);

        $employee               = Employee::find($user_id);
        $username               = $employee->first_name . ' ' . $employee->last_name;
        $reporting_person       = Employee::where('id', $employee->reporting_person)->first();
        $reporting_person_email = $reporting_person->company_email;
        $userEmail              = Auth::user()->email;

        $date          = \Carbon\Carbon::parse($request->request_date);
        $request_date  = $date->format("F j, Y, l");

        $MailData  = "";
        $MailData .= "<div><strong>Subject:</strong> {$request->subject}</div>";
        $MailData .= "<div><strong>Date:</strong> {$request_date}</div>";
        $MailData .= "<div><strong>Description:</strong> {$request->description}</div>";

        $data = [
            'username'   => $username,
            'MailData'   => $MailData,
            'user_email' => $userEmail
        ];


        try {

            Mail::to($admin_Email)
                ->cc($reporting_person_email)
                ->bcc($userEmail)
                ->send(new MailRequestMail($data));

        } catch (\Exception $e) {

            \Log::error('Mail request email failed: ' . $e->getMessage());
            return redirect()->route('mail-request.create')
                ->withErrors(['error' => 'Mail request saved, but email notification could not be sent.']);
                
        }

        return redirect()->route('mail-request.create')->with('success', 'The request was successful!');
    }

    public function show(MailRequest $mailRequest)
    {
        //
    }

    public function edit(MailRequest $mailRequest)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,cancelled',
        ]);

        try {
            if (trim(strtolower($request->confirm)) == "yes") {

                $mailRequest = MailRequest::find($id);

                if (!$mailRequest) {
                    return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
                }

                $mailRequest->updated_at = now();
                $mailRequest->status     = $request->status;
                $mailRequest->save();

                $message = 'Status ' . ucfirst($request->status) . ' Changed Successfully';
                return response()->json(['success' => true, 'message' => $message]);
            }

            if (trim(strtolower($request->confirm)) == "no") {
                $message = "you want to change the status to " . $request->status . '?';
                return response()->json(['success' => true, 'message' => $message]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(MailRequest $mailRequest)
    {
        //
    }
}
