<?php
require 'config.php';
requireAdmin();
$pdo = connect_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tech = trim($_POST['tech'] ?? '');
    $repoUrl = trim($_POST['repo_url'] ?? '');
    $liveUrl = trim($_POST['live_url'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'save') {
        if ($title === '' || $description === '' || $tech === '') {
            $message = 'Title, description, and technologies are required.';
        } elseif ($id > 0) {
            $stmt = $pdo->prepare('UPDATE projects SET title = :title, subtitle = :subtitle, description = :description, tech = :tech, repo_url = :repo_url, live_url = :live_url, image_url = :image_url, sort_order = :sort_order WHERE id = :id');
            $stmt->execute([
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':description' => $description,
                ':tech' => $tech,
                ':repo_url' => $repoUrl,
                ':live_url' => $liveUrl,
                ':image_url' => $imageUrl,
                ':sort_order' => $sortOrder,
                ':id' => $id,
            ]);
            $message = 'Project updated successfully.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO projects (title, subtitle, description, tech, repo_url, live_url, image_url, sort_order, created_at) VALUES (:title, :subtitle, :description, :tech, :repo_url, :live_url, :image_url, :sort_order, NOW())');
            $stmt->execute([
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':description' => $description,
                ':tech' => $tech,
                ':repo_url' => $repoUrl,
                ':live_url' => $liveUrl,
                ':image_url' => $imageUrl,
                ':sort_order' => $sortOrder,
            ]);
            $message = 'Project added successfully.';
        }
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    if ($deleteId > 0) {
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([':id' => $deleteId]);
        $message = 'Project deleted successfully.';
    }
    header('Location: dashboard.php');
    exit;
}

$editProject = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id');
        $stmt->execute([':id' => $editId]);
        $editProject = $stmt->fetch();
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Masud Portfolio</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <nav id="nav" style="position:relative; padding:1.5rem 2rem; background:var(--surface); border-bottom:1px solid var(--border);">
    <a href="index.html" class="nav-logo">M<span>.</span></a>
    <div class="nav-actions">
      <a href="logout.php" class="btn-ghost" style="font-size:0.75rem; padding:0.7rem 1rem;">Logout</a>
    </div>
  </nav>
  <main style="max-width:1100px; margin:3rem auto; padding:0 1.5rem;">
    <section class="section-label" style="margin-bottom:1rem;">Admin Dashboard</section>
    <h1 class="section-title">Manage Projects</h1>
    <?php if ($message): ?>
      <div class="form-status" style="margin-bottom:1.5rem; color:var(--accent);"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <section class="contact-form">
      <form method="post" novalidate>
        <input type="hidden" name="id" value="<?= htmlspecialchars($editProject['id'] ?? '') ?>">
        <input type="hidden" name="action" value="save">
        <label>
          Project Title
          <input type="text" name="title" value="<?= htmlspecialchars($editProject['title'] ?? '') ?>" required>
        </label>
        <label>
          Subtitle
          <input type="text" name="subtitle" value="<?= htmlspecialchars($editProject['subtitle'] ?? '') ?>">
        </label>
        <label class="full-width">
          Description
          <textarea name="description" rows="4" required><?= htmlspecialchars($editProject['description'] ?? '') ?></textarea>
        </label>
        <label>
          Technologies
          <input type="text" name="tech" value="<?= htmlspecialchars($editProject['tech'] ?? '') ?>" placeholder="HTML, CSS, JS">
        </label>
        <label>
          GitHub / Repo URL
          <input type="url" name="repo_url" value="<?= htmlspecialchars($editProject['repo_url'] ?? '') ?>" placeholder="https://github.com/...">
        </label>
        <label>
          Live Demo URL
          <input type="url" name="live_url" value="<?= htmlspecialchars($editProject['live_url'] ?? '') ?>" placeholder="https://example.com">
        </label>
        <label class="full-width">
          Image URL
          <input type="url" name="image_url" value="<?= htmlspecialchars($editProject['image_url'] ?? '') ?>" placeholder="Optional image URL">
        </label>
        <label>
          Sort order
          <input type="number" name="sort_order" value="<?= htmlspecialchars($editProject['sort_order'] ?? 0) ?>">
        </label>
        <div class="form-actions">
          <button type="submit" class="btn-primary"><?= $editProject ? 'Update Project' : 'Add Project' ?></button>
        </div>
      </form>
    </section>

    <h2 class="section-title" style="margin-top:3rem;">Existing Projects</h2>
    <div class="projects-grid" style="margin-top:1rem;">
      <?php foreach ($projects as $project): ?>
        <div class="project-card">
          <div class="project-num">ID <?= htmlspecialchars($project['id']) ?></div>
          <div class="project-name"><?= htmlspecialchars($project['title']) ?></div>
          <div class="project-desc"><?= htmlspecialchars(substr($project['description'], 0, 120)) ?>...</div>
          <div class="project-tech"><?= htmlspecialchars($project['tech']) ?></div>
          <div class="project-links">
            <a href="dashboard.php?edit=<?= htmlspecialchars($project['id']) ?>" class="project-link">Edit</a>
            <a href="dashboard.php?delete=<?= htmlspecialchars($project['id']) ?>" class="project-link" onclick="return confirm('Delete this project?');">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</body>
</html>
