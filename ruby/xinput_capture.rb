def get_device_id
    print "Enter the ID of the joystick device: "
    gets.chomp
  end

  def record_input(device_id)
    `xinput test #{device_id}`
  end

  def main
    device_id = get_device_id
    record_input(device_id)
  end