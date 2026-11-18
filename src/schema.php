<?php

declare(strict_types=1);

const SCHEMA_VERSION = 1;

function migrate(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS startup_meta (key TEXT PRIMARY KEY, value TEXT)');
    $version = (int) ($pdo->query("SELECT value FROM startup_meta WHERE key = 'schema_version'")->fetchColumn() ?: 0);
    if ($version >= SCHEMA_VERSION) {
        return;
    }

    $pdo->beginTransaction();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role TEXT NOT NULL DEFAULT 'student',
            display_name TEXT NOT NULL,
            email TEXT UNIQUE,
            password_hash TEXT NOT NULL,
            age_band TEXT DEFAULT '7-8',
            language_level TEXT DEFAULT 'A0',
            timezone TEXT DEFAULT 'America/New_York',
            consent_status INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS enrollments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            parent_name TEXT NOT NULL,
            parent_email TEXT NOT NULL,
            parent_phone TEXT,
            child_name TEXT NOT NULL,
            child_age TEXT NOT NULL,
            language_level TEXT NOT NULL,
            timezone TEXT NOT NULL,
            notes TEXT,
            consent INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            level TEXT NOT NULL DEFAULT 'A0-A1',
            age_range TEXT NOT NULL DEFAULT '7-11 yosh',
            description TEXT,
            status TEXT NOT NULL DEFAULT 'published'
        );

        CREATE TABLE IF NOT EXISTS lessons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sequence_number INTEGER NOT NULL UNIQUE,
            title TEXT NOT NULL,
            title_en TEXT NOT NULL,
            objective TEXT NOT NULL,
            duration_min INTEGER NOT NULL DEFAULT 5,
            video_url TEXT,
            summary TEXT,
            prerequisite_lesson_id INTEGER,
            passing_score INTEGER NOT NULL DEFAULT 80,
            status TEXT NOT NULL DEFAULT 'published'
        );

        CREATE TABLE IF NOT EXISTS vocabulary (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lesson_id INTEGER NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
            word_uz TEXT NOT NULL,
            word_en TEXT NOT NULL,
            pronunciation TEXT,
            icon TEXT NOT NULL DEFAULT '📘',
            example TEXT
        );

        CREATE TABLE IF NOT EXISTS quizzes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lesson_id INTEGER NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
            title TEXT NOT NULL,
            pass_score INTEGER NOT NULL DEFAULT 80
        );

        CREATE TABLE IF NOT EXISTS questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            quiz_id INTEGER NOT NULL REFERENCES quizzes(id) ON DELETE CASCADE,
            prompt TEXT NOT NULL,
            prompt_en TEXT,
            options_json TEXT NOT NULL,
            correct_index INTEGER NOT NULL,
            explanation TEXT,
            order_num INTEGER NOT NULL DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS quiz_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            quiz_id INTEGER NOT NULL REFERENCES quizzes(id) ON DELETE CASCADE,
            lesson_id INTEGER NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
            score INTEGER NOT NULL,
            total INTEGER NOT NULL,
            percent INTEGER NOT NULL,
            passed INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS lesson_progress (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            lesson_id INTEGER NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
            status TEXT NOT NULL DEFAULT 'locked',
            best_score INTEGER NOT NULL DEFAULT 0,
            completed_at DATETIME,
            manual_override INTEGER NOT NULL DEFAULT 0,
            UNIQUE (user_id, lesson_id)
        );

        CREATE TABLE IF NOT EXISTS badges (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            icon TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS user_badges (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            badge_code TEXT NOT NULL REFERENCES badges(code) ON DELETE CASCADE,
            earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (user_id, badge_code)
        );

        CREATE TABLE IF NOT EXISTS live_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            starts_at DATETIME NOT NULL,
            timezone TEXT NOT NULL DEFAULT 'America/New_York (EST)',
            zoom_url TEXT NOT NULL,
            mentor_name TEXT NOT NULL DEFAULT 'Gulomov Ustoz',
            status TEXT NOT NULL DEFAULT 'scheduled'
        );
    ");

    $pdo->prepare("INSERT OR REPLACE INTO startup_meta (key, value) VALUES ('schema_version', ?)")
        ->execute([(string) SCHEMA_VERSION]);
    $pdo->commit();
}
