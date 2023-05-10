import subprocess

def list_xinput_devices():
    cmd = "xinput list"
    process = subprocess.Popen(cmd.split(), stdout=subprocess.PIPE)
    output, error = process.communicate()
    return [device for device in output.decode().split('\n') if "joystick" in device.lower()]

def print_xinput_devices():
    devices = list_xinput_devices()
    if not devices:
        print("No joystick devices found")
    else:
        print("List of joystick devices:")
        for device in devices:
            print(device)

if __name__ == "__main__":
    print_xinput_devices()