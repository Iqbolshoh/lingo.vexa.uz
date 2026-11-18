#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/content.php';

echo "==> LingoUz Database Seeder ishga tushmoqda...\n";
$pdo = db();
seed_initial_data($pdo);
echo "==> Ma’lumotlar bazasi muvaffaqiyatli to‘ldirildi! 5 ta MVP darslari, lug‘atlar, testlar va demo profillar tayyor.\n";
