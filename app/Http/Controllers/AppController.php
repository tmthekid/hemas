<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\{Result, Otp, Client, Code};
use App\Http\Requests\{ClientFormRequest, VerifyOTPFormRequest};

class AppController extends Controller
{
    public function index(){
        return view('index');
    }

    public function saveClient(ClientFormRequest $request){
        $resultEntity = Result::where('phone', $request->phone)->first();
        if($resultEntity) {
            $request->session()->put('spin_error', 'You have already spun the wheel');
            return back()->withInput();
        }
        $otpEntity = Otp::where('phone', $request->phone)->first();
        if($otpEntity && $otpEntity->verified) {
            $request->session()->put('otp_error', 'Phone number has already been used');
            return back()->withInput();
        }
        if($otpEntity && $otpEntity->status == 2) {
            $request->session()->put('otp_error', 'Your OTP limit has been exceeded');
            return back()->withInput();
        }
        if($otpEntity && $otpEntity->status == 1) {
            $otp = rand(100000, 600000);
            $this->sendOtp($otp, $request->phone);
            $otpEntity->update(['code' => $otp,'status' => 2]);
            $request->session()->put('phone', $request->phone);
            return redirect()->route('get.otp');
        }
        $clientEntity = Client::where('phone', $request->phone)->first();
        if($clientEntity) {
            $otp = rand(100000, 600000);
            $this->sendOtp($otp, $request->phone);
            Otp::create(['code' => $otp, 'phone' => $request->phone, 'status' => 1]);
            $request->session()->put('phone', $request->phone);
            return redirect()->route('get.otp');
        }
        Client::create($request->only(['name', 'email' , 'phone']));
        $otp = rand(100000, 600000);
        $this->sendOtp($otp, $request->phone);
        Otp::create(['code' => $otp, 'phone' => $request->phone, 'status' => 1]);
        $request->session()->put('phone', $request->phone);
        return redirect()->route('get.otp');
    }

    public function getOTP(){
        $code = Otp::where('phone', request()->session()->get('phone'))->first();
        if(!request()->session()->get('phone') || !$code){
            return redirect()->route('home');
        }
        if($code->verified) {
            return redirect()->route('get.wheel');
        }
        return view('otp');
    }

    public function resendOTP(){
        $otpEntity = Otp::where('phone', request()->session()->get('phone'))->first();
        if($otpEntity && $otpEntity->status === 1 && !$otpEntity->verified) {
            $code = rand(100000, 600000);
            $this->sendOtp($code, $otpEntity->phone);
            $otpEntity->update(['code' => $code, 'status' => 2]);
            return response()->json(['data' => true]);
        }
        return response()->json(['data' => false]);
    }

    public function verifyOTP(VerifyOTPFormRequest $request){
        if(!request()->session()->get('phone')){
            return redirect()->route('home');
        }
        OTP::where('code', $request->otp)->update(['verified' => true]);
        return redirect()->route('get.wheel');
    }

    public function getCoupons(){
        $coupons = Code::get()->map(function($code){
            return $code->available > 0 ? [$code->code, ''] : '';
        });
        return $coupons->flatten();
    }
    
    public function getWheel(){
        $code = Otp::where('phone', request()->session()->get('phone'))->first();
        if(!$code || !request()->session()->get('phone') || !$code->verified){
            return redirect()->route('get.otp');
        }
        return view('wheel');
    }

    public function saveWheel(){
        $code = request()->value;
        if($code == '') {
            request()->session()->put('code', '');
            return response()->json(['data' => true]);
        }
        Result::create(['phone' => session()->get('phone'), 'code' => $code]);
        $codeEntity = Code::where('code', $code)->first();
        request()->session()->put('code', $code);
        request()->session()->put('description', $codeEntity->description);
        if($codeEntity->available > 0) {
            $codeEntity->decrement('available');
        }
        return response()->json(['data' => true]);
    }

    public function getCode(){
        if(!request()->session()->get('phone')){
            return redirect()->route('home');
        }
        request()->session()->forget('phone');
        return view('code');
    }

    public function downloadPDF(){
        $code = request()->session()->get('code');
        request()->session()->forget('code');
        request()->session()->forget('phone');
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>'.request()->code.'</h1><br /><h3>'.request()->description.'</h3>');
        return $pdf->stream();
    }

    protected function sendOtp($otp, $phone){
        if(strlen($phone) == 10){
	     $orderPhoneFormated='94'.substr($phone, -9);
    	}else if(strlen($phone) == 9){
    	    $orderPhoneFormated='94'.$phone;
    	}else{
    	    $orderPhoneFormated= preg_replace("/[^\d]/","",$phone);
    	}
       
       date_default_timezone_set('Asia/Colombo');
      
        $now = date("Y-m-d\TH:i:s");
        $username = "hemas_user";
        $password = "@q123456";
        $digest=md5($password);
        $body = '{
        "messages": [
        		{
        		"clientRef": "'.$otp.'",
        		"number": "'.$orderPhoneFormated.'",
        		"mask": "HEMASESTORE",
        		"text": "Spin the wheel otp is '.$otp.' ",
        		"campaignName":"HemasOrder"
        		}
        	]
        }';
        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL,"https://richcommunication.dialog.lk/api/sms/send");
        	curl_setopt($ch, CURLOPT_POST, 1);
        	curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	$headers = [
        	 	'Content-Type: application/json',
        	 	'USER: '.$username,
        		'DIGEST: '.$digest,
        	 	'CREATED: '.$now
        	];
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        	$server_output = curl_exec ($ch);
        	curl_close ($ch);
        
        	if(!empty($server_output)){		
        	}else{
        	}
    }
}