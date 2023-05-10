import json

def read_json_file(file_path):
    with open(file_path, 'r') as f:
        data = json.load(f)
    return data

import sdl2
import time

def playback_sdl_input(device_index, actions):
    sdl2.SDL_Init(sdl2.SDL_INIT_JOYSTICK)
    joystick = sdl2.joystick.SDL_JoystickOpen(device_index)
    for action in actions:
        if action["type"] == "button":
            sdl2.joystick.SDL_JoystickButton(joystick, action["button"], action["state"])
        elif action["type"] == "axis":
            sdl2.joystick.SDL_JoystickAxis(joystick, action["axis"], action["value"])
        elif action["type"] == "hat":
            sdl2.joystick.SDL_JoystickHat(joystick, action["hat"], action["value"])
        time.sleep(action["time"])
    sdl2.joystick.SDL_JoystickClose(joystick)
    sdl2.SDL_Quit()

if __name__ == "__main__":
    device_index = 0  # Replace with the index of your joystick device
    file_path = "joystick_actions.json"  # Replace with the path of the JSON file containing the actions
    actions = read_json_file(file_path)
    playback_sdl_input(device_index, actions)