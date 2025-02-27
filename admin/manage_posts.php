<?php
// Include database connection
include('../config/db.php');

// Fetch all posts from the database
$query = "SELECT * FROM blogs";
$result = mysqli_query($conn, $query);

$sn = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 30px;
            max-width: 1200px;
        }
        .header_card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .post-title-link {
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        .post-title-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        .table th {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .table th.sortable {
            cursor: pointer;
        }
        .table th.sorted {
            font-weight: bold;
            background-color: #0056b3;
        }
        .table tbody tr {
            transition: opacity 0.5s ease, background-color 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table tbody tr.selected {
            background-color: #e9ecef;
        }
        .btn i {
            margin-right: 5px;
        }
        .btn-danger.deleting {
            opacity: 0.6;
            cursor: wait;
        }
        .no-posts {
            text-align: center;
            color: #6c757d;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        .form-check-input {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header_card">
            <h2><i class="fas fa-newspaper"></i> Manage Posts</h2>
            <a href="dashboard.php?view=createPosts" class="btn btn-success">
                <i class="fas fa-plus"></i> Create New Post
            </a>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-3 d-flex justify-content-between">
            <div class="d-flex">
                <select class="form-select me-2" id="bulkAction" style="width: auto;">
                    <option value="">Bulk Actions</option>
                    <option value="delete">Delete Selected</option>
                    <option value="publish">Publish Selected</option>
                    <option value="draft">Set as Draft</option>
                </select>
                <button class="btn btn-primary" id="bulkGo">Go</button>
            </div>
            <input type="text" class="form-control" id="searchInput" placeholder="Search posts..." aria-label="Search posts" style="max-width: 300px;">
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th class="sortable">Title <i class="fas fa-sort"></i></th>
                        <th class="sortable">Author <i class="fas fa-sort"></i></th>
                        <th>Status</th>
                        <th class="sortable">Last Modified <i class="fas fa-sort"></i></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="7" class="no-posts">No posts found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select" data-id="<?php echo $row['id']; ?>"></td>
                                <td><?php echo $sn++; ?></td>
                                <td>
                                    <a href="dashboard.php?view=post&id=<?php echo $row['id']; ?>" class="post-title-link">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] === 'published' ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                    <div class="form-check form-switch d-inline-block ms-2">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               data-id="<?php echo $row['id']; ?>" 
                                               <?php echo $row['status'] === 'published' ? 'checked' : ''; ?>>
                                    </div>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($row['last_modified'])); ?></td>
                                <td>
                                    <a href="dashboard.php?view=editPost&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" aria-label="Edit post <?php echo htmlspecialchars($row['title']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>" aria-label="Delete post <?php echo htmlspecialchars($row['title']); ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <div class="toast" id="successToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
        <div class="toast" id="errorToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.querySelector('.table');
            const rows = document.querySelectorAll('.table tbody tr');
            const toastSuccess = new bootstrap.Toast(document.getElementById('successToast'));
            const toastError = new bootstrap.Toast(document.getElementById('errorToast'));

            // Show Toast
            function showToast(type, message) {
                const toast = type === 'success' ? toastSuccess : toastError;
                toast.element.querySelector('.toast-body').textContent = message;
                toast.show();
            }

            // Search Functionality
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const author = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    row.style.display = (title.includes(searchTerm) || author.includes(searchTerm)) ? '' : 'none';
                });
            });

            // Sorting Functionality
            const headers = document.querySelectorAll('.table thead th.sortable');
            let currentSortColumn = null;
            let currentSortOrder = 'asc';

            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
                    if (currentSortColumn === columnIndex) {
                        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSortColumn = columnIndex;
                        currentSortOrder = 'asc';
                    }

                    headers.forEach(h => {
                        h.classList.remove('sorted');
                        const i = h.querySelector('i');
                        i.classList.remove('fa-sort-up', 'fa-sort-down');
                        i.classList.add('fa-sort');
                    });

                    header.classList.add('sorted');
                    const icon = header.querySelector('i');
                    icon.classList.remove('fa-sort');
                    icon.classList.add(currentSortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');

                    const sortedRows = Array.from(rows).sort((a, b) => {
                        const aText = a.children[columnIndex].textContent.trim();
                        const bText = b.children[columnIndex].textContent.trim();
                        return currentSortOrder === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
                    });

                    const tbody = document.querySelector('.table tbody');
                    sortedRows.forEach(row => tbody.appendChild(row));
                });
            });

            // Delete Functionality
            table.addEventListener('click', function(event) {
                const deleteBtn = event.target.closest('.delete-btn');
                if (deleteBtn) {
                    event.preventDefault();
                    const postId = deleteBtn.dataset.id;
                    if (confirm('Are you sure you want to delete this post?')) {
                        deleteBtn.classList.add('deleting');
                        fetch(`../blog/delete_post.php?id=${postId}`)
                            .then(response => response.json())
                            .then(data => {
                                deleteBtn.classList.remove('deleting');
                                if (data.success) {
                                    const row = deleteBtn.closest('tr');
                                    row.style.opacity = '0';
                                    setTimeout(() => row.remove(), 500);
                                    showToast('success', 'Post deleted successfully.');
                                } else {
                                    showToast('error', data.message || 'Failed to delete post.');
                                }
                            })
                            .catch(error => {
                                deleteBtn.classList.remove('deleting');
                                showToast('error', 'An error occurred.');
                            });
                    }
                }
            });

            // Status Toggle
            table.addEventListener('change', function(event) {
                const toggle = event.target.closest('.status-toggle');
                if (toggle) {
                    const postId = toggle.dataset.id;
                    const newStatus = toggle.checked ? 'published' : 'draft';
                    fetch(`../blog/update_status.php?id=${postId}&status=${newStatus}`, { method: 'POST' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const badge = toggle.closest('td').querySelector('.badge');
                                badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                                badge.classList.toggle('bg-success', newStatus === 'published');
                                badge.classList.toggle('bg-secondary', newStatus === 'draft');
                                showToast('success', `Post set to ${newStatus}.`);
                            } else {
                                toggle.checked = !toggle.checked; // Revert on failure
                                showToast('error', data.message || 'Failed to update status.');
                            }
                        })
                        .catch(() => {
                            toggle.checked = !toggle.checked;
                            showToast('error', 'An error occurred.');
                        });
                }
            });

            // Bulk Actions
            const selectAll = document.getElementById('selectAll');
            const rowSelects = document.querySelectorAll('.row-select');
            const bulkAction = document.getElementById('bulkAction');
            const bulkGo = document.getElementById('bulkGo');

            selectAll.addEventListener('change', function() {
                rowSelects.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    checkbox.closest('tr').classList.toggle('selected', this.checked);
                });
            });

            rowSelects.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    this.closest('tr').classList.toggle('selected', this.checked);
                    selectAll.checked = Array.from(rowSelects).every(cb => cb.checked);
                });
            });

            bulkGo.addEventListener('click', function() {
                const action = bulkAction.value;
                if (!action) return showToast('error', 'Please select an action.');
                const selectedIds = Array.from(rowSelects)
                    .filter(cb => cb.checked)
                    .map(cb => cb.dataset.id);
                if (selectedIds.length === 0) return showToast('error', 'No posts selected.');

                if (action === 'delete' && !confirm('Delete selected posts?')) return;

                fetch('../blog/bulk_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, ids: selectedIds })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            selectedIds.forEach(id => {
                                const row = document.querySelector(`tr td input[data-id="${id}"]`).closest('tr');
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 500);
                            });
                            showToast('success', `Bulk ${action} completed.`);
                        } else {
                            showToast('error', data.message || 'Bulk action failed.');
                        }
                    })
                    .catch(() => showToast('error', 'An error occurred.'));
            });
        });
    </script>
</body>
</html>