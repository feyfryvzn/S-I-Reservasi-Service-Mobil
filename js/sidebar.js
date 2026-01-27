$(document).ready(function() {
  // Toggle sidebar dan update margin konten
  $('#sidebarCollapse').on('click', function() {
    $('#sidebar').toggleClass('active');
    $('#content').toggleClass('sidebar-open');
  });

  // Tutup sidebar saat mouse keluar
  $('#sidebar').on('mouseleave', function() {
    $('#sidebar').removeClass('active');
    $('#content').removeClass('sidebar-open');
  });

  // Pastikan sidebar tersembunyi saat halaman dimuat
  $('#sidebar').removeClass('active');
  $('#content').removeClass('sidebar-open');
});