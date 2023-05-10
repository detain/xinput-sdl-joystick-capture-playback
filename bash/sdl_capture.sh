#!/bin/bash

function get_joystick_id() {
  SDL_JOYSTICK_DEVICES=/dev/input/js*
  ls $SDL_JOYSTICK_DEVICES | cut -d/ -f3 | head -1
}

function record_input() {
  id=$(get_joystick_id)
  input_file=$(date +"%Y%m%d-%H%M%S").json
  echo "[" > $input_file
  while true; do
    event=$(timeout 1s jstest --event /dev/input/$id)
    if [ $? -eq 0 ]; then
      echo "$event," >> $input_file
    else
      break
    fi
  done
  sed -i '$s/,$//' $input_file
  echo "]" >> $input_file
}

record_input