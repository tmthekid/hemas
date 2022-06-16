@extends('layout')

@section('content')
<img src="logo.png" style="padding: 20px; margin:0 auto; display: block;">
<div style="background: #fff; padding: 3rem; border-radius: 1rem;">
    <h1 class="text-3xl font-semibold" style="color:#f57b20;">TRY YOUR LUCK TODAY!</h1>
    <p style="color:#666">Enter your full Name, Email address & Contact number below for a chance to spin the wheel and save big on your next purchase</p>
    <form class="mt-3" method="POST" action="{{ route('post.client') }}">
        @csrf
        <div class="mb-2">
            <input value="{{ old('name') }}" id="name" name="name" class="focus:border-light-blue-500 focus:ring-1 focus:ring-light-blue-500 focus:outline-none w-full text-sm text-black placeholder-gray-500 border border-gray-400 rounded-md py-2 px-10" placeholder="Full Name" />
            @error('name')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <input value="{{ old('email') }}" id="email" name="email" class="focus:border-light-blue-500 focus:ring-1 focus:ring-light-blue-500 focus:outline-none w-full text-sm text-black placeholder-gray-500 border border-gray-400 rounded-md py-2 px-10" type="email" placeholder="Email Address" />
            @error('email')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <input type="number" value="{{ old('phone') }}" id="phone" name="phone" class="focus:border-light-blue-500 focus:ring-1 focus:ring-light-blue-500 focus:outline-none w-full text-sm text-black placeholder-gray-500 border border-gray-400 rounded-md py-2 px-10" placeholder="Mobile Number" />
            @error('phone')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
        </div>
        @if(request()->session()->get('spin_error'))
            <div class="text-red-500 mb-2">{{ request()->session()->get('spin_error') }}</div>
        @endif
        @if(request()->session()->get('otp_error'))
            <div class="text-red-500 mb-2">{{ request()->session()->get('otp_error') }}</div>
        @endif
        {{ request()->session()->forget('spin_error') }}
        {{ request()->session()->forget('otp_error') }}
        <button class="w-1/3 h-9 px-4 rounded-md bg-blue-700 text-sm text-white" type="submit">Next</button>
    </form>
    <p style="border-top: #eee 1px solid; padding: 5px; color:#0776bc; margin-top:20px;">
        <ul style="color:#0776bc; font-size:14px;">
            <li>You can spin the wheel only once</li>
            <li>If you win coupon can be claimed for 20 Minutes</li>
        </ul>
    </p>
    <script>
        localStorage.setItem('timer', 30);
        localStorage.setItem('resend', false);
   </script>
</div>
@endsection