#!/bin/bash

function get_device_id() {
  xinput list | grep -i joystick | awk '{print $7}' | cut -d= -f2
}

function start_recording() {
  id=$(get_device_id)
  xinput test $id > input.log &
  pid=$!
  echo "Recording started for device $id with PID $pid"
}

function stop_recording() {
  pid=$(pgrep -f "xinput test $(get_device_id)")
  kill $pid
  echo "Recording stopped for device $(get_device_id) with PID $pid"
}

start_recording
sleep 10
stop_recording