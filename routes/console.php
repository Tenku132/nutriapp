<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('migrations:duplicates', function () {
    $files = scandir(database_path('migrations'));
    $baseNames = [];

    foreach ($files as $file) {
        if (str_ends_with($file, '.php')) {
            $base = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $file);
            $baseNames[$base][] = $file;
        }
    }

    $hasDuplicates = false;
    foreach ($baseNames as $base => $group) {
        if (count($group) > 1) {
            $hasDuplicates = true;
            $this->warn("Duplicate: $base");
            foreach ($group as $f) {
                $this->line(" - $f");
            }
        }
    }

    if (!$hasDuplicates) {
        $this->info("No duplicate migration files found.");
    }
});

