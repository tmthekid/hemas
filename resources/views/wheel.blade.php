@extends('layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div align="center">
    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <div>
                    <button id="spin_button" class="button-spin" onClick="startSpin();">Spin</button>
                </div>
            </td>
            <td width="438" height="582" class="the_wheel" align="center" valign="center">
                <canvas id="canvas" width="434" height="434">
                    <p style="{color: white}" align="center">Sorry, your browser doesn't support canvas. Please try another.</p>
                </canvas>
            </td>
        </tr>
    </table>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('js/wheel.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
    <script>
        let theWheel; 
        fetch('coupons').then(res => res.json()).then(results => {
            const segments = results.map(code => {
                if(code == '') {
                    return ({ 'fillStyle' : '#5998ab', 'text' : '' });
                }
                return ({ 'fillStyle' : '#f57b20', 'text' : code });
            });
            theWheel = new Winwheel({
                'outerRadius'     : 212,
                'innerRadius'     : 75,
                'textFontSize'    : 0,
                'textOrientation' : 'vertical', 
                'textAlignment'   : 'outer',
                'numSegments'     : segments.length,
                'segments': segments,
                'animation' :
                {
                    'type'     : 'spinToStop',
                    'duration' : 10,
                    'spins'    : 3,
                    'callbackFinished' : alertPrize,
                    'callbackSound'    : playSound,
                    'soundTrigger'     : 'pin'
                },
                'pins' :
                {
                    'number'     : 16,
                    'fillStyle'  : 'silver',
                    'outerRadius': 4,
                }
            });
        });
        let audio = new Audio('tick.mp3');
        function playSound(){
            audio.pause();
            audio.currentTime = 0;
            audio.play();
        }
        let wheelPower    = 0;
        let wheelSpinning = false;
        function powerSelected(powerLevel){
            powerLevel = 3;
            if (wheelSpinning == false) {
                document.getElementById('pw1').className = "";
                document.getElementById('pw2').className = "";
                document.getElementById('pw3').className = "";
                if (powerLevel >= 1) {
                    document.getElementById('pw1').className = "pw1";
                }
                if (powerLevel >= 2) {
                    document.getElementById('pw2').className = "pw2";
                }
                if (powerLevel >= 3) {
                    document.getElementById('pw3').className = "pw3";
                }
                wheelPower = powerLevel;
            }
        }
        function startSpin(){
            document.querySelector('#spin_button').display = 'none';
            if (wheelSpinning == false) {
                if (wheelPower == 1) {
                    theWheel.animation.spins = 3;
                } else if (wheelPower == 2) {
                    theWheel.animation.spins = 6;
                } else if (wheelPower == 3) {
                    theWheel.animation.spins = 10;
                }
                document.getElementById('spin_button').style.display = "none";
                document.getElementById('spin_button').setAttribute('disabled', true);
                theWheel.startAnimation();
                wheelSpinning = true;
            }
        }
        function resetWheel(){
            theWheel.stopAnimation(false);
            theWheel.rotationAngle = 0;
            theWheel.draw();
            document.getElementById('pw1').className = "";
            document.getElementById('pw2').className = "";
            document.getElementById('pw3').className = "";
            wheelSpinning = false;
        }
        function alertPrize(indicatedSegment){
            fetch('/wheel', { method: 'POST', headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }, body: JSON.stringify({ value: indicatedSegment.text }) }).then(async res => {
                localStorage.setItem('timer', 30);
                 localStorage.setItem('resend', false);
                const data = await res.json();
                if(data.data) {
                    window.location = '/code'
                }
            });
        }
    </script>
@endsection