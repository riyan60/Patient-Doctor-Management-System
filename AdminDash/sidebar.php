<!-- Sidebar -->
   <div class="sidebar" id="sidebarAccordion">
     <h3 class="text-center text-white pb-3">Admin Panel</h3>
   <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Dashboard</a>
    <a data-bs-toggle="collapse" href="#doctorMenu" role="button"><i class="bi bi-person-badge"></i> Doctors</a>
    <div class="collapse ms-3" id="doctorMenu" data-bs-parent="#sidebarAccordion">
      <a href="doctors_list.php">View All</a>
      <a href="add_doctor.php">Add New</a>
      <a href="pending_doctors.php">Pending</a>
    </div>

    <a data-bs-toggle="collapse" href="#patientMenu" role="button"><i class="bi bi-people"></i> Patients</a>
    <div class="collapse ms-3" id="patientMenu" data-bs-parent="#sidebarAccordion">
      <a href="patients_list.php">View All</a>
      <a href="add_patient.php">Add New</a>
    </div>

    <a href="appointments.php"><i class="bi bi-calendar-event"></i> Appointments</a>

    <a data-bs-toggle="collapse" href="#labMenu" role="button"><i class="bi bi-building"></i> Labs</a>
    <div class="collapse ms-3" id="labMenu" data-bs-parent="#sidebarAccordion">
      <a href="labs_list.php">View All</a>
      <a href="add_lab.php">Add New</a>
    </div>

    <a data-bs-toggle="collapse" href="#packageMenu" role="button"><i class="bi bi-box-seam"></i> Packages</a>
    <div class="collapse ms-3" id="packageMenu" data-bs-parent="#sidebarAccordion">
      <a href="packages_list.php">View All</a>
      <a href="add_package.php">Add New</a>
    </div>

    <a data-bs-toggle="collapse" href="#testMenu" role="button"><i class="bi bi-clipboard-data"></i> Tests</a>
    <div class="collapse ms-3" id="testMenu" data-bs-parent="#sidebarAccordion">
      <a href="tests_list.php">View All</a>
      <a href="add_test.php">Add New</a>
    </div>

    <a data-bs-toggle="collapse" href="#cityMenu" role="button"><i class="bi bi-geo-alt"></i> Cities</a>
    <div class="collapse ms-3" id="cityMenu" data-bs-parent="#sidebarAccordion">
      <a href="cities_list.php">View All</a>
      <a href="add_city.php">Add New</a>
    </div>

    <a href="blogs.php"><i class="bi bi-journal-text"></i> Blogs</a>

    <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
   </div>
