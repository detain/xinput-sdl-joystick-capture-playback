function Get-XinputJoystickDevices {
    $xinputPath = "$env:windir\System32\xinput.dll"
    if (-not (Test-Path $xinputPath)) {
        throw "xinput.dll not found on this system"
    }
    Add-Type -Path $xinputPath
    [XInput.Gamepad]::GetState(0) | Out-Null
    $controllers = 0..3 | ForEach-Object {
        try {
            [XInput.Gamepad]::GetState($_)
            "Controller $_"
        } catch {
            $null
        }
    }
    return $controllers | Where-Object { $_ -ne $null }
}

function Invoke-GetJoystickDevices {
    $joystickDevices = Get-XinputJoystickDevices
    if ($joystickDevices) {
        Write-Output "Joystick devices found:"
        $joystickDevices | ForEach-Object {
            Write-Output $_
        }
    } else {
        Write-Output "No joystick devices found."
    }
}

Invoke-GetJoystickDevices