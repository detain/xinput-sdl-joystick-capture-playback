<?php

// Define the FFI struct for the XINPUT_GAMEPAD structure
$ffi = FFI::cdef("
    typedef struct _XINPUT_GAMEPAD {
        unsigned short wButtons;
        unsigned char bLeftTrigger;
        unsigned char bRightTrigger;
        short sThumbLX;
        short sThumbLY;
        short sThumbRX;
        short sThumbRY;
    } XINPUT_GAMEPAD;
", "xinput1_4.dll");

// Define the FFI functions for the XInput API
$ffi->def("
    int XInputGetState(int dwUserIndex, void *pState);
    int XInputSetState(int dwUserIndex, void *pVibration);
");

// Define a function to read the button states from a JSON file
function readInput($inputFile) {
    // Read the button states from the input file
    $buttonStates = json_decode(file_get_contents($inputFile), true);
    // Return the button states array
    return $buttonStates;
}

// Define a function to play back the button states on an XInput device
function playBack($buttonStates, $deviceIndex) {
    // Open the XInput device with the specified index
    $device = $deviceIndex << 16;
    $state = FFI::new("XINPUT_GAMEPAD");
    $ret = FFI::XInputSetState($device, FFI::addr($state));
    // Loop through the button states and play back each one
    foreach ($buttonStates as $buttonState) {
        // Update the XInput state with the current button state
        $state->wButtons = $buttonState['wButtons'];
        $state->bLeftTrigger = $buttonState['bLeftTrigger'];
        $state->bRightTrigger = $buttonState['bRightTrigger'];
        $state->sThumbLX = $buttonState['sThumbLX'];
        $state->sThumbLY = $buttonState['sThumbLY'];
        $state->sThumbRX = $buttonState['sThumbRX'];
        $state->sThumbRY = $buttonState['sThumbRY'];
        // Send the updated XInput state to the device
        $ret = FFI::XInputSetState($device, FFI::addr($state));
        // Wait for a short time before playing the next button state
        usleep($buttonState['duration'] * 1000);
    }
    // Clear the XInput state when playback is complete
    $state->wButtons = 0;
    $state->bLeftTrigger = 0;
    $state->bRightTrigger = 0;
    $state->sThumbLX = 0;
    $state->sThumbLY = 0;
    $state->sThumbRX = 0;
    $state->sThumbRY = 0;
    $ret = FFI::XInputSetState($device, FFI::addr($state));
}

// Call the functions to read the button states from the input file and play them back on the XInput device with index 0
$buttonStates = readInput("input.json");
playBack($buttonStates, 0);
<?php
header('Content-Type: text/plain');
$ffi = FFI::cdef("
    typedef unsigned long XID;
    typedef struct {
        XID deviceid;
        int  detail;
        unsigned int flags;
        int  sourceid;
    } XIRawEvent;
    typedef struct {
        XIRawEvent *events;
        int       nevents;
        int       valuators[6];
    } XIRawEventCookie;
    typedef struct {
        int type;
        void* pad;
        unsigned long serial;
        Bool send_event;
        Display* display;
        int extension;
        int evtype;
        Time time;
        int deviceid;
        int sourceid;
        int detail;
        int flags;
        XID root;
        XID event;
        XID child;
        double root_x, root_y;
        double event_x, event_y;
        double delta_x, delta_y, delta_z;
        double rotation[4];
        double axis_data[4];
        double axis_data_unused[60];
    } XIEvent;

    void* XOpenDisplay(char*);
    void XCloseDisplay(void*);
    int XDefaultScreen(void*);
    unsigned long XAllPlanes(void);
    int XQueryExtension(void*,char*,int*,int*,int*);
    int XIQueryVersion(void*,int*,int*);
    XIRawEventCookie XIQueryDevice(void*, int, int*);
    int XFree(void*);
    int XFreeEventData(void*,XIRawEventCookie*);
    int XSendExtensionEvent(void*,void*,int,char*,XIEvent*);
", "libX11.so");

$display = $ffi->XOpenDisplay(null);
$screen = $ffi->XDefaultScreen($display);
$root = $ffi->XRootWindow($display, $screen);

$major = FFI::new("int");
$minor = FFI::new("int");

$result = $ffi->XIQueryVersion($display, $major, $minor);
if ($result != 0) {
    echo "XI version: " . $major[0] . "." . $minor[0] . "\n";
}

$json_data = file_get_contents("input.json");
$input_data = json_decode($json_data, true);

$device_id = null;

$devices_cookie = $ffi->XIQueryDevice($display, XIAllDevices, $num_devices);
$devices = FFI::cast("XID*", $devices_cookie->data);
for ($i = 0; $i < $devices_cookie->num_devices; $i++) {
    $info_cookie = $ffi->XIQueryDevice($display, $devices[$i], $num_classes);
    $info = FFI::cast("XIAnyClassInfo*", $info_cookie->data);

    if ($info->type == XITouchClass || $info->type == XIButtonClass) {
        $device_name = FFI::string($info->name);
        if ($device_name === $input_data["device_name"]) {
            $device_id = $devices[$i];
            break;
        }
    }

    $ffi->XFree($info_cookie);
}
$ffi->XFree($devices_cookie);

if ($device_id === null) {
    echo "Could not find device.\n";
    exit;
}

foreach ($input_data["keys"] as $keycode => $key_info) {
    $event = FFI::new("XIEvent");
    $event->type = GenericEvent;
    $event->send_event = 1;
    $event->display = $display;
    $event->extension = $ffi->XQueryExtension($display, "XInputExtension", null, null, null