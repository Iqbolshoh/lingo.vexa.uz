<?php
// Lists t('...') literals in student-facing PHP files that have no English entry.
$root = $argv[1];
$dict = require "$root/src/lang/en.php";
$total = 0;
$files = array_merge(glob("$root/src/*.php"), glob("$root/src/views/*.php"), glob("$root/src/views/partials/*.php"));
foreach ($files as $f) {
    if (preg_match('~/(admin|stats)\.php$~', $f)) {
        continue;
    }
    preg_match_all("/\\bt\\('((?:[^'\\\\]|\\\\.)*)'/u", file_get_contents($f), $m);
    foreach (array_unique($m[1]) as $k) {
        $total++;
        $k = str_replace(["\\'", '\\\\'], ["'", '\\'], $k);
        if (!isset($dict[$k])) {
            echo basename($f) . ": $k\n";
        }
    }
}
echo "$total keys checked\n";
