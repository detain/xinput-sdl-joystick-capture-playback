function Get-XInputState($controllerIndex) {
    $xinputPath = "$env:windir\System32\xinput.dll"
    if (-not (Test-Path $xinputPath)) {
        throw "xinput.dll not found on this system"
    }
    Add-Type -Path $xinputPath
    return [XInput.Gamepad]::GetState($controllerIndex)
}

function Set-XInputState($controllerIndex, $state) {
    $xinputPath = "$env:windir\System32\xinput.dll"
    if (-not (Test-Path $xinputPath)) {
        throw "xinput.dll not found on this system"
    }
    Add-Type -Path $xinputPath
    [XInput.Gamepad]::SetState($controllerIndex, $state)
}

function Invoke-JoystickPlayback($controllerIndex, $jsonFilePath) {
    $json = Get-Content $jsonFilePath | ConvertFrom-Json
    $startTime = Get-Date
    foreach ($entry in $json) {
        $currentTime = (Get-Date) - $startTime
        $waitTime = [TimeSpan]::Parse($entry.Time) - $currentTime
        if ($waitTime.TotalMilliseconds -gt 0) {
            Start-Sleep -Milliseconds $waitTime.TotalMilliseconds
        }
        $state = [XInput.Gamepad]::new()
        $state.Buttons = [XInput.GamepadButtonFlags]$entry.Buttons
        $state.LeftTrigger = $entry.LeftTrigger
        $state.RightTrigger = $entry.RightTrigger
        $state.ThumbLX = $entry.ThumbLX
        $state.ThumbLY = $entry.ThumbLY
        $state.ThumbRX = $entry.ThumbRX
        $state.ThumbRY = $entry.ThumbRY
        Set-XInputState $controllerIndex $state
    }
}

Invoke-JoystickPlayback -controllerIndex 0 -jsonFilePath "C:\path\to\json\file.json"