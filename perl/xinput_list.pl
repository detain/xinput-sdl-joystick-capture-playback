#!/usr/bin/perl

use strict;
use warnings;

my @devices = `xinput list`;

my @joysticks = grep { /Joystick/ } @devices;

print join("\n", @joysticks);