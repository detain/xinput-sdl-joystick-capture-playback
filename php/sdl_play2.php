<?php
// Define SDL constants
define('SDL_INIT_JOYSTICK', 0x00000200);
define('SDL_JOYSTICK_AXIS_MAX', 32767);

// Load SDL2 library with FFI
$ffi = FFI::load('libSDL2.so');

// Initialize SDL2 Joystick subsystem
$ffi->SDL_InitSubSystem(SDL_INIT_JOYSTICK);

// Read the joystick events from the JSON file
$json_data = file_get_contents('joystick_events.json');
$joystick_events = json_decode($json_data, true);

// Open the SDL joystick device
$joystick = $ffi->SDL_JoystickOpen(0);

// Set the joystick to be in non-blocking mode
$ffi->SDL_JoystickEventState(SDL_IGNORE);

// Play back the joystick events
foreach ($joystick_events as $event) {
    $event_type = $event['type'];
    $event_value = $event['value'];
    $event_time = $event['time'];

    // Sleep for the appropriate amount of time before sending the next event
    usleep($event_time * 1000);

    // Send the appropriate joystick event
    switch ($event_type) {
        case 'axis':
            $axis_id = $event_value['id'];
            $axis_value = $event_value['value'];
            $ffi->SDL_JoystickAxis($joystick, $axis_id, $axis_value * SDL_JOYSTICK_AXIS_MAX);
            break;
        case 'button':
            $button_id = $event_value['id'];
            $button_value = $event_value['value'];
            $ffi->SDL_JoystickButton($joystick, $button_id, $button_value);
            break;
    }
}

// Close the SDL joystick device
$ffi->SDL_JoystickClose($joystick);

// Quit the SDL2 Joystick subsystem
$ffi->SDL_QuitSubSystem();