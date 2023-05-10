def list_devices
    `xinput list | grep -i joystick`
  end

  def parse_devices(device_list)
    devices = []
    device_list.split("\n").each do |line|
      if line =~ /id=(\d+)/
        id = $1
        if line =~ /(.+)$/i
          name = $1.strip
          devices << {id: id, name: name}
        end
      end
    end
    devices
  end


  def main
    device_list = list_devices
    devices = parse_devices(device_list)
    devices.each do |device|
      puts "Joystick device '#{device[:name]}' (ID: #{device[:id]})"
    end
  end