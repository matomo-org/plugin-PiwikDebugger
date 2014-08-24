#!/bin/sh

$1 > "$2" 2>&1 &

wait $!
echo $? > "$3"