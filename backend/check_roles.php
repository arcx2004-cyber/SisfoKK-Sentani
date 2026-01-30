<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

\ = \App\Models\User::where('email', 'admin@sisfokk.sch.id')->first();
if (\) {
    echo " User: \ . \->email . \\n\;
 echo \Roles: \ . implode(', ', \->getRoleNames()->toArray()) . \\n\;
} else {
 echo \User not found\n\;
}
