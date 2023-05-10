function Read-JsonFile {
    param (
        [string]$Path
    )

    $json = Get-Content $Path -Raw | ConvertFrom-Json

    return $json
}

function Play-SdlJoystickActivity {
    param (
        [string]$Path,
        [int]$DeviceIndex
    )

    $activity = Read-JsonFile -Path $Path

    Add-Type -AssemblyName "Microsoft.LifeCam.SDL"

    $joystick = [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickOpen($DeviceIndex)

    foreach ($event in $activity) {
        Start-Sleep -Milliseconds $event.delay

        if ($event.eventType -eq "button") {
            [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickButton($joystick, $event.button, $event.value)
        }
        elseif ($event.eventType -eq "axis") {
            [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickAxis($joystick, $event.axis, $event.value)
        }
    }

    [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickClose($joystick)
}

function Convert-TimeToMilliseconds {
    param (
        [string]$Time
    )

    $timeSpan = [TimeSpan]::Parse($Time)

    return $timeSpan.TotalMilliseconds
}

function Record-SdlJoystickActivity {
    param (
        [string]$Path,
        [int]$DeviceIndex
    )

    Add-Type -AssemblyName "Microsoft.LifeCam.SDL"

    $joystick = [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickOpen($DeviceIndex)

    $activity = @()

    $previousEventTime = Get-Date

    while ($true) {
        $currentEventTime = Get-Date

        $deltaTime = ($currentEventTime - $previousEventTime).TotalMilliseconds

        $previousEventTime = $currentEventTime

        for ($i = 0; $i -lt [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickNumButtons($joystick); $i++) {
            $value = [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickGetButton($joystick, $i)

            $eventType = "button"
            $button = $i
            $axis = $null
            $delay = $deltaTime

            if ($value -ne 0) {
                $activity += @{
                    eventType = $eventType
                    button = $button
                    axis = $axis
                    value = $value
                    delay = $delay
                }
            }
        }

        for ($i = 0; $i -lt [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickNumAxes($joystick); $i++) {
            $value = [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickGetAxis($joystick, $i)

            $eventType = "axis"
            $button = $null
            $axis = $i
            $delay = $deltaTime

            if ($value -ne 0) {
                $activity += @{
                    eventType = $eventType
                    button = $button
                    axis = $axis
                    value = $value
                    delay = $delay
                }
            }
        }

        $activity | ConvertTo-Json | Out-File $Path

        Start-Sleep -Milliseconds 1
    }

    [Microsoft.LifeCam.SDL.SDL]::SDL_JoystickClose($joystick)
}