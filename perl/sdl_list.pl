#!/usr/bin/perl

use strict;
use warnings;

sub get_sdl_joysticks {
    my @devices = `ls /dev/input/js*`;
    my @sdl_joysticks = grep { /event\d+/ && -e "/dev/input/".substr($_, 0, -1)."3" } @devices;
    return @sdl_joysticks;
}

my @joysticks = get_sdl_joysticks();
print join("\n", @joysticks);