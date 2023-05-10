<?php $f=FFI::cdef("
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
char*XGetAtomName(Display*,int);","libX11.so.6");

$d=$f->XOpenDisplay(null);
if(!$d){exit(1);}
$m=$f->new("int");
$n=$f->new("int");
if(!$f->XIQueryVersion($d,$m,$n)){exit(1);}
$c=$f->new("int");
$i=$f->new("XIDeviceInfoPtr");
if(!$f->XIQueryDevice($d,XIAllDevices,$i,$c)){exit(1);}
$j=0;
for($k=0;$k<$c[0];$k++){
    $x=$i[0].t;
    if(strpos($x,'joystick')!==false){
        echo"Joystick Device ID: {$i[$k].id}\n";
        $j++;
    }
}
if($j===0){echo"No joysticks found\n";}
?>