<?php

$f = FFI::cdef("
    typedef unsigned long XID;
    typedef struct{XID w;int d;}XDeviceKeyEvent;
    typedef struct{XID w;int d;}XDeviceMotionEvent;
    typedef struct{XID w;int d;}XDeviceButtonEvent;
    typedef struct{XID w;int d;}XDevicePresenceNotifyEvent;
    typedef struct{XID w;int d;}XDeviceFocusChangeEvent;
    typedef struct{XID w;int d;}XDeviceStateNotifyEvent;
    typedef struct{XID w;int d;}XDeviceMappingEvent;
    typedef struct{XID w;int d;}XInputClassEvent;
    typedef union{XDeviceKeyEvent k;XDeviceMotionEvent m;XDeviceButtonEvent b;XDevicePresenceNotifyEvent p;XDeviceFocusChangeEvent f;XDeviceStateNotifyEvent s;XDeviceMappingEvent g;XInputClassEvent c;}XAnyInputEvent;
    typedef struct{int id;char*n,*t;}XIDeviceInfo;
    typedef XIDeviceInfo*XIDeviceInfoPtr;
    typedef struct{unsigned char d[32];}XEvent;
    typedef struct{int d;int n;XInputClassInfoPtr c;}XIDeviceInfo_v1;
    typedef struct{int i;int e;}XInputClassInfo;
    typedef struct{int t;XAnyInputEvent e;}XGenericEventCookie;
    typedef struct{int d;int n;XInputClassInfoPtr c;}XIDeviceInfo_v2;
    typedef XIDeviceInfo_v2*XIDeviceInfoPtr_v2;
    Display*XOpenDisplay(char*);
    int XCloseDisplay(Display*);
    int XIQueryDevice(Display*,int,XIDeviceInfoPtr*,int*);
    void XIFreeDeviceInfo(XIDeviceInfoPtr*);
    int XIQueryVersion(Display*,int*,int*);
    int XGetEventData(Display*,XGenericEventCookie*);
    void XFreeEventData(Display*,XGenericEventCookie*);
    char*XGetAtomName(Display*,int);
", "libX11.so.6");

$display = $f->XOpenDisplay(null);

if (!$display) {
    exit(1);
}

$major = $f->new("int");
$minor = $f->new("int");

if (!$f->XIQueryVersion($display, $major, $minor)) {
    exit(1);
}

$count = $f->new("int");
$info = $f->new("XIDeviceInfoPtr");

if (!$f->XIQueryDevice($display, XIAllDevices, $info, $count)) {
    exit(1);
}

$device_id = 0; // Change this to the device ID you want to record

foreach ($info as $device) {
    if ($device->id == $device_id) {
        $device_name = $device->n;
        break;
    }
}

$f->XIFreeDeviceInfo($info);

$keys = array();
$last_event_time = null;
$event_count = 0;

while (true) {
    $cookie = $f->new("XGenericEventCookie");
    $f->XGetEventData($display, $cookie);

    $event = $cookie->e;

    if ($event->c.b.type == XI_ButtonPress) {
        $event_time = $event->c.b.time;
        $keycode = $event->c.b.detail;

        if (!isset($keys[$keycode])) {
            $keys[$keycode] = array(
                "start_time" => $event_time,
                "end_time" => null,
                "duration" => null,
                "count" => 0,
                "interval" => null
            );
        }

        if ($last_event_time !== null) {
            $keys[$keycode]["interval"] = $event_time - $last_event_time;
        }

        $last_event_time = $event_time;
        $keys[$keycode]["count"]++;

    } elseif ($event->c.b.type == XI_ButtonRelease) {
        $event_time = $event->c.b.time;
        $keycode = $event->c.b.detail;

        if (isset($keys[$keycode])) {
            $keys[$keycode]["end_time"] = $event_time;
            $keys[$keycode]["duration"] = $event_time - $keys[$keycode]["start_time"];

            $last_event_time = $event_time;
            $event_count++;

            if ($event_count >= 10) {
                $data = array(
                    "device_name" => $device_name,
                    "keys" => $keys
                );

                file_put_contents("input.json", json_encode($data, JSON_PRETTY_PRINT));

                $event_count = 0;
            }
        }
    }

    $f->XFreeEventData($display, $cookie);
}

$f->XCloseDisplay($display);
