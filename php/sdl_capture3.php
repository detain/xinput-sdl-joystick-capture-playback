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

$num_axes = $ffi->SDL_JoystickNumAxes($joystick);
$num_buttons = $ffi->SDL_JoystickNumButtons($joystick);

$recordings = array();

while (true) {
    $start_time = microtime(true);
    $axis_values = array();
    for ($i = 0; $i < $num_axes; $i++) {
        $axis = $ffi->SDL_JoystickGetAxis($joystick, $i);
        $axis_values[$i] = $axis->x;
    }

    $button_values = array();
    for ($i = 0; $i < $num_buttons; $i++) {
        $button = $ffi->SDL_JoystickGetButton($joystick, $i);
        $button_values[$i] = $button;
    }

    $end_time = microtime(true);
    $duration = $end_time - $start_time;

    $recording = array(
        "time" => $start_time,
        "duration" => $duration,
        "axes" => $axis_values,
        "buttons" => $button_values
    );

    $recordings[] = $recording;

    usleep(1000);
}

$ffi->SDL_JoystickClose($joystick);

$json_data = json_encode($recordings);
file_put_contents("joystick_recordings.json", $json_data);