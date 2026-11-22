<?php

use Illuminate\Support\Facades\Route;

// Ensure bootstrap and handlers are loaded
require_once base_path('src/bootstrap.php');
require_once base_path('src/learning.php');
require_once base_path('src/handlers.php');
require_once base_path('src/admin.php');
require_once base_path('src/onboarding.php');
require_once base_path('src/stats.php');
require_once base_path('src/telegram.php');
require_once base_path('src/og.php');
require_once base_path('src/stories.php');

// Helper wrapper for request handling
function run_action(callable $fn, ...$args) {
    start_session();
    resolve_locale();
    track_activity();
    $fn(...$args);
}

// ---------------- Public & Learner Routes ----------------
Route::get('/', function () { run_action('page_home'); });
Route::post('/enroll', function () { run_action('action_enroll'); });
Route::get('/login', function () { run_action('page_login'); });
Route::post('/login', function () { run_action('action_login'); });
Route::any('/logout', function () { run_action('action_logout'); });
Route::get('/register', function () { run_action('page_register'); });
Route::post('/register', function () { run_action('action_register'); });
Route::get('/dashboard', function () { run_action('page_dashboard'); });

// Onboarding
Route::get('/onboarding', function () { run_action('page_onboarding'); });
Route::post('/onboarding', function () { run_action('action_onboarding'); });
Route::post('/onboarding/skip', function () { run_action('action_onboarding_skip'); });
Route::get('/onboarding/placement', function () { run_action('page_placement'); });
Route::post('/onboarding/placement', function () { run_action('action_placement'); });
Route::get('/onboarding/done', function () { run_action('page_onboarding_done'); });

// Courses & Lessons
Route::get('/courses', function () { run_action('page_courses'); });
Route::get('/courses/{id}', function ($id) { run_action('page_course', (int) $id); });
Route::get('/lessons/{id}', function ($id) { run_action('page_lesson', (int) $id); });
Route::post('/lessons/{id}/quiz', function ($id) { run_action('action_submit_quiz', (int) $id); });
Route::post('/lessons/{id}/game', function ($id) { run_action('action_game_complete', (int) $id); });
Route::get('/lessons/{id}/result', function ($id) { run_action('page_quiz_result', (int) $id); });

// Profile & Live & Certificate
Route::get('/profile', function () { run_action('page_profile'); });
Route::post('/profile', function () { run_action('action_update_profile'); });
Route::get('/live', function () { run_action('page_live'); });
Route::get('/certificate', function () { run_action('page_certificate'); });
Route::get('/verify/{code}', function ($code) { run_action('page_verify_certificate', (string) $code); });

// Leaderboard & Flashcards & Stories
Route::get('/leaderboard', function () { run_action('page_leaderboard'); });
Route::get('/flashcards', function () { run_action('page_flashcards'); });
Route::post('/flashcards/done', function () { run_action('action_flashcards_done'); });
Route::get('/stories', function () { run_action('page_stories'); });
Route::get('/stories/{id}', function ($id) { run_action('page_story', (string) $id); });
Route::post('/stories/{id}/complete', function ($id) { run_action('action_story_complete', (string) $id); });

// Telegram Bot
Route::post('/api/telegram/webhook', function () { run_action('action_telegram_webhook'); });
Route::get('/api/telegram/status', function () { run_action('action_telegram_status'); });
Route::post('/api/telegram/unlink', function () { run_action('action_telegram_unlink'); });
Route::get('/telegram/connect', function () { run_action('page_telegram_connect'); });

// Dynamic Open Graph (OG) Share Cards
Route::get('/og/profile/{id}.png', function ($id) { action_og_profile((int) $id); });
Route::get('/og/word/{id}.png', function ($id) { action_og_word((int) $id); });
Route::get('/og/default.png', function () { action_og_default(); });

// Static Pages
Route::get('/privacy', function () { run_action('page_privacy'); });
Route::get('/terms', function () { run_action('page_privacy'); });
Route::get('/parent-tips', function () { run_action('page_parent_tips'); });

// ---------------- Admin Routes ----------------
Route::prefix('admin')->group(function () {
    Route::get('/', function () { run_action('page_admin'); });
    Route::get('/courses', function () { run_action('page_admin_courses'); });
    Route::get('/courses/create', function () { run_action('page_admin_course_form'); });
    Route::post('/courses', function () { run_action('action_admin_course_save'); });
    Route::get('/courses/{id}', function ($id) { run_action('page_admin_course', (int) $id); });
    Route::get('/courses/{id}/edit', function ($id) { run_action('page_admin_course_form', (int) $id); });
    Route::post('/courses/{id}', function ($id) { run_action('action_admin_course_save', (int) $id); });
    Route::post('/courses/{id}/delete', function ($id) { run_action('action_admin_course_delete', (int) $id); });

    Route::get('/courses/{id}/lessons/create', function ($id) { run_action('page_admin_lesson_create', (int) $id); });
    Route::post('/courses/{id}/lessons', function ($id) { run_action('action_admin_lesson_store', (int) $id); });
    Route::get('/lessons/{id}', function ($id) { run_action('page_admin_lesson', (int) $id); });
    Route::post('/lessons/{id}', function ($id) { run_action('action_admin_lesson_update', (int) $id); });
    Route::post('/lessons/{id}/delete', function ($id) { run_action('action_admin_lesson_delete', (int) $id); });
    Route::post('/lessons/{id}/move', function ($id) { run_action('action_admin_lesson_move', (int) $id); });

    Route::post('/lessons/{id}/vocab', function ($id) { run_action('action_admin_vocab_save', (int) $id); });
    Route::post('/lessons/{id}/vocab/{vocabId}', function ($id, $vocabId) { run_action('action_admin_vocab_save', (int) $id, (int) $vocabId); });
    Route::post('/lessons/{id}/vocab/{vocabId}/delete', function ($id, $vocabId) { run_action('action_admin_vocab_delete', (int) $id, (int) $vocabId); });

    Route::post('/lessons/{id}/questions', function ($id) { run_action('action_admin_question_save', (int) $id); });
    Route::post('/lessons/{id}/questions/{qId}', function ($id, $qId) { run_action('action_admin_question_save', (int) $id, (int) $qId); });
    Route::post('/lessons/{id}/questions/{qId}/delete', function ($id, $qId) { run_action('action_admin_question_delete', (int) $id, (int) $qId); });
    Route::post('/lessons/{id}/questions/{qId}/move', function ($id, $qId) { run_action('action_admin_question_move', (int) $id, (int) $qId); });

    Route::get('/students', function () { run_action('page_admin_students'); });
    Route::get('/students/create', function () { run_action('page_admin_student_create'); });
    Route::post('/students', function () { run_action('action_admin_student_store'); });
    Route::get('/students/{id}', function ($id) { run_action('page_admin_student', (int) $id); });
    Route::post('/students/{id}/update', function ($id) { run_action('action_admin_student_update', (int) $id); });
    Route::post('/students/{id}/password', function ($id) { run_action('action_admin_student_reset_password', (int) $id); });
    Route::post('/students/{id}/delete', function ($id) { run_action('action_admin_student_delete', (int) $id); });
    Route::post('/students/{id}/reset-progress', function ($id) { run_action('action_admin_student_reset_progress', (int) $id); });

    Route::post('/unlock', function () { run_action('action_admin_unlock'); });
    Route::get('/gradebook', function () { run_action('page_admin_gradebook'); });
    Route::get('/stats', function () { run_action('page_admin_stats'); });
    Route::get('/export/students', function () { run_action('action_admin_export_students'); });
    Route::get('/export/gradebook', function () { run_action('action_admin_export_gradebook'); });
    Route::get('/export', function () { run_action('action_admin_export'); });

    Route::get('/settings', function () { run_action('page_admin_settings'); });
    Route::post('/settings/password', function () { run_action('action_admin_password'); });
    Route::post('/settings/admins', function () { run_action('action_admin_create_admin'); });
    Route::post('/settings/telegram', function () { run_action('action_admin_telegram_save'); });
    Route::post('/settings/telegram/webhook', function () { run_action('action_admin_telegram_webhook_set'); });
    Route::post('/settings/telegram/broadcast', function () { run_action('action_admin_telegram_test_broadcast'); });
    Route::post('/settings/telegram/dev-notify', function () { run_action('action_admin_telegram_dev_notify'); });
    Route::post('/settings/telegram/dev-post', function () { run_action('action_admin_telegram_dev_post'); });

    Route::get('/enrollments', function () { run_action('page_admin_enrollments'); });
    Route::post('/enrollments/status', function () { run_action('action_admin_enrollment_status'); });

    Route::get('/live', function () { run_action('page_admin_live'); });
    Route::post('/live', function () { run_action('action_admin_live_save'); });
    Route::post('/live/{id}', function ($id) { run_action('action_admin_live_save', (int) $id); });
    Route::post('/live/{id}/delete', function ($id) { run_action('action_admin_live_delete', (int) $id); });
});
