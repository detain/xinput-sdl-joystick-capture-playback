import subprocess

def get_device_id(device_name):
    cmd = "xinput list"
    process = subprocess.Popen(cmd.split(), stdout=subprocess.PIPE)
    output, error = process.communicate()
    for line in output.decode().split('\n'):
        if device_name in line:
            return line.split('id=')[-1].split('\t')[0]
    return None

import json

def read_json_file(file_path):
    with open(file_path) as f:
        data = json.load(f)
    return data

import os
import time

def playback_device_input(device_id, actions):
    cmd = f"xinput test {device_id}"
    for action in actions:
        os.system(cmd)
        time.sleep(action["time"])
        for key in action["keys"]:
            os.system(f"xinput --set-prop {device_id} {key['id']} {key['value']}")
            time.sleep(0.1)

if __name__ == "__main__":
    device_name = "My Joystick Device"  # Replace with the name of your joystick device
    device_id = get_device_id(device_name)
    if not device_id:
        print(f"No device found with name '{device_name}'")
        exit()

    file_path = "joystick_actions.json"  # Replace with the path to your JSON file
    actions = read_json_file(file_path)

    playback_device_input(device_id, actions)