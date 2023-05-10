import sdl2
import time

def record_sdl_input(device_index):
    sdl2.SDL_Init(sdl2.SDL_INIT_JOYSTICK)
    joystick = sdl2.joystick.SDL_JoystickOpen(device_index)
    num_axes = sdl2.joystick.SDL_JoystickNumAxes(joystick)
    num_buttons = sdl2.joystick.SDL_JoystickNumButtons(joystick)
    start_time = time.time()
    actions = []
    while True:
        for event in sdl2.ext.get_events():
            if event.type == sdl2.SDL_JOYBUTTONDOWN:
                action = {"type": "button", "button": event.jbutton.button, "state": True, "time": time.time()-start_time}
                actions.append(action)
            elif event.type == sdl2.SDL_JOYBUTTONUP:
                action = {"type": "button", "button": event.jbutton.button, "state": False, "time": time.time()-start_time}
                actions.append(action)
            elif event.type == sdl2.SDL_JOYAXISMOTION:
                axis = event.jaxis.axis
                value = event.jaxis.value
                action = {"type": "axis", "axis": axis, "value": value, "time": time.time()-start_time}
                actions.append(action)
            elif event.type == sdl2.SDL_JOYHATMOTION:
                hat = event.jhat.hat
                value = event.jhat.value
                action = {"type": "hat", "hat": hat, "value": value, "time": time.time()-start_time}
                actions.append(action)
            elif event.type == sdl2.SDL_QUIT:
                sdl2.joystick.SDL_JoystickClose(joystick)
                sdl2.SDL_Quit()
                return actions

import json

def write_json_file(file_path, data):
    with open(file_path, 'w') as f:
        json.dump(data, f)

if __name__ == "__main__":
    device_index = 0  # Replace with the index of your joystick device
    actions = record_sdl_input(device_index)
    file_path = "joystick_actions.json"  # Replace with the desired path for the output JSON file
    write_json_file(file_path, actions)