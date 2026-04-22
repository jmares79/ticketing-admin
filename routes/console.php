<?php

Schedule::command('tickets:generate')->everyMinute();
Schedule::command('tickets:process')->everyFiveMinutes();
