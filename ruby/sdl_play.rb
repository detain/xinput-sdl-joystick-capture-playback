def get_device_id
    print "Enter the ID of the joystick device: "
    gets.chomp
  end

  require 'json'

def read_input_file(input_file)
  File.read(input_file)
end

def playback_input(device_id, events)
    events.each do |event|
      `sdl2-jstest --event #{device_id} --#{event['type']} #{event['value']}`
      sleep(event['duration'])
    end
  end

  def main
    input_file = ARGV[0]
    device_id = get_device_id
    events = JSON.parse(read_input_file(input_file))
    playback_input(device_id, events)
  end