<?php

// Define the necessary functions for FFI module
$ffi = FFI::cdef(
    "typedef void* xinput_state;
    typedef int BOOL;
    typedef unsigned int UINT;
    typedef unsigned char BYTE;
    typedef unsigned short WORD;
    typedef unsigned long DWORD;
    typedef struct _XINPUT_GAMEPAD {
        WORD wButtons;
        BYTE bLeftTrigger;
        BYTE bRightTrigger;
        SHORT sThumbLX;
        SHORT sThumbLY;
        SHORT sThumbRX;
        SHORT sThumbRY;
    } XINPUT_GAMEPAD;
    typedef struct _XINPUT_STATE {
        DWORD dwPacketNumber;
        XINPUT_GAMEPAD Gamepad;
    } XINPUT_STATE;
    typedef DWORD(*PFN_XInputGetState)(DWORD dwUserIndex, XINPUT_STATE* pState);
    const int XINPUT_MAX_CONTROLLERS = 4;
    const int XINPUT_GAMEPAD_DPAD_UP = 0x0001;
    const int XINPUT_GAMEPAD_DPAD_DOWN = 0x0002;
    const int XINPUT_GAMEPAD_DPAD_LEFT = 0x0004;
    const int XINPUT_GAMEPAD_DPAD_RIGHT = 0x0008;
    const int XINPUT_GAMEPAD_START = 0x0010;
    const int XINPUT_GAMEPAD_BACK = 0x0020;
    const int XINPUT_GAMEPAD_LEFT_THUMB = 0x0040;
    const int XINPUT_GAMEPAD_RIGHT_THUMB = 0x0080;
    const int XINPUT_GAMEPAD_LEFT_SHOULDER = 0x0100;
    const int XINPUT_GAMEPAD_RIGHT_SHOULDER = 0x0200;
    const int XINPUT_GAMEPAD_A = 0x1000;
    const int XINPUT_GAMEPAD_B = 0x2000;
    const int XINPUT_GAMEPAD_X = 0x4000;
    const int XINPUT_GAMEPAD_Y = 0x8000;
    BOOL XInputGetState(DWORD dwUserIndex, XINPUT_STATE* pState);",
    "xinput.dll");

// Define the function to record input
function recordInput($deviceNumber, $outputFile) {
    global $ffi;
    $state = $ffi->new("XINPUT_STATE");
    $buttonStates = array();
    while (true) {
        // Get the current state of the XInput device
        $result = $ffi->XInputGetState($deviceNumber, $ffi->addr($state));
        if ($result != 0) {
            break;
        }
        // Check if any buttons are currently pressed
        $buttonMask = $state->Gamepad->wButtons;
        if ($buttonMask > 0) {
            // Record the start time of the button press
            $startTime = microtime(true);
            // Wait for the button to be released
            while ($ffi->XInputGetState($deviceNumber, $ffi->addr($state)) == 0 && $state->Gamepad->wButtons == $buttonMask) {
                usleep(1000);
            }
            // Record the end time of the button press
            $endTime = microtime(true);
            // Calculate the duration of the button press
            $duration = $endTime - $startTime;
            // Add the button press data to the
// buttonStates array
$buttonStates[] = array(
    "buttonMask" => $buttonMask,
    "duration" => $duration
);
}
// Wait for a short time before checking the device state again
usleep(1000);
}
// Write the buttonStates array to the output file as JSON
file_put_contents($outputFile, json_encode($buttonStates));