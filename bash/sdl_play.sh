#!/bin/bash

function get_joystick_id() {
  SDL_JOYSTICK_DEVICES=/dev/input/js*
  ls $SDL_JOYSTICK_DEVICES | cut -d/ -f3 | head -1
}

function play_input() {
  id=$(get_joystick_id)
  input_file=$1
  events=$(cat $input_file)
  for event in $events
  do
    echo "$event" > /dev/input/$id
    sleep 0.001
  done
}

play_input $1