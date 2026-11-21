<?php

declare(strict_types=1);

// LingoUz admin panel: courses → lessons → vocabulary/questions, students, gradebook, enrollments, live sessions

function require_admin(): array
{
    if (!auth_check()) {
        flash('info', 'Admin panelga kirish uchun tizimga kiring.');
        redirect('/login');
    }
    if (!is_admin()) {
        flash('error', 'Bu sahifaga faqat administratorlar kira oladi.');
        redirect('/dashboard');
    }
    return auth_user();
}

function back(string $fallback): void
{
    $to = (string) ($_POST['_back'] ?? '');
    redirect(str_starts_with($to, '/admin') ? $to : $fallback);
}

function input_str(string $key, int $max = 255): string
{
    return mb_substr(trim((string) ($_POST[$key] ?? '')), 0, $max);
}

function input_int(string $key, int $min, int $max, int $default): int
{
    $v = filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT);
    return $v === false || $v === null ? $default : max($min, min($max, (int) $v));
}

/** Admin pages render inside a sidebar shell; $section highlights the active menu item. */
function render_admin(string $view, string $section, array $data): void
{
    render('admin/' . $view, $data + ['adminSection' => $section]);
}

// ---------------------------------------------------------------- overview

function page_admin(): void
{
    require_admin();
    $pdo = db();
    $counts = [
        'students' => (int) $pdo->query("SELECT count(*) FROM users WHERE role = 'student'")->fetchColumn(),
        'active7' => (int) $pdo->query("SELECT count(DISTINCT user_id) FROM quiz_attempts WHERE created_at >= datetime('now', '-7 days')")->fetchColumn(),
        'courses' => (int) $pdo->query('SELECT count(*) FROM courses')->fetchColumn(),
        'lessons' => (int) $pdo->query('SELECT count(*) FROM lessons')->fetchColumn(),
        'questions' => (int) $pdo->query('SELECT count(*) FROM questions')->fetchColumn(),
        'words' => (int) $pdo->query('SELECT count(*) FROM vocabulary')->fetchColumn(),
        'attempts' => (int) $pdo->query('SELECT count(*) FROM quiz_attempts')->fetchColumn(),
        'pass_rate' => (int) round((float) $pdo->query('SELECT avg(passed) * 100 FROM quiz_attempts')->fetchColumn()),
        'avg_score' => (int) round((float) $pdo->query('SELECT avg(percent) FROM quiz_attempts')->fetchColumn()),
        'pending' => (int) $pdo->query("SELECT count(*) FROM enrollments WHERE status = 'pending'")->fetchColumn(),
    ];

    // Mastery per lesson: average best score of students who tried it, and how many passed.
    $lessonStats = $pdo->query("
        SELECT l.id, l.title, l.position, l.passing_score, c.title AS course_title,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.best_score > 0) AS tried,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.status = 'completed') AS passed,
               (SELECT avg(lp.best_score) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.best_score > 0) AS avg_best
        FROM lessons l JOIN courses c ON c.id = l.course_id
        ORDER BY c.sort_order, c.id, l.position
    ")->fetchAll();

    $attempts = $pdo->query('
        SELECT qa.*, u.display_name, l.title AS lesson_title
        FROM quiz_attempts qa JOIN users u ON u.id = qa.user_id JOIN lessons l ON l.id = qa.lesson_id
        ORDER BY qa.id DESC LIMIT 10
    ')->fetchAll();

    // Activity of the last 14 days (attempts per day) for the bar chart.
    $daily = [];
    $rows = $pdo->query("SELECT date(created_at) AS d, count(*) AS n FROM quiz_attempts WHERE created_at >= date('now', '-13 days') GROUP BY d")->fetchAll(PDO::FETCH_KEY_PAIR);
    for ($i = 13; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} days"));
        $daily[$d] = (int) ($rows[$d] ?? 0);
    }

    render_admin('overview', 'overview', [
        'title' => 'Admin — Umumiy ko‘rinish',
        'counts' => $counts,
        'lessonStats' => $lessonStats,
        'attempts' => $attempts,
        'daily' => $daily,
        'hardest' => hardest_questions(6),
    ]);
}

/** Questions with the highest error rate across all stored attempt details. */
function hardest_questions(int $limit, ?int $lessonId = null): array
{
    $sql = 'SELECT details_json, lesson_id FROM quiz_attempts WHERE details_json IS NOT NULL' . ($lessonId ? ' AND lesson_id = ' . $lessonId : '');
    $stats = [];
    foreach (db()->query($sql) as $row) {
        foreach (json_decode((string) $row['details_json'], true) ?: [] as $d) {
            $id = (int) ($d['question_id'] ?? 0);
            if (!$id) {
                continue;
            }
            $stats[$id] ??= ['id' => $id, 'prompt' => $d['prompt'], 'type' => $d['type'] ?? 'choice', 'lesson_id' => (int) $row['lesson_id'], 'total' => 0, 'wrong' => 0];
            $stats[$id]['total']++;
            $stats[$id]['wrong'] += empty($d['is_correct']) ? 1 : 0;
        }
    }
    foreach ($stats as &$s) {
        $s['error_rate'] = (int) round($s['wrong'] / max(1, $s['total']) * 100);
    }
    unset($s);
    $stats = array_filter($stats, fn ($s) => $s['wrong'] > 0);
    usort($stats, fn ($a, $b) => [$b['error_rate'], $b['total']] <=> [$a['error_rate'], $a['total']]);
    return array_slice($stats, 0, $limit);
}

// ---------------------------------------------------------------- courses

function page_admin_courses(): void
{
    require_admin();
    $courses = db()->query("
        SELECT c.*,
               (SELECT count(*) FROM lessons l WHERE l.course_id = c.id) AS lesson_count,
               (SELECT count(*) FROM lessons l WHERE l.course_id = c.id AND l.status = 'published') AS published_count,
               (SELECT count(*) FROM vocabulary v JOIN lessons l ON l.id = v.lesson_id WHERE l.course_id = c.id) AS word_count,
               (SELECT count(*) FROM questions q JOIN quizzes z ON z.id = q.quiz_id JOIN lessons l ON l.id = z.lesson_id WHERE l.course_id = c.id) AS question_count,
               (SELECT count(DISTINCT lp.user_id) FROM lesson_progress lp JOIN lessons l ON l.id = lp.lesson_id WHERE l.course_id = c.id AND lp.best_score > 0) AS learners
        FROM courses c ORDER BY c.sort_order, c.id
    ")->fetchAll();
    render_admin('courses', 'courses', ['title' => 'Admin — Kurslar', 'courses' => $courses]);
}

function course_form_data(): array
{
    return [
        'title' => input_str('title', 120),
        'subtitle' => input_str('subtitle', 160),
        'description' => input_str('description', 2000),
        'title_en' => input_str('title_en', 120),
        'subtitle_en' => input_str('subtitle_en', 160),
        'description_en' => input_str('description_en', 2000),
        'level' => input_str('level', 20) ?: 'A0–A1',
        'age_range' => input_str('age_range', 40) ?: '7–11 yosh',
        'category' => ($_POST['category'] ?? '') === 'culture' ? 'culture' : 'language',
        'icon' => in_array($_POST['icon'] ?? '', LESSON_ICONS, true) ? $_POST['icon'] : 'book-open',
        'theme' => isset(COURSE_THEMES[$_POST['theme'] ?? '']) ? $_POST['theme'] : 'lapis',
        'status' => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        'sort_order' => input_int('sort_order', 0, 999, 0),
    ];
}

function page_admin_course_form(?int $courseId = null): void
{
    require_admin();
    $course = $courseId ? find_course($courseId) : null;
    if ($courseId && !$course) {
        not_found('Kurs topilmadi');
        return;
    }
    render_admin('course_form', 'courses', [
        'title' => $course ? 'Kursni tahrirlash' : 'Yangi kurs',
        'course' => $course ?? ['title' => '', 'subtitle' => '', 'description' => '', 'title_en' => '', 'subtitle_en' => '', 'description_en' => '', 'level' => 'A0–A1', 'age_range' => '7–11 yosh', 'category' => 'language', 'icon' => 'book-open', 'theme' => 'lapis', 'status' => 'draft', 'sort_order' => (int) db()->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM courses')->fetchColumn()],
    ]);
}

function action_admin_course_save(?int $courseId = null): void
{
    require_admin();
    $d = course_form_data();
    if ($d['title'] === '') {
        flash('error', 'Kurs nomini kiriting.');
        redirect($courseId ? "/admin/courses/{$courseId}/edit" : '/admin/courses/create');
    }
    $pdo = db();
    if ($courseId) {
        $pdo->prepare('UPDATE courses SET title=?, subtitle=?, description=?, title_en=?, subtitle_en=?, description_en=?, level=?, age_range=?, category=?, icon=?, theme=?, status=?, sort_order=? WHERE id=?')
            ->execute([...array_values($d), $courseId]);
        flash('success', 'Kurs saqlandi.');
    } else {
        $pdo->prepare("INSERT INTO courses (title, subtitle, description, title_en, subtitle_en, description_en, level, age_range, category, icon, theme, status, sort_order, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?, datetime('now'))")
            ->execute(array_values($d));
        $courseId = (int) $pdo->lastInsertId();
        flash('success', 'Kurs yaratildi. Endi unga dars qo‘shing.');
    }
    redirect("/admin/courses/{$courseId}");
}

function action_admin_course_delete(int $courseId): void
{
    require_admin();
    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare('DELETE FROM lessons WHERE course_id = ?')->execute([$courseId]);
    $pdo->prepare('DELETE FROM courses WHERE id = ?')->execute([$courseId]);
    $pdo->commit();
    flash('success', 'Kurs va uning barcha darslari o‘chirildi.');
    redirect('/admin/courses');
}

function page_admin_course(int $courseId): void
{
    require_admin();
    $course = find_course($courseId);
    if (!$course) {
        not_found('Kurs topilmadi');
        return;
    }
    $stmt = db()->prepare("
        SELECT l.*,
               (SELECT count(*) FROM vocabulary v WHERE v.lesson_id = l.id) AS word_count,
               (SELECT count(*) FROM questions q JOIN quizzes z ON z.id = q.quiz_id WHERE z.lesson_id = l.id) AS question_count,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.status = 'completed') AS passed_count,
               (SELECT avg(lp.best_score) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.best_score > 0) AS avg_best
        FROM lessons l WHERE l.course_id = ? ORDER BY l.position, l.id
    ");
    $stmt->execute([$courseId]);
    render_admin('course_show', 'courses', ['title' => $course['title'] . ' — Admin', 'course' => $course, 'lessons' => $stmt->fetchAll()]);
}

/** Renumbers positions 1..n so moving up/down is always a simple swap. */
function renumber_lessons(int $courseId): void
{
    $pdo = db();
    $ids = $pdo->prepare('SELECT id FROM lessons WHERE course_id = ? ORDER BY position, id');
    $ids->execute([$courseId]);
    $upd = $pdo->prepare('UPDATE lessons SET position = ? WHERE id = ?');
    foreach ($ids->fetchAll(PDO::FETCH_COLUMN) as $i => $id) {
        $upd->execute([$i + 1, $id]);
    }
}

function action_admin_lesson_move(int $lessonId): void
{
    require_admin();
    $lesson = find_lesson($lessonId);
    if (!$lesson) {
        redirect('/admin/courses');
    }
    renumber_lessons((int) $lesson['course_id']);
    $lesson = find_lesson($lessonId);
    $target = (int) $lesson['position'] + (($_POST['dir'] ?? '') === 'up' ? -1 : 1);
    $pdo = db();
    $other = $pdo->prepare('SELECT id FROM lessons WHERE course_id = ? AND position = ?');
    $other->execute([$lesson['course_id'], $target]);
    if ($otherId = $other->fetchColumn()) {
        $pdo->prepare('UPDATE lessons SET position = ? WHERE id = ?')->execute([$lesson['position'], $otherId]);
        $pdo->prepare('UPDATE lessons SET position = ? WHERE id = ?')->execute([$target, $lessonId]);
    }
    redirect('/admin/courses/' . $lesson['course_id']);
}

// ---------------------------------------------------------------- lessons

function lesson_form_data(): array
{
    return [
        'title' => input_str('title', 160),
        'title_en' => input_str('title_en', 160),
        'objective' => input_str('objective', 600),
        'objective_en' => input_str('objective_en', 600),
        'summary' => input_str('summary', 1200),
        'duration_min' => input_int('duration_min', 1, 120, 5),
        'video_url' => input_str('video_url', 500),
        'reading_uz' => input_str('reading_uz', 4000),
        'reading_en' => input_str('reading_en', 4000),
        'culture_title' => input_str('culture_title', 160),
        'culture_note' => input_str('culture_note', 2000),
        'culture_title_en' => input_str('culture_title_en', 160),
        'culture_note_en' => input_str('culture_note_en', 2000),
        'icon' => in_array($_POST['icon'] ?? '', LESSON_ICONS, true) ? $_POST['icon'] : 'book-open',
        'passing_score' => input_int('passing_score', 50, 100, 80),
        'status' => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
    ];
}

/** Only YouTube/Vimeo embeds or direct video files are accepted as lesson videos. */
function clean_video_url(string $url): string
{
    if ($url === '') {
        return '';
    }
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube(?:-nocookie)?\.com/embed/)([\w-]{11})~', $url, $m)) {
        return 'https://www.youtube-nocookie.com/embed/' . $m[1];
    }
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return preg_match('~^https://[^\s"\'<>]+\.(mp4|webm)$~i', $url) ? $url : '';
}

function page_admin_lesson_create(int $courseId): void
{
    require_admin();
    $course = find_course($courseId);
    if (!$course) {
        not_found('Kurs topilmadi');
        return;
    }
    render_admin('lesson_create', 'courses', ['title' => 'Yangi dars — ' . $course['title'], 'course' => $course]);
}

function action_admin_lesson_store(int $courseId): void
{
    require_admin();
    if (!find_course($courseId)) {
        redirect('/admin/courses');
    }
    $d = lesson_form_data();
    if ($d['title'] === '' || $d['objective'] === '') {
        flash('error', 'Dars nomi va maqsadini kiriting.');
        redirect("/admin/courses/{$courseId}/lessons/create");
    }
    $d['title_en'] = $d['title_en'] ?: $d['title'];
    $d['video_url'] = clean_video_url($d['video_url']);
    $pdo = db();
    $seq = (int) $pdo->query('SELECT COALESCE(MAX(sequence_number), 0) + 1 FROM lessons')->fetchColumn();
    $pos = (int) $pdo->query("SELECT COALESCE(MAX(position), 0) + 1 FROM lessons WHERE course_id = {$courseId}")->fetchColumn();
    $pdo->prepare('
        INSERT INTO lessons (sequence_number, course_id, position, title, title_en, objective, objective_en, summary, duration_min, video_url,
                             reading_uz, reading_en, culture_title, culture_note, culture_title_en, culture_note_en, icon, passing_score, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ')->execute([$seq, $courseId, $pos, ...array_values($d)]);
    $lessonId = (int) $pdo->lastInsertId();
    lesson_quiz($lessonId);
    flash('success', 'Dars yaratildi. Endi lug‘at va test savollarini qo‘shing.');
    redirect("/admin/lessons/{$lessonId}");
}

function page_admin_lesson(int $lessonId): void
{
    require_admin();
    $lesson = find_lesson($lessonId);
    if (!$lesson) {
        not_found('Dars topilmadi');
        return;
    }
    $vocab = db()->prepare('SELECT * FROM vocabulary WHERE lesson_id = ? ORDER BY id');
    $vocab->execute([$lessonId]);
    $tab = in_array($_GET['tab'] ?? '', ['content', 'vocab', 'questions', 'results'], true) ? $_GET['tab'] : 'content';

    $results = db()->prepare('
        SELECT u.id, u.display_name, lp.status, lp.best_score, lp.completed_at,
               (SELECT count(*) FROM quiz_attempts qa WHERE qa.user_id = u.id AND qa.lesson_id = ?) AS attempts
        FROM lesson_progress lp JOIN users u ON u.id = lp.user_id
        WHERE lp.lesson_id = ? AND (lp.best_score > 0 OR lp.status = \'completed\')
        ORDER BY lp.best_score DESC
    ');
    $results->execute([$lessonId, $lessonId]);

    render_admin('lesson_edit', 'courses', [
        'title' => $lesson['title'] . ' — Admin',
        'lesson' => $lesson,
        'course' => find_course((int) $lesson['course_id']),
        'vocab' => $vocab->fetchAll(),
        'questions' => lesson_questions($lessonId),
        'tab' => $tab,
        'results' => $results->fetchAll(),
        'hardest' => hardest_questions(5, $lessonId),
    ]);
}

function action_admin_lesson_update(int $lessonId): void
{
    require_admin();
    $lesson = find_lesson($lessonId);
    if (!$lesson) {
        redirect('/admin/courses');
    }
    $d = lesson_form_data();
    if ($d['title'] === '' || $d['objective'] === '') {
        flash('error', 'Dars nomi va maqsadini kiriting.');
        redirect("/admin/lessons/{$lessonId}");
    }
    $d['title_en'] = $d['title_en'] ?: $d['title'];
    $raw = $d['video_url'];
    $d['video_url'] = clean_video_url($raw);
    db()->prepare('
        UPDATE lessons SET title=?, title_en=?, objective=?, objective_en=?, summary=?, duration_min=?, video_url=?, reading_uz=?, reading_en=?,
                           culture_title=?, culture_note=?, culture_title_en=?, culture_note_en=?, icon=?, passing_score=?, status=?
        WHERE id = ?
    ')->execute([...array_values($d), $lessonId]);
    db()->prepare('UPDATE quizzes SET pass_score = ? WHERE lesson_id = ?')->execute([$d['passing_score'], $lessonId]);
    flash($raw !== '' && $d['video_url'] === '' ? 'error' : 'success', $raw !== '' && $d['video_url'] === ''
        ? 'Dars saqlandi, lekin video havolasi tanilmadi (YouTube, Vimeo yoki https://….mp4).'
        : 'Dars saqlandi.');
    redirect("/admin/lessons/{$lessonId}");
}

function action_admin_lesson_delete(int $lessonId): void
{
    require_admin();
    $lesson = find_lesson($lessonId);
    if ($lesson) {
        db()->prepare('DELETE FROM lessons WHERE id = ?')->execute([$lessonId]);
        renumber_lessons((int) $lesson['course_id']);
        flash('success', '«' . $lesson['title'] . '» darsi o‘chirildi.');
    }
    redirect('/admin/courses/' . ($lesson['course_id'] ?? ''));
}

// ---------------------------------------------------------------- vocabulary

function action_admin_vocab_save(int $lessonId, ?int $vocabId = null): void
{
    require_admin();
    $uz = input_str('word_uz', 120);
    $en = input_str('word_en', 160);
    if ($uz === '' || $en === '') {
        flash('error', 'O‘zbekcha so‘z va tarjimani kiriting.');
        redirect("/admin/lessons/{$lessonId}?tab=vocab");
    }
    $row = [$uz, $en, input_str('pronunciation', 120), input_str('example', 300)];
    if ($vocabId) {
        db()->prepare('UPDATE vocabulary SET word_uz=?, word_en=?, pronunciation=?, example=? WHERE id=? AND lesson_id=?')->execute([...$row, $vocabId, $lessonId]);
        flash('success', 'So‘z yangilandi.');
    } else {
        db()->prepare("INSERT INTO vocabulary (lesson_id, word_uz, word_en, pronunciation, example, icon) VALUES (?, ?, ?, ?, ?, '')")->execute([$lessonId, ...$row]);
        flash('success', '«' . $uz . '» so‘zi qo‘shildi.');
    }
    redirect("/admin/lessons/{$lessonId}?tab=vocab");
}

function action_admin_vocab_delete(int $lessonId, int $vocabId): void
{
    require_admin();
    db()->prepare('DELETE FROM vocabulary WHERE id = ? AND lesson_id = ?')->execute([$vocabId, $lessonId]);
    flash('success', 'So‘z o‘chirildi.');
    redirect("/admin/lessons/{$lessonId}?tab=vocab");
}

// ---------------------------------------------------------------- questions

function action_admin_question_save(int $lessonId, ?int $questionId = null): void
{
    require_admin();
    $type = isset(QUESTION_TYPES[$_POST['type'] ?? '']) ? $_POST['type'] : 'choice';
    $prompt = input_str('prompt', 400);
    $promptEn = input_str('prompt_en', 400);
    $options = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) ($_POST['options'] ?? ''))), fn ($o) => $o !== ''));
    $answer = input_str('answer_text', 300);
    $audio = input_str('audio_text', 200);
    $correct = input_int('correct_index', 1, 10, 1) - 1;
    $back = "/admin/lessons/{$lessonId}?tab=questions";

    $error = match (true) {
        $prompt === '' => 'Savol matnini kiriting.',
        in_array($type, ['choice', 'listen'], true) && count($options) < 2 => 'Kamida 2 ta variant yozing (har biri yangi qatorda).',
        in_array($type, ['choice', 'listen'], true) && $correct >= count($options) => 'To‘g‘ri javob raqami variantlar sonidan katta.',
        $type === 'listen' && $audio === '' => 'Tinglash savoli uchun ovozda aytiladigan o‘zbekcha matnni kiriting.',
        in_array($type, ['build', 'fill'], true) && $answer === '' => 'To‘g‘ri javob matnini kiriting.',
        default => null,
    };
    if ($error) {
        flash('error', $error);
        redirect($back);
    }
    if (in_array($type, ['build', 'fill'], true)) {
        $correct = 0;
        $options = $type === 'build' ? $options : [];
    }

    $quiz = lesson_quiz($lessonId);
    $row = [$type, $prompt, $promptEn ?: null, json_encode($options, JSON_UNESCAPED_UNICODE), $correct, $answer ?: null, $audio ?: null, input_str('explanation', 600) ?: null];
    if ($questionId) {
        db()->prepare('UPDATE questions SET type=?, prompt=?, prompt_en=?, options_json=?, correct_index=?, answer_text=?, audio_text=?, explanation=? WHERE id=? AND quiz_id=?')
            ->execute([...$row, $questionId, $quiz['id']]);
        flash('success', 'Savol yangilandi.');
    } else {
        $order = (int) db()->query("SELECT COALESCE(MAX(order_num),0)+1 FROM questions WHERE quiz_id = {$quiz['id']}")->fetchColumn();
        db()->prepare('INSERT INTO questions (type, prompt, prompt_en, options_json, correct_index, answer_text, audio_text, explanation, quiz_id, order_num) VALUES (?,?,?,?,?,?,?,?,?,?)')
            ->execute([...$row, $quiz['id'], $order]);
        flash('success', 'Savol qo‘shildi.');
    }
    redirect($back);
}

function action_admin_question_delete(int $lessonId, int $questionId): void
{
    require_admin();
    $quiz = lesson_quiz($lessonId);
    db()->prepare('DELETE FROM questions WHERE id = ? AND quiz_id = ?')->execute([$questionId, $quiz['id']]);
    flash('success', 'Savol o‘chirildi.');
    redirect("/admin/lessons/{$lessonId}?tab=questions");
}

function action_admin_question_move(int $lessonId, int $questionId): void
{
    require_admin();
    $quiz = lesson_quiz($lessonId);
    $pdo = db();
    $ids = $pdo->query("SELECT id FROM questions WHERE quiz_id = {$quiz['id']} ORDER BY order_num, id")->fetchAll(PDO::FETCH_COLUMN);
    $i = array_search($questionId, array_map('intval', $ids), true);
    $j = $i === false ? false : $i + (($_POST['dir'] ?? '') === 'up' ? -1 : 1);
    if ($i !== false && isset($ids[$j])) {
        [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
    }
    $upd = $pdo->prepare('UPDATE questions SET order_num = ? WHERE id = ?');
    foreach ($ids as $n => $id) {
        $upd->execute([$n + 1, $id]);
    }
    redirect("/admin/lessons/{$lessonId}?tab=questions");
}

// ---------------------------------------------------------------- students

function page_admin_students(): void
{
    require_admin();
    $q = trim((string) ($_GET['q'] ?? ''));
    $stmt = db()->prepare("
        SELECT u.*,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.status = 'completed') AS completed_lessons,
               (SELECT avg(lp.best_score) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.best_score > 0) AS avg_best,
               (SELECT count(*) FROM quiz_attempts qa WHERE qa.user_id = u.id) AS attempts,
               (SELECT max(created_at) FROM quiz_attempts qa WHERE qa.user_id = u.id) AS last_activity
        FROM users u
        WHERE u.role = 'student' AND (? = '' OR u.display_name LIKE ? OR u.email LIKE ?)
        ORDER BY u.id DESC
    ");
    $stmt->execute([$q, "%{$q}%", "%{$q}%"]);
    render_admin('students', 'students', [
        'title' => 'Admin — O‘quvchilar',
        'students' => $stmt->fetchAll(),
        'q' => $q,
        'totalLessons' => (int) db()->query("SELECT count(*) FROM lessons WHERE status = 'published'")->fetchColumn(),
    ]);
}

function page_admin_student(int $userId): void
{
    require_admin();
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
    $stmt->execute([$userId]);
    $student = $stmt->fetch();
    if (!$student) {
        not_found('O‘quvchi topilmadi');
        return;
    }
    $attempts = db()->prepare('
        SELECT qa.*, l.title AS lesson_title FROM quiz_attempts qa JOIN lessons l ON l.id = qa.lesson_id
        WHERE qa.user_id = ? ORDER BY qa.id DESC LIMIT 30
    ');
    $attempts->execute([$userId]);
    $badges = db()->prepare('SELECT b.*, ub.earned_at FROM user_badges ub JOIN badges b ON b.code = ub.badge_code WHERE ub.user_id = ? ORDER BY ub.earned_at');
    $badges->execute([$userId]);

    render_admin('student_show', 'students', [
        'title' => $student['display_name'] . ' — O‘quvchi kartasi',
        'student' => $student,
        'courseStates' => array_map(fn ($c) => course_state($c, $userId), db()->query('SELECT * FROM courses ORDER BY sort_order, id')->fetchAll()),
        'attempts' => $attempts->fetchAll(),
        'badges' => $badges->fetchAll(),
        'level' => xp_level((int) $student['xp']),
        'streak' => current_streak($student),
    ]);
}

function action_admin_unlock(): void
{
    require_admin();
    $userId = (int) ($_POST['user_id'] ?? 0);
    $lessonId = (int) ($_POST['lesson_id'] ?? 0);
    if ($userId > 0 && find_lesson($lessonId)) {
        db()->prepare('
            INSERT INTO lesson_progress (user_id, lesson_id, status, manual_override) VALUES (?, ?, \'available\', 1)
            ON CONFLICT(user_id, lesson_id) DO UPDATE SET manual_override = 1
        ')->execute([$userId, $lessonId]);
        flash('success', 'Dars o‘quvchi uchun qo‘lda ochildi.');
    }
    back("/admin/students/{$userId}");
}

function action_admin_student_reset_password(int $userId): void
{
    require_admin();
    $password = (string) ($_POST['password'] ?? '');
    if (mb_strlen($password) < 6) {
        flash('error', 'Yangi parol kamida 6 ta belgi bo‘lsin.');
    } else {
        db()->prepare("UPDATE users SET password_hash = ? WHERE id = ? AND role = 'student'")->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
        flash('success', 'Parol yangilandi.');
    }
    redirect("/admin/students/{$userId}");
}

function action_admin_student_delete(int $userId): void
{
    require_admin();
    db()->prepare("DELETE FROM users WHERE id = ? AND role = 'student'")->execute([$userId]);
    flash('success', 'O‘quvchi va uning natijalari o‘chirildi.');
    redirect('/admin/students');
}

function page_admin_gradebook(): void
{
    require_admin();
    $courses = db()->query('SELECT * FROM courses ORDER BY sort_order, id')->fetchAll();
    $courseId = (int) ($_GET['course'] ?? ($courses[0]['id'] ?? 0));
    $course = find_course($courseId);
    $lessons = $course ? course_lessons($courseId, false) : [];
    $students = db()->query("SELECT id, display_name, xp FROM users WHERE role = 'student' ORDER BY id")->fetchAll();
    $grid = [];
    foreach (db()->query('SELECT user_id, lesson_id, best_score, status FROM lesson_progress') as $p) {
        $grid[(int) $p['user_id']][(int) $p['lesson_id']] = $p;
    }
    render_admin('gradebook', 'gradebook', [
        'title' => 'Admin — Baholar jurnali',
        'courses' => $courses,
        'course' => $course,
        'lessons' => $lessons,
        'students' => $students,
        'grid' => $grid,
    ]);
}

// ---------------------------------------------------------------- enrollments

function page_admin_enrollments(): void
{
    require_admin();
    $status = (string) ($_GET['status'] ?? '');
    $stmt = db()->prepare("SELECT * FROM enrollments WHERE (? = '' OR status = ?) ORDER BY id DESC");
    $stmt->execute([$status, $status]);
    render_admin('enrollments', 'enrollments', [
        'title' => 'Admin — Arizalar',
        'enrollments' => $stmt->fetchAll(),
        'status' => $status,
        'counts' => db()->query('SELECT status, count(*) FROM enrollments GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR),
    ]);
}

function action_admin_enrollment_status(): void
{
    require_admin();
    $id = (int) ($_POST['enrollment_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    if ($id > 0 && in_array($status, ['pending', 'contacted', 'approved', 'rejected'], true)) {
        db()->prepare('UPDATE enrollments SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('success', "Ariza #{$id} holati yangilandi.");
    }
    back('/admin/enrollments');
}

function action_admin_export(): void
{
    require_admin();
    $enrollments = db()->query('SELECT * FROM enrollments ORDER BY id DESC')->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lingouz_enrollments_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, ['ID', 'Ota-ona Ismi', 'Email', 'Telefon', 'Bola Ismi', 'Yoshi', 'Darajasi', 'Vaqt Zonasi', 'Izoh', 'Holat', 'Sana'], ',', '"', '');
    foreach ($enrollments as $e) {
        fputcsv($out, [$e['id'], $e['parent_name'], $e['parent_email'], $e['parent_phone'] ?? '', $e['child_name'], $e['child_age'], $e['language_level'], $e['timezone'], $e['notes'] ?? '', $e['status'], $e['created_at']], ',', '"', '');
    }
    fclose($out);
    exit;
}

// ---------------------------------------------------------------- live sessions

function page_admin_live(): void
{
    require_admin();
    render_admin('live', 'live', [
        'title' => 'Admin — Jonli darslar',
        'sessions' => db()->query('SELECT * FROM live_sessions ORDER BY starts_at DESC')->fetchAll(),
        'edit' => isset($_GET['edit']) ? db()->query('SELECT * FROM live_sessions WHERE id = ' . (int) $_GET['edit'])->fetch() : null,
    ]);
}

function action_admin_live_save(?int $id = null): void
{
    require_admin();
    $title = input_str('title', 160);
    $zoom = input_str('zoom_url', 400);
    $zone = normalize_timezone($_POST['timezone'] ?? '');
    try {
        $startsUtc = (new DateTimeImmutable((string) ($_POST['starts_at'] ?? ''), new DateTimeZone($zone)))
            ->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    } catch (Throwable) {
        $startsUtc = '';
    }
    if ($title === '' || $startsUtc === '' || !preg_match('~^https://~', $zoom)) {
        flash('error', 'Nom, sana-vaqt va https:// bilan boshlanadigan Zoom havolasini kiriting.');
        redirect('/admin/live' . ($id ? "?edit={$id}" : ''));
    }
    $row = [$title, input_str('description', 1000), $startsUtc, $zone, $zoom, input_str('mentor_name', 80) ?: 'Gulomov Ustoz', ($_POST['status'] ?? '') === 'cancelled' ? 'cancelled' : 'scheduled'];
    if ($id) {
        db()->prepare('UPDATE live_sessions SET title=?, description=?, starts_at=?, timezone=?, zoom_url=?, mentor_name=?, status=? WHERE id=?')->execute([...$row, $id]);
    } else {
        db()->prepare('INSERT INTO live_sessions (title, description, starts_at, timezone, zoom_url, mentor_name, status) VALUES (?,?,?,?,?,?,?)')->execute($row);
    }
    flash('success', 'Jonli dars saqlandi.');
    redirect('/admin/live');
}

function action_admin_live_delete(int $id): void
{
    require_admin();
    db()->prepare('DELETE FROM live_sessions WHERE id = ?')->execute([$id]);
    flash('success', 'Jonli dars o‘chirildi.');
    redirect('/admin/live');
}
