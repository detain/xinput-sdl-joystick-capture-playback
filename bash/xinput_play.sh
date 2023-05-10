#!/bin/bash

function get_device_id() {
  xinput list | grep -i joystick | awk '{print $7}' | cut -d= -f2
}

function replay_input() {
  id=$(get_device_id)
  while read line; do
    xinput set-button-map $id $line
    sleep 0.1
  done < input.json
}

replay_input