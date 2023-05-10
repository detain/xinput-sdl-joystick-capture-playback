# Import the XInput module
Import-Module XInput

# Create an array to store joystick press information
$joystickPresses = @()

# Create a variable to store the current state of the joystick
$prevJoystickState = $null

# Create a variable to store the timestamp of the previous update
$prevUpdateTime = [DateTime]::MinValue

# Set the duration threshold for a press
$pressDurationThreshold = [TimeSpan]::FromMilliseconds(50)

# Loop indefinitely
while ($true) {
    # Get the current state of the joystick
    $joystickState = Get-XInputState -UserIndex 0

    # If the joystick state has changed, record the press
    if ($joystickState -ne $prevJoystickState) {
        # Get the current timestamp
        $currentTime = Get-Date

        # Calculate the duration of the previous press
        $pressDuration = $currentTime - $prevUpdateTime

        # If the duration is greater than the threshold, record the press
        if ($prevJoystickState -and $pressDuration -ge $pressDurationThreshold) {
            $joystickPresses += @{
                "Button" = $prevJoystickState.Gamepad.Buttons
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