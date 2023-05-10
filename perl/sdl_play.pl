#!/usr/bin/perl

use strict;
use warnings;
use SDL;
use JSON;

sub play_input {
    my ($device_id) = @_;
    my $input_file = "input_record.json";
    SDL::init(SDL_INIT_JOYSTICK);
    my $joystick = SDL::Joystick->new($device_id);
    my $events = decode_json(join("", <>));
    foreach my $event (@{$events}) {
        my $timestamp = $event->{"timestamp"};
        my $type = $event->{"type"};
        my $code = $event->{"code"};
        my $value = $event->{"value"};
        my $duration = $event->{"duration"};
        SDL::delay($duration * 1000);
        if ($type == SDL_JOYAXISMOTION) {
            $joystick->set_axis($code, $value);
        } elsif ($type == SDL_JOYBUTTONDOWN) {
            $joystick->button_down($code);
        } elsif ($type == SDL_JOYBUTTONUP) {
            $joystick->button_up($code);
        }
    }
    SDL::Joystick::close($joystick);
}

my $device_id = 0; # Change this to the desired device ID
play_input($device_id);