<?php
// Подключение к базе данных (данные Beget)
$host = 'localhost';               // или 'u95122db.beget.tech', если localhost не работает
$db   = 'u95122db_1';
$user = 'u95122db_1';
$pass = 'f*b8ltWjDhqA';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение списка курсов
$courses = $pdo->query("SELECT * FROM courses ORDER BY id")->fetchAll();

// Обработка формы записи
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $courseId = $_POST['course'] ?? '';
    $consent  = isset($_POST['consent']) ? 1 : 0;

    // Валидация
    if ($fullname === '') {
        $errors['fullname'] = 'Пожалуйста, введите полное имя';
    }
    if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Формат: +7(XXX)-XXX-XX-XX';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email';
    }
    if ($courseId === '' || !is_numeric($courseId)) {
        $errors['course'] = 'Выберите курс из списка';
    }
    if (!$consent) {
        $errors['consent'] = 'Необходимо дать согласие';
    }

    if (empty($errors)) {
        // Сохранение в БД
        $stmt = $pdo->prepare("INSERT INTO enrollments (fullname, phone, email, course_id, consent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullname, $phone, $email, (int)$courseId, 1]);
        $success = true;
        $_POST = []; // очистка формы
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Курсы | ЛингвоМастер</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Roboto, 'Helvetica Neue', system-ui, sans-serif; background: #f7f9fc; color: #1e293b; line-height: 1.5; display: flex; flex-direction: column; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; width: 100%; }
        .header { background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); background: rgba(255,255,255,0.9); }
        .header-inner { display: flex; align-items: center; justify-content: space-between; height: 72px; }
        .logo { font-size: 1.8rem; font-weight: 700; letter-spacing: -0.5px; color: #0f3b5c; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .logo span { background: #0f3b5c; color: white; padding: 4px 12px; border-radius: 30px; font-size: 1rem; font-weight: 600; margin-left: 4px; }
        .nav { display: flex; gap: 36px; align-items: center; }
        .nav-link { text-decoration: none; color: #334155; font-weight: 500; transition: color 0.2s; font-size: 1.05rem; }
        .nav-link:hover { color: #0f3b5c; }
        .btn-course { background: #0f3b5c; color: white !important; padding: 10px 24px; border-radius: 40px; font-weight: 600; text-decoration: none; transition: background 0.2s, transform 0.1s; box-shadow: 0 8px 20px rgba(15,59,92,0.25); }
        .btn-course:hover { background: #1e4b6e; transform: translateY(-2px); }
        .page-content { flex: 1; padding: 48px 0 64px; }
        .page-title { font-size: 2.4rem; font-weight: 700; color: #0f3b5c; margin-bottom: 32px; }
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 56px; }
        .course-card { background: white; border-radius: 28px; padding: 32px 28px; box-shadow: 0 12px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #f1f5f9; }
        .course-card:hover { transform: translateY(-6px); box-shadow: 0 24px 40px rgba(15,59,92,0.12); }
        .course-icon { font-size: 2.5rem; margin-bottom: 16px; }
        .course-title { font-size: 1.6rem; font-weight: 700; color: #0c2d44; margin-bottom: 8px; }
        .course-desc { color: #475569; margin-bottom: 20px; font-size: 1rem; line-height: 1.6; }
        .course-price { font-size: 2rem; font-weight: 700; color: #0f3b5c; margin-bottom: 12px; display: flex; align-items: baseline; gap: 6px; }
        .course-price span { font-size: 1rem; font-weight: 500; color: #64748b; }
        .course-meta { display: flex; gap: 16px; flex-wrap: wrap; margin-top: auto; font-size: 0.9rem; color: #334155; }
        .enroll-section { background: white; border-radius: 36px; padding: 40px 36px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); margin-top: 16px; }
        .enroll-title { font-size: 1.8rem; font-weight: 700; color: #0f3b5c; margin-bottom: 28px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .full-width { grid-column: span 2; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-weight: 600; color: #334155; font-size: 0.95rem; }
        input, select { padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem; transition: border 0.2s; background: #fafbfc; outline: none; font-family: inherit; }
        input:focus, select:focus { border-color: #0f3b5c; background: white; }
        .checkbox-group { display: flex; align-items: flex-start; gap: 12px; margin: 20px 0 10px; }
        .checkbox-group input[type="checkbox"] { width: 20px; height: 20px; margin-top: 3px; accent-color: #0f3b5c; }
        .checkbox-group label { font-weight: 400; font-size: 0.95rem; color: #334155; line-height: 1.5; }
        .submit-btn { background: #0f3b5c; color: white; border: none; padding: 16px 40px; border-radius: 40px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: 0.2s; box-shadow: 0 10px 25px rgba(15,59,92,0.3); width: fit-content; margin-top: 8px; }
        .submit-btn:hover { background: #1a4a6b; transform: scale(1.02); }
        .error-text { color: #b91c1c; font-size: 0.8rem; margin-top: 2px; display: block; }
        .success-msg { background: #e6f7e6; color: #14532d; padding: 16px 24px; border-radius: 20px; margin-bottom: 24px; font-weight: 500; }
        .footer { background: #0f2b3f; color: #e2e8f0; padding: 48px 0 32px; margin-top: auto; border-radius: 40px 40px 0 0; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; align-items: start; }
        .footer h4 { color: white; font-size: 1.2rem; margin-bottom: 16px; font-weight: 600; }
        .footer p, .footer a { color: #cbd5e1; text-decoration: none; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .footer a:hover { color: white; text-decoration: underline; }
        hr { border-color: #2d4b5e; margin: 28px 0 20px; opacity: 0.4; }
        .copyright { text-align: center; color: #94a3b8; font-size: 0.9rem; }
        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .header-inner { flex-wrap: wrap; height: auto; padding: 16px 0; gap: 12px; }
            .nav { gap: 16px; }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container header-inner">
        <a href="index.html" class="logo">
            🌐 ЛингвоМастер <span>online</span>
        </a>
        <nav class="nav">
            <a href="index.php" class="nav-link">Главная</a>
            <a href="courses.php" class="btn-course">📘 Курсы</a>
        </nav>
    </div>
</header>

<main class="page-content container">
    <h1 class="page-title">Актуальные курсы</h1>

    <!-- Динамический вывод курсов из БД -->
    <div class="courses-grid">
        <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <div class="course-icon"><?= htmlspecialchars($course['icon']) ?></div>
            <h2 class="course-title"><?= htmlspecialchars($course['name']) ?></h2>
            <p class="course-desc"><?= htmlspecialchars($course['description']) ?></p>
            <div class="course-price"><?= number_format($course['price'], 0, ',', ' ') ?> ₽ <span>за курс</span></div>
            <div class="course-meta">
                <span>📅 <?= htmlspecialchars($course['duration']) ?></span>
                <span>👥 до <?= htmlspecialchars($course['max_students']) ?> чел.</span>
                <span>💻 <?= htmlspecialchars($course['format']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Форма записи -->
    <section class="enroll-section">
        <h2 class="enroll-title">✍️ Записаться на курс</h2>

        <?php if ($success): ?>
            <div class="success-msg">🎉 Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.</div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="fullname">ФИО *</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Иванов Иван Иванович"
                           value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                    <?php if (isset($errors['fullname'])): ?>
                        <span class="error-text"><?= $errors['fullname'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" placeholder="+7(XXX)-XXX-XX-XX"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                           pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" required>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error-text"><?= $errors['phone'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" placeholder="example@mail.ru"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-text"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width">
                    <label for="course">Выберите курс *</label>
                    <select id="course" name="course" required>
                        <option value="" disabled selected>— выберите курс —</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"
                                <?= (isset($_POST['course']) && $_POST['course'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?> (<?= number_format($c['price'], 0, ',', ' ') ?> ₽)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['course'])): ?>
                        <span class="error-text"><?= $errors['course'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="consent" name="consent" <?= isset($_POST['consent']) ? 'checked' : '' ?>>
                <label for="consent">Я согласен на обработку персональных данных и принимаю условия политики конфиденциальности *</label>
            </div>
            <?php if (isset($errors['consent'])): ?>
                <span class="error-text" style="margin-bottom:16px;"><?= $errors['consent'] ?></span>
            <?php endif; ?>

            <button type="submit" class="submit-btn">Записаться</button>
        </form>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4>📍 Контакты</h4>
                <p><span>📞</span> +7 (495) 122-45-67</p>
                <p><span>✉️</span> hello@lingvomaster.ru</p>
                <p><span>🏠</span> Москва, ул. Арбат, 12, офис 34</p>
            </div>
            <div>
                <h4>🕒 Режим работы</h4>
                <p>Пн–Пт: 09:00 – 21:00</p>
                <p>Сб: 10:00 – 17:00</p>
                <p>Вс: выходной (онлайн-запись)</p>
            </div>
            <div>
                <h4>🔗 Навигация</h4>
                <a href="index.php">Главная</a>
                <a href="courses.php">Курсы</a>
                <a href="#">О компании</a>
                <a href="#">Блог</a>
            </div>
        </div>
        <hr>
        <div class="copyright">
            © 2026 ЛингвоМастер — языковые курсы. Все права защищены.
        </div>
    </div>
</footer>

<script>
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let val = e.target.value.replace(/[^\d+()-]/g, '');
        if (val.startsWith('+7') && val.length <= 17) {
            let digits = val.replace(/\D/g, '');
            if (digits.startsWith('7')) digits = digits.substring(1);
            let formatted = '+7(';
            if (digits.length > 0) formatted += digits.substring(0, 3);
            if (digits.length >= 4) formatted += ')-' + digits.substring(3, 6);
            if (digits.length >= 7) formatted += '-' + digits.substring(6, 8);
            if (digits.length >= 9) formatted += '-' + digits.substring(8, 10);
            e.target.value = formatted;
        }
    });
</script>
</body>
</html>