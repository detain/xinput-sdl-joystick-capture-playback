<?php

// Load the libX11 library using FFI
$ffi = FFI::cdef("
    typedef unsigned long XID;
    typedef struct {
        XID window;
        int deviceid;
    } XDeviceKeyEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDeviceMotionEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDeviceButtonEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDevicePresenceNotifyEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDeviceFocusChangeEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDeviceStateNotifyEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XDeviceMappingEvent;

    typedef struct {
        XID window;
        int deviceid;
    } XInputClassEvent;

    typedef union {
        XDeviceKeyEvent key;
        XDeviceMotionEvent motion;
        XDeviceButtonEvent button;
        XDevicePresenceNotifyEvent presence;
        XDeviceFocusChangeEvent focus;
        XDeviceStateNotifyEvent state;
        XDeviceMappingEvent mapping;
        XInputClassEvent class;
    } XAnyInputEvent;

    typedef struct {
        int device_id;
        char *name;
        char *type;
    } XIDeviceInfo;

    typedef XIDeviceInfo *XIDeviceInfoPtr;

    typedef struct {
        unsigned char data[32];
    } XEvent;

    typedef struct {
        int deviceid;
        int num_classes;
        XInputClassInfoPtr classes;
    } XIDeviceInfo_v1;

    typedef struct {
        int class_id;
        int event_type_base;
    } XInputClassInfo;

    typedef struct {
        int type;
        XAnyInputEvent event;
    } XGenericEventCookie;

    typedef struct {
        int deviceid;
        int num_classes;
        XInputClassInfoPtr classes;
    } XIDeviceInfo_v2;

    typedef XIDeviceInfo_v2 *XIDeviceInfoPtr_v2;

    Display *XOpenDisplay(char *);
    int XCloseDisplay(Display *);

    int XIQueryDevice(Display *, int, XIDeviceInfoPtr *, int *);
    void XIFreeDeviceInfo(XIDeviceInfoPtr *);

    int XIQueryVersion(Display *, int *, int *);
    int XGetEventData(Display *, XGenericEventCookie *);
    void XFreeEventData(Display *, XGenericEventCookie *);

    char *XGetAtomName(Display *, int);
", "libX11.so.6");

// Open a connection to the X server
$display = $ffi->XOpenDisplay(null);
if (!$display) {
    echo "Error opening display\n";
    exit(1);
}

// Query the XInput extension version
$major = $ffi->new("int");
$minor = $ffi->new("int");
if (!$ffi->XIQueryVersion($display, $major, $minor)) {
    echo "XInput extension not available\n";
    exit(1);
}

// Query the list of XInput devices
$deviceCount = $ffi->new("int");
$deviceInfo = $ffi->new("XIDeviceInfoPtr");
if (!$ffi->XIQueryDevice($display, XIAllDevices, $deviceInfo, $deviceCount)) {
    echo "Error querying XInput devices\n";
    exit(1);
}

// Loop through the devices and print their names
for ($k=0; $k < $deviceCount[0]; $k++){
    $x=$deviceInfo[0].t;
    if(strpos($x,'joystick')!==false) {
        echo"Joystick Device ID: {$deviceInfo[$k].id}\n";
        $j++;
    }
}
if($j===0) {
    echo"No joysticks found\n";
}
