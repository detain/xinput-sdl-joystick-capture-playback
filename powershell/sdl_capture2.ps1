# Import the SDL2 module
Add-Type -Path "C:\Path\To\SDL2.dll"
$SDL = New-Object SDL2.SDL

# Create an array to store joystick press information
$joystickPresses = @()

# Create a variable to store the current state of the joystick
$prevJoystickState = $null

# Create a variable to store the timestamp of the previous update
$prevUpdateTime = [DateTime]::MinValue

# Set the duration threshold for a press
$pressDurationThreshold = [TimeSpan]::FromMilliseconds(50)

# Initialize SDL
$SDL.SDL_Init([SDL2.SDL_INIT_JOYSTICK])

# Open the first joystick
$joystick = $SDL.SDL_JoystickOpen(0)

# Loop indefinitely
while ($true) {
    # Get the current state of the joystick
    $joystickState = $SDL.SDL_JoystickGetButton($joystick, 0)

    # If the joystick state has changed, record the press
    if ($joystickState -ne $prevJoystickState) {
        # Get the current timestamp
        $currentTime = Get-Date

        # Calculate the duration of the previous press
        $pressDuration = $currentTime - $prevUpdateTime

        # If the duration is greater than the threshold, record the press
        if ($prevJoystickState -and $pressDuration -ge $pressDurationThreshold) {
            $joystickPresses += @{
                "Button" = 0
                "Duration" = $pressDuration
            }
        }

        # Update the previous joystick state and timestamp
        $prevJoystickState = $joystickState
        $prevUpdateTime = $currentTime
    }

    # Sleep for a short period of time to prevent the script from consuming too many resources
    Start-Sleep -Milliseconds 10
}

# Close the joystick and quit SDL
$SDL.SDL_JoystickClose($joystick)
$SDL.SDL_Quit()