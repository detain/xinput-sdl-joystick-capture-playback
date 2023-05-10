def get_device_id
    print "Enter the ID of the joystick device: "
    gets.chomp
  end

  require 'json'

def record_input(device_id)
  events = []
  last_time = Time.now
  while true
    output = `sdl2-jstest --event #{device_id}`
    events_data = output.scan(/Event: \d+ (\w+): (\d+)/)
    events_data.each do |event_data|
      type, value = event_data
      event_time = Time.now
      duration = event_time - last_time
      last_time = event_time
      events << {
        'type' => type,
        'value' => value.to_i,
        'duration' => duration,
        'time_since_last' => event_time - events.last['time'] if events.last
      }
    end
    sleep(0.01)
  end
  events.to_json
end

def write_output_file(output_file, events)
    File.open(output_file, 'w') do |f|
      f.write(events)
    end
  end

  def main
  device_id = get_device_id
  events = record_input(device_id)
  output_file = "#{device_id}.json"
  write_output_file(output_file, events)
end