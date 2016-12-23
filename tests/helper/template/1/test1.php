<?php

$case->assertTrue($number == 500);
print "abc";

print $self->get('test2', array('number' => 600));

$case->assertTrue($number == 500);
print "def";

