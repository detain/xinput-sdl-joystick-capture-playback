<?php

// Load the XInput library using FFI
$ffi = FFI::cdef('
    typedef struct _XINPUT_STATE {
        DWORD dwPacketNumber;
        XINPUT_GAMEPAD Gamepad;
    } XINPUT_STATE, *PXINPUT_STATE;

    typedef struct _XINPUT_GAMEPAD {
        WORD wButtons;
        BYTE bLeftTrigger;
        BYTE bRightTrigger;
        SHORT sThumbLX;
        SHORT sThumbLY;
        SHORT sThumbRX;
        SHORT sThumbRY;
    } XINPUT_GAMEPAD, *PXINPUT_GAMEPAD;

    DWORD XInputGetState(DWORD dwUserIndex, XINPUT_STATE* pState);
', 'xinput1_4.dll');

// Create an array to store joystick press information
$joystickPresses = array();

// Create a variable to store the current state of the joystick
$prevJoystickState = null;

// Create a variable to store the timestamp of the previous update
$prevUpdateTime = PHP_INT_MIN;

// Set the duration threshold for a press
$pressDurationThreshold = 50; // in milliseconds

// Loop indefinitely
while (true) {
    // Get the current state of the joystick
    $joystickState = FFI::new('XINPUT_STATE');
    $ffi->XInputGetState(0, FFI::addr($joystickState));

    // If the joystick state has changed, record the press
    if ($joystickState->Gamepad->wButtons !== $prevJoystickState) {
        // Get the current timestamp
        $currentTime = microtime(true) * 1000;

        // Calculate the duration of the previous press
        $pressDuration = $currentTime - $prevUpdateTime;

        // If the duration is greater than the threshold, record the press
        if ($prevJoystickState !== null && $pressDuration >= $pressDurationThreshold) {
            $joystickPresses[] = array(
                "Button" => $prevJoystickState,
                "Duration" => $pressDuration
            );
        }

        // Update the previous joystick state and timestamp
        $prevJoystickState = $joystickState->Gamepad->wButtons;
        $prevUpdateTime = $currentTime;
    }

    // Sleep for a short period of time to prevent the script from consuming too many resources
    usleep(10000); // in microseconds
}