@extends('layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css" />
@endsection

@section('content')
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        @if(request()->session()->get('code') != '')
            <div style="background: rgba(255, 255, 255); padding: 2rem; border-radius: 1rem; margin: 20px auto;">
                <div class="flex items-center">
                    <div class="text-lg">Coupon:</div>
                    <p class="ml-3 px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded">{{ request()->session()->get('code') }}</p>
                </div>
                <p class="text-right mt-6">{!! request()->session()->get('description') !!}</p>
            </div>
            <a href="/pdf?code={{ request()->session()->get('code') }}&description={{ request()->session()->get('description') }}" class="px-4 py-2 rounded-md bg-blue-700 text-sm text-white">Print</a>
            {{ request()->session()->forget('code') }}
            {{ request()->session()->forget('description') }}
        @else
            <div style="background: rgba(255, 255, 255); padding: 2rem; border-radius: 1rem; margin: 20px auto;">We're sorry to say, You're not a winner this time</div>
            <a href="/" class="px-4 py-2 rounded-md bg-blue-700 text-sm text-white">Back</a>
            {{ request()->session()->forget('code') }}
        @endif
    </div>
    <script>
        localStorage.removeItem('otp_visited');
    </script>
@endsection