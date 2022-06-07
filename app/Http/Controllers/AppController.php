<?php
namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Support\Facades\App;

class AppController extends Controller
{
    public function index(){
        return view('index');
    }

    public function saveClient(){
        request()->validate([
            'full_name' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);
        request()->session()->put('client_name', request()->full_name);
        request()->session()->put('client_email', request()->email);
        request()->session()->put('client_phone', request()->phone);
        return view('otp');
    }

    public function verifyOTP(){
        request()->validate([
            'otp' => 'required',
        ]);
        return redirect()->route('get.wheel');
    }

    public function getWheel(){
        if(!request()->session()->get('client_name')){
            return redirect()->route('index');
        }
        return view('wheel');
    }

    public function saveWheel(){
        request()->session()->put('code', request()->value);
        return response()->json(['data' => true]);
        // $code = $value->codes->where('status', true)->random();
        // $client_name = request()->session()->get('client_name');
        // $client_email = request()->session()->get('client_email');
        // $client_phone = request()->session()->get('client_phone');
        // request()->session()->forget('client_name');
        // request()->session()->forget('client_email');
        // request()->session()->forget('client_phone');
        // if($serial && $code->id) {
        //     $serial->update(['status' => false]);
        //     $serial->client()->create([
        //         'full_name' => $client_name,
        //         'email' => $client_email,
        //         'phone' => $client_phone
        //     ]);
        //     request()->session()->put('code', $code->code);
        //     return response()->json(['data' => true]);
        // } else {
        //     return response()->json(['data' => 'Something went wrong, please try again']);
        // }
    }

    public function getCode(){
        return view('code');
    }

    public function downloadPDF(){
        $code = request()->session()->get('code');
        request()->session()->forget('code');
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>'. request()->code .'</h1>');
        return $pdf->stream();
    }
}