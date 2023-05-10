function Get-SDLJoystickDevices {
    $sdlPath = "C:\Windows\System32\SDL2.dll"
    if (-not (Test-Path $sdlPath)) {
        throw "SDL2.dll not found on this system"
    }
    Add-Type -Path $sdlPath
    $numJoysticks = [SDL2.SDL]::NumJoysticks()
    for ($i = 0; $i -lt $numJoysticks; $i++) {
        Write-Output ([SDL2.SDL]::JoystickNameForIndex($i))
    }
}

Get-SDLJoystickDevices