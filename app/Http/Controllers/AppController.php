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

    public function geOTP(){
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
        if(!$code) {
            request()->session()->forget('phone');
            return response()->json(['data' => false]);
        }
        request()->session()->put('code', $code);
        Result::create(['phone' => session()->get('phone'), 'code' => $code]);
        $codeEntity = Code::where('code', $code)->first();
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
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>'. request()->code .'</h1>');
        return $pdf->stream();
    }

    protected function sendOtp($otp, $phone){}
}