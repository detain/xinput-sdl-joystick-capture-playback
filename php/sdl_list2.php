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
    void SDL_JoystickClose(SDL_Joystick *joystick);
", "libSDL2.so");

$ffi->SDL_Init(0x00000008);

$num_joysticks = $ffi->SDL_NumJoysticks();
echo "Number of Joysticks: " . $num_joysticks . "\n";

for ($i = 0; $i < $num_joysticks; $i++) {
    $name = $ffi->SDL_JoystickNameForIndex($i);
    $joystick = $ffi->SDL_JoystickOpen($i);
    $num_axes = $ffi->SDL_JoystickNumAxes($joystick);

    echo "Joystick " . $i . ": " . $name . "\n";
    echo "Number of Axes: " . $num_axes . "\n";

    $ffi->SDL_JoystickClose($joystick);
}