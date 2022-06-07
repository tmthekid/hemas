@extends('layout')

@section('content')
<div style="background: rgba(255, 255, 255, .7); padding: 3rem; border-radius: 1rem;">
    <h1 class="text-3xl font-semibold">OTP Details</h1>
    <form class="mt-3" method="POST" action="{{ route('post.otp') }}">
        @csrf
        <div class="mb-2">
            <input id="otp" name="otp" class="focus:border-light-blue-500 focus:ring-1 focus:ring-light-blue-500 focus:outline-none w-full text-sm text-black placeholder-gray-500 border border-gray-400 rounded-md py-2 px-10" placeholder="OTP" />
            @error('otp')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
        </div>
        <button class="w-1/3 h-9 px-4 rounded-md bg-blue-700 text-sm text-white" type="submit">Confirm</button>
    </form>
</div>
@endsection