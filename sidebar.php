<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<!-- Sidebar -->
<div class="col-md-2 bg-white border-end py-4 shadow-sm" style="min-height: 100vh;">
  <ul class="nav flex-column">
    <li class="nav-item mb-2">
      <a class="nav-link fw-semibold <?= $current_page == 'dashboard.php' ? 'bg-black text-white rounded fw-bold' : 'text-dark' ?>" href="dashboard.php">Daftar Tugas</a>
    </li>
    <li class="nav-item mb-2">
      <a class="nav-link fw-semibold <?= $current_page == 'tasks.php' ? 'bg-black text-white rounded fw-bold' : 'text-dark' ?>" href="tasks.php">Kelola Tugas</a>
    </li>
  </ul>
</div>
<!-- End Sidebar -->