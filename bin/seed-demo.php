#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/content.php';

echo "==> Running LingoUz Database Seeder...\n";
$pdo = db();
seed_initial_data($pdo);
echo "==> Database seeded successfully with starter courses, vocabulary, quizzes, and demo accounts.\n";
