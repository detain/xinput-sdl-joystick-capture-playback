#!/usr/bin/perl

use strict;
use warnings;
use SDL;
use JSON;

sub record_input {
    my ($device_id) = @_;
    my $output_file = "input_record.json";
    SDL::init(SDL_INIT_JOYSTICK);
    my $joystick = SDL::Joystick->new($device_id);
    my $event = SDL::Event->new();
    my $start_time = time();
    my $last_time = $start_time;
    my $events = [];
    while (1) {
        SDL::Events::pump_events();
        while (SDL::Events::poll_event($event)) {
            my $event_type = $event->type();
            if ($event_type == SDL_JOYAXISMOTION || $event_type == SDL_JOYBUTTONDOWN || $event_type == SDL_JOYBUTTONUP) {
                my $timestamp = time() - $start_time;
                my $type = $event->type();
                my $code = $event->jbutton_button();
                my $value = $event->jbutton_state();
                my $duration = $timestamp - $last_time;
                push @{$events}, {"timestamp" => $timestamp, "type" => $type, "code" => $code, "value" => $value, "duration" => $duration};
                $last_time = $timestamp;
            }
            last if ($event_type == SDL_QUIT);
        }
        last if (!SDL::Joystick::event_state());
    }
    SDL::Joystick::close($joystick);
    open(my $fh, ">", $output_file) or die "Cannot open $output_file: $!";
    print $fh encode_json($events);
    close($fh);
}

my $device_id = 0; # Change this to the desired device ID
record_input($device_id);