<?php
// Session check & Backend Placeholders
declare(strict_types=1);

require_once __DIR__ . '../includes/auth_check.php';

$user = auth_require_auth();
if ((string) $user['role_name'] !== 'Gram Sevak') {
    auth_redirect('../includes/official_login.php', 'Unauthorized access.');
}

// Integration Placeholders for Gram Sevak Module
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 101;
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Rajesh Patil (Gram Sevak)';
$_SESSION['role_id'] = $_SESSION['role_id'] ?? 2;
$_SESSION['role_name'] = $_SESSION['role_name'] ?? 'Gram Sevak';
$_SESSION['is_logged_in'] = $_SESSION['is_logged_in'] ?? true;

/* 
 * Database Contract Table References:
 * - categories (category_id, category_name)
 */
$page_title = "Complaint Category Management";
$active_page = "categories";
require_once __DIR__ . '/header.php';
?>

            <!-- Page Body -->
            <main class="gpcms-body container-fluid">
                <!-- Page Breadcrumb & Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-secondary-custom">Complaint Categories</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="gramsevak_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Categories</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm" onclick="printElement('categoryTable', 'Gram Panchayat Complaint Categories')">
                            <i class="bi bi-printer me-1"></i> Print Categories
                        </button>
                        <button class="btn btn-gpcms-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="bi bi-plus-lg me-1"></i> Add New Category
                        </button>
                    </div>
                </div>

                <!-- Search & Filter Controls -->
                <div class="gpcms-card p-3 mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchCategoryInput" placeholder="Search categories..." onkeyup="filterCategories()">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <select class="form-select form-select-sm" id="filterCategoryStatus" onchange="filterCategories()">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category List Table -->
                <div class="gpcms-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="bi bi-tags-fill me-2 text-primary-custom"></i>Database Categories (table: <code>categories</code>)</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-dark" onclick="printElement('categoryTable', 'Gram Panchayat Complaint Categories Table')">
                                <i class="bi bi-printer me-1"></i> Print Table
                            </button>
                            <span class="badge bg-secondary-subtle text-secondary" id="categoryCountBadge">Total: 6</span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 gpcms-table" id="categoryTable">
                                <thead>
                                    <tr>
                                        <th>Category ID</th>
                                        <th>Category Name</th>
                                        <th>Description</th>
                                        <th>Complaint Count</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions (UI)</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
                                    <!-- Populated dynamically via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="gpcms-footer">
                <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold">Shivaji Nagar Gram Panchayat</span> &copy; <?php echo date('Y'); ?>. All Rights Reserved.
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-secondary-subtle text-secondary">GPCMS Version 3.1</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle text-primary-custom me-2"></i>Add Complaint Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAddCategory" onsubmit="event.preventDefault(); handleAddCategory();">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newCategoryName" class="form-label font-weight-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newCategoryName" placeholder="e.g. Street Lighting" required>
                        </div>
                        <div class="mb-3">
                            <label for="newCategoryDesc" class="form-label font-weight-bold">Description</label>
                            <textarea class="form-control" id="newCategoryDesc" rows="3" placeholder="Briefly describe what complaints fall under this category..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="newCategoryStatus" class="form-label font-weight-bold">Status</label>
                            <select class="form-select" id="newCategoryStatus">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gpcms-primary btn-sm">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square text-primary-custom me-2"></i>Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditCategory" onsubmit="event.preventDefault(); handleEditCategory();">
                    <div class="modal-body">
                        <input type="hidden" id="editCategoryId">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label font-weight-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editCategoryName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryDesc" class="form-label font-weight-bold">Description</label>
                            <textarea class="form-control" id="editCategoryDesc" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryStatus" class="form-label font-weight-bold">Status</label>
                            <select class="form-select" id="editCategoryStatus">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gpcms-primary btn-sm">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i>Delete Category Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete category <strong id="deleteCategoryNameText" class="text-danger">Category</strong>?</p>
                    <small class="text-muted d-block mt-2">This will remove the category classification from database table <code>categories</code>.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDeleteCat">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell-fill text-warning me-2"></i>Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush" id="notificationList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Gram Sevak Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h5>
                    <span class="badge bg-primary-custom"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>

