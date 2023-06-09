<?php
$ffi = FFI::cdef("
    typedef struct SDL_Joystick SDL_Joystick;
    typedef struct {
        int16_t x;
        int16_t y;
    } SDL_JoystickAxis;

    int SDL_Init(unsigned int flags);
    int SDL_NumJoysticks();
    const char *SDL_JoystickNameForIndex(int index);
    SDL_Joystick *SDL_JoystickOpen(int index);
    int SDL_JoystickNumAxes(SDL_Joystick *joystick);
    SDL_JoystickAxis SDL_JoystickGetAxis(SDL_Joystick *joystick, int axis);
    int SDL_JoystickGetButton(SDL_Joystick *joystick, int button);
    void SDL_JoystickClose(SDL_Joystick *joystick);
", "libSDL2.so");

$ffi->SDL_Init(0x00000008);

$joystick_index = 0; // Change to desired joystick index
$joystick_name = $ffi->SDL_JoystickNameForIndex($joystick_index);
$joystick = $ffi->SDL_JoystickOpen($joystick_index);

$recordings = json_decode(file_get_contents("joystick_recordings.json"), true);

foreach ($recordings as $recording) {
    $start_time = $recording["time"];

    $axis_values = $recording["axes"];
    for ($i = 0; $i < count($axis_values); $i++) {
        $axis = $ffi->SDL_JoystickGetAxis($joystick, $i);
        $axis->x = $axis_values[$i];
    }

    $button_values = $recording["buttons"];
    for ($i = 0; $i < count($button_values); $i++) {
        $button = $ffi->SDL_JoystickGetButton($joystick, $i);
        $button = $button_values[$i];
    }

    $end_time = microtime(true);
    $duration = $recording["duration"];

    $elapsed_time = microtime(true) - $start_time;

    if ($elapsed_time < $duration) {
        usleep(($duration - $elapsed_time) * 1000000);
    }
}

$ffi->SDL_JoystickClose($joystick);