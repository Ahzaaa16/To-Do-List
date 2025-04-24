<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
$conn = new mysqli("localhost", "root", "", "todo_app", 3308);
$totalTasks = $conn->query("SELECT COUNT(*) FROM tasks")->fetch_row()[0];
$completed = $conn->query("SELECT COUNT(*) FROM tasks WHERE status = 'selesai'")->fetch_row()[0];
$pending = $conn->query("SELECT COUNT(*) FROM tasks WHERE status = 'belum selesai'")->fetch_row()[0];
$progress = $totalTasks > 0 ? round(($completed / $totalTasks) * 100) : 0;
// Statistik tugas
$countSelesai = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'selesai'")->fetch_assoc()['total'];
$countBelum = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'belum selesai'")->fetch_assoc()['total'];

$countRendah = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE priority = 'rendah'")->fetch_assoc()['total'];
$countSedang = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE priority = 'sedang'")->fetch_assoc()['total'];
$countTinggi = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE priority = 'tinggi'")->fetch_assoc()['total'];
?>

<!-- Main Content -->
<div class="col-md-10">
  <div class="p-4">

    <!-- Title -->
    <h1 class="mb-4 fw-bold">Dashboard</h1>

    <!-- Statistic Cards -->
    <div class="row g-4">
      <!-- Card 1: Total Tugas -->
      <div class="col-md-4">
        <div class="border rounded p-3 text-primary border-primary shadow-sm bg-light card-hover">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Total Tugas</h5>
            <i class="bi bi-list-task fs-4"></i>
          </div>
          <h2 class="mt-2"><?= $totalTasks ?></h2>
        </div>
      </div>

      <!-- Card 2: Tugas Selesai -->
      <div class="col-md-4">
        <div class="border rounded p-3 text-success border-success shadow-sm bg-light">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tugas Selesai</h5>
            <i class="bi bi-check-circle fs-4"></i>
          </div>
          <h2 class="mt-2"><?= $completed ?></h2>
        </div>
      </div>

      <!-- Card 3: Belum Selesai -->
      <div class="col-md-4">
        <div class="border rounded p-3 text-warning border-warning shadow-sm bg-light">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Belum Selesai</h5>
            <i class="bi bi-exclamation-circle fs-4"></i>
          </div>
          <h2 class="mt-2"><?= $pending ?></h2>
        </div>
      </div>
    </div>

    <!-- Chart Container -->
    <div class="row mt-5">
      <!-- Status Donut Chart -->
      <div class="col-md-6 mb-4">
        <div class="card p-3">
          <h5 class="fw-bold mb-3">Status Tugas</h5>
          <canvas id="statusChart" width="400" height="400"></canvas>
        </div>
      </div>

      <!-- Prioritas Bar Chart -->
      <div class="col-md-6 mb-4">
        <div class="card p-3">
          <h5 class="fw-bold mb-3">Prioritas Tugas</h5>
          <canvas id="priorityChart" width="400" height="400"></canvas>
        </div>
      </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="mt-5 card p-3">
      <h5 class="mb-3 fw-bold">Progress Keseluruhan</h5>
      <div class="progress" style="height: 25px;">
        <div
          class="progress-bar bg-success"
          role="progressbar"
          style="width: <?= $progress ?>%;"
          aria-valuenow="<?= $progress ?>"
          aria-valuemin="0"
          aria-valuemax="100">
          <?= $progress ?>%
        </div>
      </div>
    </div>

    <!-- Tugas Terbaru Table -->
    <div class="mt-5 card p-4">
      <h5 class="mb-4 fw-bold">Tugas Terbaru</h5>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Nama Tugas</th>
              <th>Prioritas</th>
              <th>Status</th>
              <th>Dibuat</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $latestTasks = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC LIMIT 5");
            while ($row = $latestTasks->fetch_assoc()):
            ?>
              <tr>
                <td><?= htmlspecialchars($row['task_name']) ?></td>
                <td><span class="text-capitalize"><?= $row['priority'] ?></span></td>
                <td>
                  <span class="badge bg-<?= $row['status'] == 'selesai' ? 'success' : 'warning' ?>">
                    <?= ucwords($row['status']) ?>
                  </span>
                </td>
                <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div class="text-end mt-3">
        <a href="tasks.php" class="btn btn-outline-primary fw-bold">Lihat Semua Tugas</a>
      </div>
    </div>


  </div>
</div>

<script>
  const statusChart = document.getElementById('statusChart').getContext('2d');
  const priorityChart = document.getElementById('priorityChart').getContext('2d');

  // Donut Chart - Status Tugas
  new Chart(statusChart, {
    type: 'doughnut',
    data: {
      labels: ['Selesai', 'Belum Selesai'],
      datasets: [{
        data: [<?= $countSelesai ?>, <?= $countBelum ?>],
        backgroundColor: ['#28a745', '#ffc107'],
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Bar Chart - Prioritas
  new Chart(priorityChart, {
    type: 'bar',
    data: {
      labels: ['Rendah', 'Sedang', 'Tinggi'],
      datasets: [{
        label: 'Jumlah Tugas',
        data: [<?= $countRendah ?>, <?= $countSedang ?>, <?= $countTinggi ?>],
        backgroundColor: ['#17a2b8', '#ffc107', '#dc3545']
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
</script>


<?php include 'footer.php'; ?>