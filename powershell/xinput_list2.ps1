Add-Type -Name "Win32Joystick" -Namespace "Win32" -MemberDefinition '
[DllImport("winmm.dll")]
public static extern int joyGetNumDevs();
'
$joyCount = [Win32.Win32Joystick]::joyGetNumDevs()
for ($i = 0; $i -lt $joyCount; $i++) {
    $joyCaps = New-Object System.IntPtr
    $ret = [Win32.Win32API]::joyGetDevCapsW($i, $joyCaps, [System.Runtime.InteropServices.Marshal]::SizeOf([Win32.Win32API+JOYCAPSW]))
    if ($ret -eq 0) {
        $joyInfo = [System.Runtime.InteropServices.Marshal]::PtrToStructure($joyCaps, [Win32.Win32API+JOYCAPSW])
        if ($joyInfo.szPname -match "joystick") {
            Write-Output "Joystick Device ID: $i"
        }
    }
}
if ($joyCount -eq 0) {
    Write-Output "No joysticks found"
}