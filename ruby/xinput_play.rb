require 'json'

def read_input_file(input_file)
  JSON.parse(File.read(input_file))
end

def playback_input(device_id, events)
    events.each do |event|
      `xinput test #{device_id} #{event['type']} #{event['code']} #{event['value']}`
      sleep(event['time'])
    end
  end

  def get_device_id
    print "Enter the ID of the joystick device: "
    gets.chomp
  end

  def main
    input_file = ARGV[0]
    device_id = get_device_id
    events = read_input_file(input_file)
    playback_input(device_id, events)
  end