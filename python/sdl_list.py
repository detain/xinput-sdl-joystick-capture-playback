import sdl2

def list_sdl_devices():
    sdl2.SDL_Init(sdl2.SDL_INIT_JOYSTICK)
    num_joysticks = sdl2.joystick.SDL_NumJoysticks()
    devices = []
    for i in range(num_joysticks):
        name = sdl2.joystick.SDL_JoystickNameForIndex(i).decode()
        devices.append(name)
    sdl2.SDL_Quit()
    return devices

def print_sdl_devices():
    devices = list_sdl_devices()
    if not devices:
        print("No joystick devices found")
    else:
        print("List of joystick devices:")
        for device in devices:
            print(device)

if __name__ == "__main__":
    print_sdl_devices()