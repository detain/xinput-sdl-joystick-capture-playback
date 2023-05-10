#!/bin/bash

function get_joystick_ids() {
  xinput list | grep -i joystick | awk '{print $7}' | cut -d= -f2
}

function list_joystick_devices() {
  ids=($(get_joystick_ids))
  for id in "${ids[@]}"
  do
    xinput list-props $id | grep "Device Node" | awk '{print $NF}'
  done
}

list_joystick_devices