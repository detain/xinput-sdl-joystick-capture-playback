#!/usr/bin/perl

use strict;
use warnings;
use JSON;

sub playback_input {
    my ($device_id, $input_file) = @_;
    open(my $fh, "<", $input_file) or die "Cannot open $input_file: $!";
    my @lines = <$fh>;
    close($fh);
    foreach my $line (@lines) {
        my $data = decode_json($line);
        my $type = $data->{type};
        my $code = $data->{code};
        my $value = $data->{value};
        system("xinput test $device_id $type $code $value");
    }
}

my $device_id = 2; # Change this to the desired device ID
my $input_file = "input_record.json"; # Change this to the name of the JSON file
playback_input($device_id, $input_file);