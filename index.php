<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng - Tech Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .table-container { background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .role-badge { font-size: 0.8rem; padding: 5px 10px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-dark">Hệ Thống Quản Lý Người Dùng</h2>
            <p class="text-secondary mb-0">Kết nối database <strong>qlbanhang</strong></p>
        </div>
        <div class="col text-end">
            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Thêm Người Dùng</button>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Họ Tên</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Email</th>
                    <th>Vai Trò</th>
                    <th class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM NguoiDung");
                while ($row = $stmt->fetch()) {
                    $badgeColor = ($row['vai_tro'] == 'admin') ? 'bg-danger' : (($row['vai_tro'] == 'nhanvien') ? 'bg-primary' : 'bg-success');
                    echo "<tr>
                            <td>{$row['id_nguoidung']}</td>
                            <td><strong>{$row['ho_ten']}</strong></td>
                            <td>{$row['ten_dang_nhap']}</td>
                            <td>{$row['email']}</td>
                            <td><span class='badge {$badgeColor} role-badge'>{$row['vai_tro']}</span></td>
                            <td class='text-center'>
                                <button class='btn btn-sm btn-outline-secondary' onclick=\"showEditModal('{$row['id_nguoidung']}', '{$row['ho_ten']}', '{$row['email']}', '{$row['vai_tro']}')\">Sửa</button>
                                <button class='btn btn-sm btn-outline-danger' onclick=\"executeDelete('{$row['id_nguoidung']}')\">Xóa</button>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Thêm tài khoản mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3"><label>Họ và tên</label><input type="text" id="addFullName" class="form-control" required></div>
                    <div class="mb-3"><label>Tên đăng nhập</label><input type="text" id="addUserName" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" id="addEmail" class="form-control" required></div>
                    <div class="mb-3"><label>Vai trò</label>
                        <select id="addRole" class="form-select">
                            <option value="khachhang">Khách hàng</option>
                            <option value="nhanvien">Nhân viên</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu thông tin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Chỉnh sửa thông tin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3"><label>Họ và tên</label><input type="text" id="editFullName" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" id="editEmail" class="form-control" required></div>
                    <div class="mb-3"><label>Vai trò hệ thống</label>
                        <select id="editRole" class="form-select">
                            <option value="khachhang">Khách hàng</option>
                            <option value="nhanvien">Nhân viên</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 ms-auto">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm tên hoặc email...">
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = 'api_user.php';

    // Đổ dữ liệu vào Modal Sửa
    function showEditModal(id, name, email, role) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editFullName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    // Xử lý Thêm mới
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            ten_dang_nhap: document.getElementById('addUserName').value,
            ho_ten: document.getElementById('addFullName').value,
            email: document.getElementById('addEmail').value,
            vai_tro: document.getElementById('addRole').value,
            mat_khau: '123456'
        };
        fetch(`${API_URL}?action=create`, { method: 'POST', body: JSON.stringify(data) })
        .then(res => res.json()).then(res => { if(res.status === 'success') location.reload(); });
    });

    // Xử lý Cập nhật
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            id_nguoidung: document.getElementById('editUserId').value,
            ho_ten: document.getElementById('editFullName').value,
            email: document.getElementById('editEmail').value,
            vai_tro: document.getElementById('editRole').value
        };
        fetch(`${API_URL}?action=update`, { method: 'POST', body: JSON.stringify(data) })
        .then(res => res.json()).then(res => { if(res.status === 'success') location.reload(); });
    });

    // Xử lý Xóa
    function executeDelete(id) {
        if(confirm("Xác nhận xóa ID: " + id + "?")) {
            fetch(`${API_URL}?action=delete&id=${id}`).then(res => res.json())
            .then(res => { if(res.status === 'success') location.reload(); else alert("Lỗi: Người dùng có hóa đơn không thể xóa!"); });
        }
    }
    // Hàm này sẽ được gọi tự động khi trang web tải xong
window.onload = function() {
    loadUsers();
};

// 1. Cập nhật hàm loadUsers
function loadUsers(query = '') {
    fetch(`api_user.php?action=read&search=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            const tableBody = document.querySelector('tbody');
            tableBody.innerHTML = ''; 

            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Không tìm thấy người dùng nào.</td></tr>';
                return;
            }

            data.forEach(user => {
                let badgeClass = user.vai_tro === 'admin' ? 'bg-danger' : (user.vai_tro === 'nhanvien' ? 'bg-primary' : 'bg-success');
                tableBody.innerHTML += `
                    <tr>
                        <td>${user.id_nguoidung}</td>
                        <td><strong>${user.ho_ten}</strong></td>
                        <td>${user.ten_dang_nhap}</td>
                        <td>${user.email}</td>
                        <td><span class="badge ${badgeClass} role-badge">${user.vai_tro}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary me-1" onclick="showEditModal(${user.id_nguoidung}, '${user.ho_ten}', '${user.email}', '${user.vai_tro}')">Sửa</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="executeDelete(${user.id_nguoidung})">Xóa</button>
                        </td>
                    </tr>`;
            });
        });
}

// 2. Lắng nghe sự kiện gõ phím vào ô tìm kiếm
document.getElementById('searchInput').addEventListener('input', function(e) {
    const keyword = e.target.value;
    loadUsers(keyword); // Gọi lại hàm load với từ khóa mới
});
</script>
</body>
</html>
