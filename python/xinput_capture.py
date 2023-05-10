import subprocess

def get_device_id(device_name):
    cmd = "xinput list"
    process = subprocess.Popen(cmd.split(), stdout=subprocess.PIPE)
    output, error = process.communicate()
    for line in output.decode().split('\n'):
        if device_name in line:
            return line.split('id=')[-1].split('\t')[0]
    return None

import os

def record_device_input(device_id):
    cmd = f"xinput test {device_id}"
    os.system(cmd)

if __name__ == "__main__":
    device_name = "My Joystick Device"  # Replace with the name of your joystick device
    device_id = get_device_id(device_name)
    if device_id:
        record_device_input(device_id)
    else:
        print(f"No device found with name '{device_name}'")
