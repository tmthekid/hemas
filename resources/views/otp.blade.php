@extends('layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div style="background: rgba(255, 255, 255); padding: 3rem; border-radius: 1rem;">
        <h1 class="text-3xl font-semibold">OTP Details</h1>
        <form class="mt-3" method="POST" action="{{ route('post.otp') }}">
            @csrf
            <div class="mb-2">
                <input id="otp" name="otp" class="mb-2 focus:border-light-blue-500 focus:ring-1 focus:ring-light-blue-500 focus:outline-none w-full text-sm text-black placeholder-gray-500 border border-gray-400 rounded-md py-2 px-10" placeholder="OTP" />
                @error('otp')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <button class="w-1/3 h-9 mb-2 px-4 rounded-md bg-blue-700 text-sm text-white" type="submit">Verify</button>
            <div id="timer"></div>
        </form>
    </div>
    <script>
        if(!localStorage.getItem('otp_visited')) {
            const timer = document.querySelector('#timer');
            const timerInterval = setInterval(() => {
                localStorage.setItem('timer', Number(localStorage.getItem('timer')) - 1);
                timer.textContent = `Time: ${localStorage.getItem('timer')}s`;
                if(Number(localStorage.getItem('timer')) === 0) {
                    clearInterval(timerInterval);
                    localStorage.setItem('otp_visited', 1);
                    timer.innerHTML = '<button type="button" onclick="resetOTP();" style="color: #0000EE; text-decoration: underline;">Resend</button>';
                }
            }, 1000);
        } else {
            if(localStorage.getItem('resend') == 'false') {
                timer.innerHTML = '<button type="button" onclick="resetOTP();" style="color: #0000EE; text-decoration: underline;">Resend</button>';
            }
        }
        function resetOTP() {
            fetch('/resend', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')} })
                .then(res => res.json())
                .then(() => {
                    localStorage.setItem('resend', true); 
                    timer.innerHTML = '';
            });
        } 
    </script>
@endsection
