<script>
    // Toggle the mobile menu visibility
    function toggleMenu() {
        var navLinks = document.getElementById("top-nav");
        navLinks.classList.toggle("show"); // Toggle the 'show' class to display/hide the menu
    }
  </script>
  
  <script>
    // Show toast message for success
    @if(session('success'))
        document.addEventListener("DOMContentLoaded", function() {
            const successToast = document.getElementById('success-toast');
            successToast.classList.add('show');
  
            // Hide the success toast message after 5 seconds
            setTimeout(function() {
                successToast.classList.remove('show');
            }, 5000);
        });
    @endif
  
    // Show toast message for validation errors
    @if ($errors->any())
        document.addEventListener("DOMContentLoaded", function() {
            const errorToast = document.getElementById('error-toast');
            errorToast.classList.add('show');
  
            // Hide the error toast message after 5 seconds
            setTimeout(function() {
                errorToast.classList.remove('show');
            }, 5000);
        });
    @endif
  </script>
  <script>
    // Toggle dropdown visibility on click
  document.querySelector('.dropbtn').addEventListener('click', function(event) {
      const dropdown = this.closest('.dropdown');
      dropdown.classList.toggle('show'); // Toggle the 'show' class to display/hide the dropdown
  });
  </script>
  
  <script src="https://cdn.lordicon.com/lordicon.js"></script>
</body>
</html>