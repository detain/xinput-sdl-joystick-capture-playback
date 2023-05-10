<?php

// Load the SDL library using FFI
$ffi = FFI::cdef('
    typedef struct SDL_Joystick SDL_Joystick;

    int SDL_Init(int flags);
    void SDL_Quit(void);

    int SDL_NumJoysticks(void);
    SDL_Joystick* SDL_JoystickOpen(int device_index);
    void SDL_JoystickClose(SDL_Joystick* joystick);

    int SDL_JoystickGetNumButtons(SDL_Joystick* joystick);
    int SDL_JoystickGetButton(SDL_Joystick* joystick, int button);

    typedef struct timeval {
        long tv_sec;
        long tv_usec;
    } timeval;

    int gettimeofday(struct timeval* tv, void* tz);
', 'libSDL2.so');

// Initialize SDL
$ffi->SDL_Init(0);

// Create an array to store joystick press information
$joystickPresses = array();

// Create a variable to store the current state of the joystick
$prevJoystickState = null;

// Create a variable to store the timestamp of the previous update
$prevUpdateTime = PHP_INT_MIN;

// Set the duration threshold for a press
$pressDurationThreshold = 50; // in milliseconds

// Open the first joystick
$joystick = $ffi->SDL_JoystickOpen(0);

// Loop indefinitely
while (true) {
    // Get the current state of the joystick
    $joystickState = 0;

    for ($button = 0; $button < $ffi->SDL_JoystickGetNumButtons($joystick); $button++) {
        $joystickState |= ($ffi->SDL_JoystickGetButton($joystick, $button) << $button);
    }

    // If the joystick state has changed, record the press
    if ($joystickState !== $prevJoystickState) {
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
        $prevJoystickState = $joystickState;
        $prevUpdateTime = $currentTime;
    }

    // Sleep for a short period of time to prevent the script from consuming too many resources
    usleep(10000); // in microseconds
}

// Close the joystick and quit SDL when the script is terminated
$ffi->SDL_JoystickClose($joystick);
$ffi->SDL_Quit();