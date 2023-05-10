#!/bin/bash

function get_joystick_ids() {
  SDL_JOYSTICK_DEVICES=/dev/input/js*
  ls $SDL_JOYSTICK_DEVICES | cut -d/ -f3
}

function list_joystick_devices() {
  ids=($(get_joystick_ids))
  for id in "${ids[@]}"
  do
    echo "/dev/input/$id"
  done
}

list_joystick_devices