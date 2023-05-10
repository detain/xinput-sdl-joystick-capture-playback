def list_devices
    `sdl2-jstest --list | grep -o '/dev/input/js.'`
  end

  def parse_devices(device_list)
    devices = []
    device_list.split("\n").each do |line|
      if line =~ /js(\d+)/
        id = $1
        devices << {id: id}
      end
    end
    devices
  end

  def main
    device_list = list_devices
    devices = parse_devices(device_list)
    devices.each do |device|
      puts "Joystick device '/dev/input/js#{device[:id]}'"
    end
  end