function Get-SDLJoystickState($joystickIndex) {
    $sdlPath = "C:\Windows\System32\SDL2.dll"
    if (-not (Test-Path $sdlPath)) {
        throw "SDL2.dll not found on this system"
    }
    Add-Type -Path $sdlPath
    $state = [SDL2.SDL]::JoystickGetAxis($joystickIndex, 0), [SDL2.SDL]::JoystickGetAxis($joystickIndex, 1), [SDL2.SDL]::JoystickGetAxis($joystickIndex, 2), [SDL2.SDL]::JoystickGetAxis($joystickIndex, 3), [SDL2.SDL]::JoystickGetButton($joystickIndex, 0), [SDL2.SDL]::JoystickGetButton($joystickIndex, 1), [SDL2.SDL]::JoystickGetButton($joystickIndex, 2), [SDL2.SDL]::JoystickGetButton($joystickIndex, 3), [SDL2.SDL]::JoystickGetButton($joystickIndex, 4), [SDL2.SDL]::JoystickGetButton($joystickIndex, 5), [SDL2.SDL]::JoystickGetButton($joystickIndex, 6), [SDL2.SDL]::JoystickGetButton($joystickIndex, 7), [SDL2.SDL]::JoystickGetButton($joystickIndex, 8), [SDL2.SDL]::JoystickGetButton($joystickIndex, 9), [SDL2.SDL]::JoystickGetButton($joystickIndex, 10), [SDL2.SDL]::JoystickGetButton($joystickIndex, 11), [SDL2.SDL]::JoystickGetButton($joystickIndex, 12), [SDL2.SDL]::JoystickGetButton($joystickIndex, 13), [SDL2.SDL]::JoystickGetButton($joystickIndex, 14), [SDL2.SDL]::JoystickGetButton($joystickIndex, 15)
    return $state
}

function Record-SDLJoystickActivity($joystickIndex, $outputFilePath) {
    $sdlPath = "C:\Windows\System32\SDL2.dll"
    if (-not (Test-Path $sdlPath)) {
        throw "SDL2.dll not found on this system"
    }
    Add-Type -Path $sdlPath
    $state = Get-SDLJoystickState $joystickIndex
    $startTime = Get-Date
    $activity = @()
    while ($true) {
        $currentTime = (Get-Date) - $startTime
        $entry = [ordered]@{
            "Time" = "{0:hh\:mm\:ss\.fff}" -f $currentTime
            "Axis1" = [SDL2.SDL]::JoystickGetAxis($joystickIndex, 0)
            "Axis2" = [SDL2.SDL]::JoystickGetAxis($joystickIndex, 1)
            "Axis3" = [SDL2.SDL]::JoystickGetAxis($joystickIndex, 2)
            "Axis4" = [SDL2.SDL]::JoystickGetAxis($joystickIndex, 3)
            "Button1" = [SDL2.SDL]::JoystickGetButton($joystickIndex, 0)

function Record-SdlInput {
    Param(
        [int]$deviceIndex,
        [string]$outputFilePath
    )

    $events = @()

    Write-Host "Recording input from device $deviceIndex. Press Ctrl+C to stop."

    while($true) {
        $state = Get-SdlJoystickState -deviceIndex $deviceIndex
        $buttons = $state.buttons

        foreach($button in $buttons) {
            $buttonState = if($button -eq 'True') {'down'} else {'up'}
            $events += @{
                'button' = $button
                'state' = $buttonState
                'timestamp' = [System.DateTimeOffset]::Now.ToUnixTimeMilliseconds()
            }
        }

        $events | ConvertTo-Json | Set-Content $outputFilePath
    }
}

function Get-SdlJoystickState {
    Param(
        [int]$deviceIndex
    )

    $joystick = [SdlDotNet.Input.Joystick]::Open($deviceIndex)

    $axes = $joystick.Axes
    $buttons = $joystick.Buttons

    $axisState = @{}
    foreach($axis in $axes) {
        $axisState[$axis.Name] = $axis.Position
    }

    $buttonState = @{}
    foreach($button in $buttons) {
        $buttonState[$button.Name] = $button.Pressed
    }

    $joystick.Close()

    return @{
        'axes' = $axisState
        'buttons' = $buttonState
    }
}

Add-Type -Path "C:\Program Files (x86)\SdlDotNet\sdl.dll"
Add-Type -Path "C:\Program Files (x86)\SdlDotNet\sdl-dotnet.dll"

function Get-SdlJoystickCount {
    return [SdlDotNet.Input.Joystick]::NumberConnected
}

function Get-SdlJoystickList {
    $count = Get-SdlJoystickCount
    $joysticks = @()

    for($i = 0; $i -lt $count; $i++) {
        $joystick = [SdlDotNet.Input.Joystick]::Open($i)
        $joysticks += @{
            'index' = $i
            'name' = $joystick.Name
        }
        $joystick.Close()
    }

    return $joysticks
}

function Record-SdlInput {
    Param(
        [int]$deviceIndex,
        [string]$outputFilePath
    )

    $events = @()
    $buttonStartTimes = @{}

    Write-Host "Recording input from device $deviceIndex. Press Ctrl+C to stop."

    while($true) {
        $state = Get-SdlJoystickState -deviceIndex $deviceIndex
        $buttons = $state.buttons

        foreach($button in $buttons.GetEnumerator()) {
            $buttonName = $button.Name
            $buttonState = if($button.Pressed) {'down'} else {'up'}
            $timestamp = [System.DateTimeOffset]::Now.ToUnixTimeMilliseconds()

            if($buttonState -eq 'down' -and -not $buttonStartTimes.ContainsKey($buttonName)) {
                $buttonStartTimes[$buttonName] = $timestamp
            }

            if($buttonState -eq 'up' -and $buttonStartTimes.ContainsKey($buttonName)) {
                $startTime = $buttonStartTimes[$buttonName]
                $duration = $timestamp - $startTime
                $events += @{
                    'button' = $buttonName
                    'state' = 'down'
                    'duration' = $duration
                    'timestamp' = $startTime
                }
                $events += @{
                    'button' = $buttonName
                    'state' = 'up'
                    'duration' = $duration
                    'timestamp' = $timestamp
                }
                $buttonStartTimes.Remove($buttonName)
            }

            $events += @{
                'button' = $buttonName
                'state' = $buttonState
                'timestamp' = $timestamp
            }
        }

        $events | ConvertTo-Json | Set-Content $outputFilePath
    }
}

function Get-SdlJoystickState {
    Param(
        [int]$deviceIndex
    )

    $joystick = [SdlDotNet.Input.Joystick]::Open($deviceIndex)

    $axes = $joystick.Axes
    $buttons = $joystick.Buttons

    $axisState = @{}
    foreach($axis in $axes) {
        $axisState[$axis.Name] = $axis.Position
    }

    $buttonState = @{}
    foreach($button in $buttons) {
        $buttonState[$button.Name] = $button.Pressed
    }

    $joystick.Close()

    return @{
        'axes' = $axisState
        'buttons' = $buttonState
    }
}