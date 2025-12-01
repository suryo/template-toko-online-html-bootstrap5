<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Kelola Member</h1>
                <!-- Member registration is usually done by users themselves, but admin can add too -->
                <button onclick="showAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Member
                </button>
            </div>

            <!-- Members Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="members-table">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="member-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Tambah Member</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="member-form" onsubmit="saveMember(event)">
                <div class="modal-body">
                    <input type="hidden" id="member-id">
                    
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" id="nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Password <span id="password-hint" class="text-muted text-small" style="font-weight: normal;">(Kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" id="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" id="no-hp" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="assets/js/admin.js"></script>
    <script>
        let allMembers = [];

        // Load members
        async function loadMembers() {
            try {
                const response = await fetch(API_BASE + '/member.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allMembers = data.data;
                    displayMembers(allMembers);
                }
            } catch (error) {
                console.error('Error loading members:', error);
                showError('Gagal memuat data member');
            }
        }

        // Display members
        function displayMembers(members) {
            const tbody = document.getElementById('members-table');
            
            if (members.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            tbody.innerHTML = members.map(member => `
                <tr>
                    <td>${member.id}</td>
                    <td>${member.nama}</td>
                    <td>${member.email}</td>
                    <td>${member.no_hp || '-'}</td>
                    <td>${formatDate(member.created_at)}</td>
                    <td>
                        <button onclick="editMember(${member.id})" class="btn btn-xs btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteMember(${member.id})" class="btn btn-xs btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Show add modal
        function showAddModal() {
            document.getElementById('modal-title').textContent = 'Tambah Member';
            document.getElementById('member-form').reset();
            document.getElementById('member-id').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password-hint').style.display = 'none';
            document.getElementById('member-modal').classList.add('show');
        }

        // Edit member
        function editMember(id) {
            const member = allMembers.find(m => m.id === id);
            if (!member) return;
            
            document.getElementById('modal-title').textContent = 'Edit Member';
            document.getElementById('member-id').value = member.id;
            document.getElementById('nama').value = member.nama;
            document.getElementById('email').value = member.email;
            document.getElementById('no-hp').value = member.no_hp;
            
            // Password not required for edit
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('password-hint').style.display = 'inline';
            
            document.getElementById('member-modal').classList.add('show');
        }

        // Save member
        async function saveMember(e) {
            e.preventDefault();
            
            const id = document.getElementById('member-id').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            formData.append('nama', document.getElementById('nama').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('no_hp', document.getElementById('no-hp').value);
            
            const password = document.getElementById('password').value;
            if (password) {
                formData.append('password', password);
            }
            
            try {
                const url = id ? `${API_BASE}/member.php?id=${id}` : `${API_BASE}/member.php`;
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess(id ? 'Member berhasil diupdate' : 'Member berhasil ditambahkan');
                    closeModal();
                    loadMembers();
                } else {
                    showError(data.message || 'Gagal menyimpan member');
                }
            } catch (error) {
                console.error('Error saving member:', error);
                showError('Gagal menyimpan member');
            }
        }

        // Delete member
        async function deleteMember(id) {
            if (!confirm('Yakin ingin menghapus member ini?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/member.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Member berhasil dihapus');
                    loadMembers();
                } else {
                    showError(data.message || 'Gagal menghapus member');
                }
            } catch (error) {
                console.error('Error deleting member:', error);
                showError('Gagal menghapus member');
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('member-modal').classList.remove('show');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadMembers();
        });
    </script>
</body>
</html>
