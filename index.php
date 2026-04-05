<?php
session_start();

$users = [
    'student' => ['username' => 'student1', 'password' => 'User2024'],
    'teacher' => ['username' => 'teacher1', 'password' => 'User2024'],
];

$data = [
    'student' => [
        'welcome' => 'Student Dashboard',
        'timetable' => [
            ['time' => '09:00 - 10:00', 'course' => 'English Literature', 'room' => 'Room A'],
            ['time' => '10:15 - 11:15', 'course' => 'Mathematics', 'room' => 'Room B'],
            ['time' => '11:30 - 12:30', 'course' => 'Biology', 'room' => 'Room C'],
        ],
        'courses' => ['English Literature', 'Mathematics', 'Biology', 'Physical Education'],
    ],
    'teacher' => [
        'welcome' => 'Teacher Dashboard',
        'timetable' => [
            ['time' => '08:30 - 09:30', 'course' => 'Physics', 'room' => 'Lab 2'],
            ['time' => '10:00 - 11:00', 'course' => 'Calculus', 'room' => 'Room D'],
            ['time' => '11:30 - 12:30', 'course' => 'History', 'room' => 'Room E'],
        ],
        'courses' => ['Physics', 'Calculus', 'History', 'Computer Science'],
    ],
];

$message = '';
$selectedRole = 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $selectedRole = $_POST['role'] ?? 'student';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (isset($users[$selectedRole]) && $username === $users[$selectedRole]['username'] && $password === $users[$selectedRole]['password']) {
        $_SESSION['role'] = $selectedRole;
        $_SESSION['username'] = $username;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $message = 'Invalid username or password. Please try again.';
}

$loggedIn = isset($_SESSION['role']) && isset($data[$_SESSION['role']]);
if ($loggedIn) {
    $selectedRole = $_SESSION['role'];
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student / Teacher Portal</title>
  <link rel="stylesheet" href="index.css" />
</head>
<body>
  <div class="page-shell">
    <header class="hero">
      <div>
        <p class="eyebrow">Campus Portal</p>
        <h1>Student & Teacher Login</h1>
        <p>Access course timetables, download materials, and view offered courses.</p>
      </div>
    </header>

    <main class="content">
      <?php if (!$loggedIn): ?>
      <section class="auth-panel">
        <div class="tabs">
          <button type="button" id="studentTab" class="tab-button<?= $selectedRole === 'student' ? ' active' : '' ?>" data-role="student">Student</button>
          <button type="button" id="teacherTab" class="tab-button<?= $selectedRole === 'teacher' ? ' active' : '' ?>" data-role="teacher">Teacher</button>
        </div>

        <form method="post" id="loginForm" class="login-form">
          <input type="hidden" name="role" id="roleInput" value="<?= escape($selectedRole) ?>" />

          <div class="field-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required value="<?= escape($_POST['username'] ?? '') ?>" />
          </div>
          <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required />
          </div>
          <button type="submit" class="primary-button">Sign In</button>
          <p class="help-text">Use <strong>student1 / User2024</strong> or <strong>teacher1 / User2024</strong>.</p>
          <?php if ($message): ?>
            <p class="message error"><?= escape($message) ?></p>
          <?php else: ?>
            <p class="message">&nbsp;</p>
          <?php endif; ?>
        </form>
      </section>
      <?php endif; ?>

      <?php if ($loggedIn): ?>
      <?php $roleData = $data[$selectedRole]; ?>
      <section class="portal">
        <div class="portal-header">
          <div>
            <p class="status-label">Logged in as <span id="currentRole"><?= ucfirst(escape($selectedRole)) ?></span></p>
            <h2 id="welcomeText"><?= escape($roleData['welcome']) ?></h2>
          </div>
          <form method="post" style="margin:0;">
            <button type="submit" name="logout" class="secondary-button">Log out</button>
          </form>
        </div>

        <div class="cards-grid">
          <article class="card">
            <h3>Today's Timetable</h3>
            <table class="timetable">
              <thead>
                <tr><th>Time</th><th>Course</th><th>Room</th></tr>
              </thead>
              <tbody>
                <?php foreach ($roleData['timetable'] as $item): ?>
                <tr>
                  <td><?= escape($item['time']) ?></td>
                  <td><?= escape($item['course']) ?></td>
                  <td><?= escape($item['room']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </article>

          <article class="card">
            <h3>Courses Offered</h3>
            <ul class="course-list">
              <?php foreach ($roleData['courses'] as $course): ?>
                <li><?= escape($course) ?></li>
              <?php endforeach; ?>
            </ul>
          </article>

          <article class="card downloads-card">
            <h3>Download PDFs</h3>
            <p>Select a resource to download or preview.</p>
            <ul class="download-list">
              <li><a href="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" download>Class Schedule PDF</a></li>
              <li><a href="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" download>Course Outline PDF</a></li>
              <li><a href="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" download>Teacher Notes PDF</a></li>
            </ul>
          </article>
        </div>
      </section>
      <?php endif; ?>
    </main>
  </div>

  <script>
    const studentTab = document.getElementById('studentTab');
    const teacherTab = document.getElementById('teacherTab');
    const roleInput = document.getElementById('roleInput');

    if (studentTab && teacherTab && roleInput) {
      studentTab.addEventListener('click', () => {
        studentTab.classList.add('active');
        teacherTab.classList.remove('active');
        roleInput.value = 'student';
      });

      teacherTab.addEventListener('click', () => {
        teacherTab.classList.add('active');
        studentTab.classList.remove('active');
        roleInput.value = 'teacher';
      });
    }
  </script>
</body>
</html>
