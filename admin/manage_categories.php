<?php
/**
 * admin/manage_categories.php
 * GPCMS — Category CRUD Backend and UI
 * Admin-only access controlled. Follows handbook.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db_connect.php';

// Access Control check
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role_name'], ['Administrator', 'Super Admin', 'Gram Panchayat Admin'], true)) {
    header("Location: ../index.php");
    exit;
}

$conn = get_db_connection();
$error = '';
$success = '';

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $category_name = trim($_POST['category_name'] ?? '');
        $category_description = trim($_POST['category_description'] ?? '');
        
        if ($category_name === '') {
            $error = 'Category name is required.';
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (category_name, category_description) VALUES (?, ?)");
            $stmt->bind_param("ss", $category_name, $category_description);
            if ($stmt->execute()) {
                $success = 'Category created successfully.';
            } else {
                $error = 'Failed to create category: ' . $conn->error;
            }
            $stmt->close();
        }
    } elseif ($action === 'update') {
        $category_id = (int)($_POST['category_id'] ?? 0);
        $category_name = trim($_POST['category_name'] ?? '');
        $category_description = trim($_POST['category_description'] ?? '');
        
        if ($category_id <= 0 || $category_name === '') {
            $error = 'Invalid category details.';
        } else {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_description = ? WHERE category_id = ?");
            $stmt->bind_param("ssi", $category_name, $category_description, $category_id);
            if ($stmt->execute()) {
                $success = 'Category updated successfully.';
            } else {
                $error = 'Failed to update category: ' . $conn->error;
            }
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        $category_id = (int)($_POST['category_id'] ?? 0);
        if ($category_id <= 0) {
            $error = 'Invalid category ID.';
        } else {
            // Check if there are complaints referencing this category
            $chk = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE category_id = ?");
            $chk->bind_param("i", $category_id);
            $chk->execute();
            $chk->bind_result($cnt);
            $chk->fetch();
            $chk->close();
            
            if ($cnt > 0) {
                $error = 'Cannot delete category. It is linked to existing complaints.';
            } else {
                $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
                $stmt->bind_param("i", $category_id);
                if ($stmt->execute()) {
                    $success = 'Category deleted successfully.';
                } else {
                    $error = 'Failed to delete category: ' . $conn->error;
                }
                $stmt->close();
            }
        }
    }
}

// Fetch all categories
$categories = [];
$res = $conn->query("SELECT category_id, category_name, category_description FROM categories ORDER BY category_id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}

$adminName = htmlspecialchars($_SESSION['full_name'] ?? 'Panchayat Admin');
$adminInitial = strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Categories — GPCMS Enterprise</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/analytics.css" rel="stylesheet">
</head>
<body>
<header class="app-header" role="banner">
    <div class="header-brand">
        <div class="brand-icon"><i class="bi bi-bank2"></i></div>
        <div class="brand-name">GPCMS Admin<small>Gram Panchayat Enterprise</small></div>
    </div>
    <div class="header-right">
        <div class="dropdown">
            <div class="profile-trigger" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                <div class="profile-avatar"><?= $adminInitial ?></div>
                <div class="profile-info d-none d-md-flex">
                    <span class="profile-name"><?= $adminName ?></span>
                    <span class="profile-role">Administrator</span>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="analytics_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="../includes/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<nav class="app-sidebar" id="appSidebar">
    <div class="sidebar-nav">
        <div class="sidebar-label">MAIN NAVIGATION</div>
        <ul role="list">
            <li class="nav-item-custom"><a href="analytics_dashboard.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-speedometer2"></i></span><span class="nav-text">Dashboard</span></a></li>
            <li class="nav-item-custom"><a href="manage_categories.php" class="nav-link-custom active"><span class="nav-icon"><i class="bi bi-tags-fill"></i></span><span class="nav-text">Manage Categories</span></a></li>
            <li class="nav-item-custom"><a href="category_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-collection-fill"></i></span><span class="nav-text">Category Reports</span></a></li>
            <li class="nav-item-custom"><a href="village_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-geo-alt-fill"></i></span><span class="nav-text">Village Reports</span></a></li>
            <li class="nav-item-custom"><a href="pending_resolved_report.php" class="nav-link-custom"><span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span><span class="nav-text">Resolution Status</span></a></li>
        </ul>
    </div>
</nav>

<main class="app-main">
<div class="page-content">
<div class="content-main">
    <div class="page-header anim-fadeInUp">
        <div>
            <h1 class="page-title"><i class="bi bi-tags-fill me-2 text-primary-gp"></i>Manage Categories</h1>
            <div class="page-subtitle">Add, edit, or delete GPCMS complaint categories.</div>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title" id="formTitle">Create Category</div>
                </div>
                <div class="section-card-body">
                    <form method="POST" id="categoryForm">
                        <input type="hidden" name="action" id="actionField" value="create">
                        <input type="hidden" name="category_id" id="categoryIdField" value="">
                        
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" id="categoryNameField" class="form-control" required placeholder="e.g., Sanitation">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="category_description" id="categoryDescriptionField" class="form-control" rows="4" placeholder="Brief description of issues under this category..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="submitBtn">Create</button>
                            <button type="button" class="btn btn-surface" id="cancelBtn" style="display:none;" onclick="resetForm()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">Existing Categories</div>
                </div>
                <div class="section-card-body pb-0">
                    <div class="data-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td>#<?= $cat['category_id'] ?></td>
                                        <td class="fw-600"><?= htmlspecialchars($cat['category_name']) ?></td>
                                        <td class="text-muted-gp"><?= htmlspecialchars($cat['category_description'] ?? '') ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-surface py-1 px-2" onclick="editCategory(<?= $cat['category_id'] ?>, '<?= htmlspecialchars(addslashes($cat['category_name'])) ?>', '<?= htmlspecialchars(addslashes($cat['category_description'] ?? '')) ?>')"><i class="bi bi-pencil-fill"></i></button>
                                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-surface py-1 px-2 text-danger"><i class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted-gp">No categories defined in the system.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCategory(id, name, desc) {
    document.getElementById('formTitle').innerText = 'Edit Category #' + id;
    document.getElementById('actionField').value = 'update';
    document.getElementById('categoryIdField').value = id;
    document.getElementById('categoryNameField').value = name;
    document.getElementById('categoryDescriptionField').value = desc;
    document.getElementById('submitBtn').innerText = 'Update';
    document.getElementById('cancelBtn').style.display = 'inline-block';
}

function resetForm() {
    document.getElementById('formTitle').innerText = 'Create Category';
    document.getElementById('actionField').value = 'create';
    document.getElementById('categoryIdField').value = '';
    document.getElementById('categoryForm').reset();
    document.getElementById('submitBtn').innerText = 'Create';
    document.getElementById('cancelBtn').style.display = 'none';
}
</script>
</body>
</html>
