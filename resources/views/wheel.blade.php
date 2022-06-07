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
                <div class="power_controls">
                    <button id="spin_button" class="px-6 py-2 rounded-md bg-blue-700 text-lg text-white" onClick="startSpin();">Spin</button>
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
    <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
    <script>
        let theWheel = new Winwheel({
            'outerRadius'     : 212,        // Set outer radius so wheel fits inside the background.
            'innerRadius'     : 75,         // Make wheel hollow so segments don't go all way to center.
            'textFontSize'    : 24,         // Set default font size for the segments.
            'textOrientation' : 'vertical', // Make text vertial so goes down from the outside of wheel.
            'textAlignment'   : 'outer',    // Align text to outside of wheel.
            'numSegments'     : 5,         // Specify number of segments.
            'segments'        :             // Define segments including colour and text.
            [                               // font size and test colour overridden on backrupt segments.
               {'fillStyle' : '#ee1c24', 'text' : '5KU8S'},
               {'fillStyle' : '#3cb878', 'text' : 'KKS5Y'},
               {'fillStyle' : '#f6989d', 'text' : 'MB6SA'},
               {'fillStyle' : '#00aef0', 'text' : 'I87YH'},
               {'fillStyle' : '#f26522', 'text' : '23SDA'}
            ],
            'animation' :           // Specify the animation to use.
            {
                'type'     : 'spinToStop',
                'duration' : 10,    // Duration in seconds.
                'spins'    : 3,     // Default number of complete spins.
                'callbackFinished' : alertPrize,
                'callbackSound'    : playSound,   // Function to call when the tick sound is to be triggered.
                'soundTrigger'     : 'pin'        // Specify pins are to trigger the sound, the other option is 'segment'.
            },
            'pins' :				// Turn pins on.
            {
                'number'     : 24,
                'fillStyle'  : 'silver',
                'outerRadius': 4,
            }
        });

        // Loads the tick audio sound in to an audio object.
        let audio = new Audio('tick.mp3');

        // This function is called when the sound is to be played.
        function playSound()
        {
            // Stop and rewind the sound if it already happens to be playing.
            audio.pause();
            audio.currentTime = 0;

            // Play the sound.
            audio.play();
        }

        // Vars used by the code in this page to do power controls.
        let wheelPower    = 0;
        let wheelSpinning = false;

        // -------------------------------------------------------
        // Function to handle the onClick on the power buttons.
        // -------------------------------------------------------
        function powerSelected(powerLevel)
        {
            powerLevel = 3;
            // Ensure that power can't be changed while wheel is spinning.
            if (wheelSpinning == false) {
                // Reset all to grey incase this is not the first time the user has selected the power.
                document.getElementById('pw1').className = "";
                document.getElementById('pw2').className = "";
                document.getElementById('pw3').className = "";

                // Now light up all cells below-and-including the one selected by changing the class.
                if (powerLevel >= 1) {
                    document.getElementById('pw1').className = "pw1";
                }

                if (powerLevel >= 2) {
                    document.getElementById('pw2').className = "pw2";
                }

                if (powerLevel >= 3) {
                    document.getElementById('pw3').className = "pw3";
                }

                // Set wheelPower var used when spin button is clicked.
                wheelPower = powerLevel;

                // Light up the spin button by changing it's source image and adding a clickable class to it.
                // document.getElementById('spin_button').src = "http://localhost:8000/spin_on.png";
                // document.getElementById('spin_button').className = "clickable";
            }
        }

        // -------------------------------------------------------
        // Click handler for spin button.
        // -------------------------------------------------------
        function startSpin()
        {
            document.querySelector('#spin_button').display = 'none';
            // Ensure that spinning can't be clicked again while already running.
            if (wheelSpinning == false) {
                // Based on the power level selected adjust the number of spins for the wheel, the more times is has
                // to rotate with the duration of the animation the quicker the wheel spins.
                if (wheelPower == 1) {
                    theWheel.animation.spins = 3;
                } else if (wheelPower == 2) {
                    theWheel.animation.spins = 6;
                } else if (wheelPower == 3) {
                    theWheel.animation.spins = 10;
                }

                // Disable the spin button so can't click again while wheel is spinning.
                // document.getElementById('spin_button').src = "http://localhost:8000/spin_off.png";
                // document.getElementById('spin_button').className = "";
                document.getElementById('spin_button').style.display = "none";
                document.getElementById('spin_button').setAttribute('disabled', true);
                // Begin the spin animation by calling startAnimation on the wheel object.
                theWheel.startAnimation();

                // Set to true so that power can't be changed and spin button re-enabled during
                // the current animation. The user will have to reset before spinning again.
                wheelSpinning = true;
            }
        }

        // -------------------------------------------------------
        // Function for reset button.
        // -------------------------------------------------------
        function resetWheel()
        {
            theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
            theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
            theWheel.draw();                // Call draw to render changes to the wheel.

            document.getElementById('pw1').className = "";  // Remove all colours from the power level indicators.
            document.getElementById('pw2').className = "";
            document.getElementById('pw3').className = "";

            wheelSpinning = false;          // Reset to false to power buttons and spin can be clicked again.
        }

        // -------------------------------------------------------
        // Called when the spin animation has finished by the callback feature of the wheel because I specified callback in the parameters.
        // -------------------------------------------------------
        function alertPrize(indicatedSegment)
        {
            fetch('/wheel', { method: 'POST', headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }, body: JSON.stringify({ value: indicatedSegment.text }) }).then(async res => {
                const data = await res.json();
                if(data) {
                    window.location = '/code'
                } else {
                    alert('Something went wrong. Please try again.');
                }
            });
        }
    </script>
@endsection