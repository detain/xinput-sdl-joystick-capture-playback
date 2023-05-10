#!/usr/bin/perl

use strict;
use warnings;

sub record_input {
    my ($device_id) = @_;
    my $output_file = "input_record.txt";
    system("xinput test $device_id > $output_file &");
}

my $device_id = 2; # Change this to the desired device ID
record_input($device_id);