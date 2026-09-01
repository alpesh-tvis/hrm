<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels; 
use Illuminate\Support\Facades\Schedule;  
use DateTime;

class HolidayController extends Controller
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('emails:send')->everyMinute(); // Send emails daily
        // Other scheduling options: hourly(), everyMinute(), etc.
    }
    public function __construct(){
        $this->middleware(['auth','verified']);
    }

    public function financial_year(){
        $date = date('m');
        if ($date > 3) {
        $year = date('Y')."-".(date('Y') +1);
        }
        else {
        $year = (date('Y')-1)."-".date('Y');
        }
        return $year;
    }


    public function index(){
        $holidayList = Holiday::orderBy('holiday_date', 'ASC')->get();
        $financial_years = DB::table('holidays')->select('finanical_year')->groupBy('finanical_year')->orderBy('finanical_year', 'desc')->get();
        
        //For Email Notification for latest holiday  
        /*$startDate = Carbon::today();
        $nexttwoDayDate = Carbon::today()->addDays(3);
        foreach ($holidayList as $datevalue) {
            $holidayss = $datevalue->holiday_date;
            $date = new DateTime($holidayss);
            $formattedDate = date_format($date, 'l, F j, Y');
            
            $remark = $datevalue->remark;
        }

        if ($nexttwoDayDate !== $datevalue->holiday_date) {
           
            $recipient = 'lokesh@tvistech.com';
            $subject = 'Upcoming Holiday - '. $remark;
            $message = 'Your Holiday Special - ' . $formattedDate;

            Mail::raw($message, function($mail) use ($recipient, $subject) {
                $mail->to($recipient)->subject($subject);
            });
            
        }*/
        
        //Send Email notify
        /*$recipient = 'lokesh@tvistech.com';
        $subject = 'Upcoming Holiday';
        $message = 'Your Holiday Special';

        Mail::raw($message, function($mail) use ($recipient, $subject) {
            $mail->to($recipient)->subject($subject);
        });*/


        return view('holiday.index')->with(compact('holidayList', 'financial_years'));
    }

    public function create(){
        $currentadmin_id = Auth::id();
         
        if( $currentadmin_id == 1){
        return view('holiday.add');
        }
        else{
        return redirect()->back();
        }   
    }

    public function store(Request $request){
        $currentadmin_id = Auth::id();
        
        $this->validate($request, [
            'holiday_date' => ['required', Rule::unique('holidays')->ignore($request->id)],
            'remark' => 'required|string',
        ]);

        $myDate = $request->holiday_date;
        $date = $myDate;
        $day = date('l', strtotime($date));
        $finc_year = $this->financial_year();

        $holiday = new Holiday();
        $holiday->holiday_date = $myDate;
        $holiday->day = $day;
        $holiday->remark = $request->remark;
        $holiday->finanical_year =  $finc_year;
        $holiday->save();

        $holiday_name = $holiday->remark;
       

        if( $currentadmin_id == 1){
        return redirect()->back()->with('holiday_add', $holiday_name . ' Holiday Leave submitted successfully');
        }
        else{
        return redirect()->back();
        }  
    }

    public function edit(Request $request){
        $holidayid = $request->id;
        $allHoliday = Holiday::find($holidayid);

        return view('holiday.edit')->with(compact('allHoliday'));
    }

    public function update(Request $request, $id){
        $id = $request->id;
        $holidayupdate = Holiday::find($id);

        $this->validate($request, [
            'holiday_date' => ['required', Rule::unique('holidays')->ignore($request->id)],
            'remark' => 'required|string',
        ]);
        
        $myDate = $request->holiday_date;
        $date = $myDate;
        $day = date('l', strtotime($date));
        $finc_year = $this->financial_year();
        
        if ($id) {

        $holidayupdate->id = $request->id;
        $holidayupdate->holiday_date = $date;
        $holidayupdate->day = $day;
        $holidayupdate->remark = $request->remark;
        $holidayupdate->finanical_year = $finc_year;
        }
        $remark =  $request->remark;

        $holidayupdate->update();

         return redirect()->back()->with('holiday_update_success', $remark . ' Holiday updated successfully');
    }

    public function destroy(Request $request, $id){

        Holiday::find($id)->delete();
        return response()->json(['destroy_holiday_success' => 'Holiday row deleted successfully!']);

    }

    

}
