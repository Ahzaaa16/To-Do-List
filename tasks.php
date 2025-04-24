<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="col-md-10 p-4">
  <?php
  // Koneksi ke database
  $conn = new mysqli("localhost", "root", "", "todo_app", 3308);

  // Tambah / Update tugas
  if (isset($_POST['add'])) {
    $id = $_POST['task_id'];
    $name = $_POST['task_name'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    if (!empty($id)) {
      $conn->query("UPDATE tasks SET task_name='$name', description='$description', priority='$priority', status='$status' WHERE id=$id");
    } else {
      $conn->query("INSERT INTO tasks (task_name, description, priority, status) VALUES ('$name', '$description', '$priority', 'belum selesai')");
    }
  }

  // Ubah status tugas
  if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $conn->query("UPDATE tasks SET status='$status' WHERE id=$id");
  }

  // Hapus tugas
  if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
  }

  // Filter dan pencarian
  $where = "1";
  $searchParam = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
  $filterParam = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';

  if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $where .= " AND task_name LIKE '%$search%'";
  }

  if (!empty($_GET['filter'])) {
    $filter = $_GET['filter'];
    $where .= " AND status = '$filter'";
  }

  $tugas = $conn->query("SELECT * FROM tasks WHERE $where ORDER BY id ASC");
  ?>

  <h3 class="mb-4 fw-bold">Daftar Tugas</h3>

  <!-- Form tambah/update tugas -->
  <!-- Form tambah/update tugas -->
  <form method="POST" class="row g-3 mb-4" id="task-form">
    <input type="hidden" name="task_id" id="task_id">

    <div class="col-md-2">
      <input type="text" id="task_id_display" class="form-control" placeholder="ID" readonly>
    </div>

    <div class="col-md-3">
      <input type="text" name="task_name" id="task_name" placeholder="Nama tugas" class="form-control" required>
    </div>

    <div class="col-md-3">
      <input type="text" name="description" id="description" placeholder="Deskripsi" class="form-control" required>
    </div>

    <div class="col-md-2">
      <select name="priority" id="priority" class="form-select" required>
        <option value="">Prioritas</option>
        <option value="rendah">Rendah</option>
        <option value="sedang">Sedang</option>
        <option value="tinggi">Tinggi</option>
      </select>
    </div>

    <div class="col-md-2">
      <select name="status" id="status" class="form-select" required>
        <option value="">Status</option>
        <option value="belum selesai">Belum Selesai</option>
        <option value="selesai">Selesai</option>
      </select>
    </div>

    <div class="col-md-2">
      <button name="add" type="submit" class="btn btn-primary w-100 fw-bold" id="submit-button">Tambah</button>
    </div>
  </form>


  <!-- Filter dan cari -->
  <form method="GET" class="row g-2 mb-3 align-items-center">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Cari tugas..." value="<?= $_GET['search'] ?? '' ?>">
    </div>

    <div class="col-md-3">
      <select name="filter" class="form-select" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="belum selesai" <?= (($_GET['filter'] ?? '') == 'belum selesai') ? 'selected' : '' ?>>Belum Selesai</option>
        <option value="selesai" <?= (($_GET['filter'] ?? '') == 'selesai') ? 'selected' : '' ?>>Selesai</option>
      </select>
    </div>

    <div class="col-md-1">
      <button type="submit" class="btn btn-primary w-100 fw-bold">Cari</button>
    </div>

    <div class="col-md-1">
      <a href="?" class="btn btn-primary w-100 fw-bold">Reset</a>
    </div>
  </form>

  <!-- Tabel tugas -->
  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Nama Tugas</th>
        <th>Deskripsi</th>
        <th>Prioritas</th>
        <th>Status</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($tugas->num_rows > 0): ?>
        <?php while ($row = $tugas->fetch_assoc()): ?>
          <tr onclick="fillForm('<?= $row['id'] ?>', '<?= htmlspecialchars($row['task_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>', '<?= $row['priority'] ?>', '<?= $row['status'] ?>')">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['task_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= ucfirst($row['priority']) ?></td>
            <td>
              <a href="?id=<?= $row['id'] ?>&status=<?= $row['status'] == 'selesai' ? 'belum selesai' : 'selesai' ?><?= $searchParam . $filterParam ?>"
                class="btn btn-sm fw-bold <?= $row['status'] == 'selesai' ? 'btn-success text-white' : 'btn-warning text-white' ?>">
                <?= ucwords($row['status']) ?>
              </a>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <a href="?delete=<?= $row['id'] . $searchParam . $filterParam ?>"
                class="btn btn-danger btn-sm fw-bold"
                onclick="return confirm('Yakin ingin menghapus?')">
                Hapus
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-danger fw-bold py-3">
            Data tidak ditemukan atau ada kesalahan penulisan.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Script form otomatis terisi -->
<script>
  function fillForm(id, name, description, priority, status) {
    document.getElementById('task_id').value = id;
    document.getElementById('task_id_display').value = id;
    document.getElementById('task_name').value = name;
    document.getElementById('description').value = description; // Isi deskripsi dengan benar
    document.getElementById('priority').value = priority;
    document.getElementById('status').value = status;
    document.getElementById('submit-button').textContent = "Update";
  }

  // Reset form saat load pertama
  window.onload = () => {
    document.getElementById('task-form').reset();
  };
</script>

<?php include 'footer.php'; ?>