  </div>

<script src="/js/ds-echo.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  
      <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <script>
  
$(document).ready(function(){

   $(document).on("click", ".bars_icon" , function(){
     
        const sidenav = $(".sidenav");

   const bars_icon = $(this);
   

  sidenav.toggleClass("expand_nav");
 
  if (sidenav.hasClass("expand_nav")) {
            bars_icon.removeClass("fas fa-bars").addClass("fas fa-close");
         
        } else {
            bars_icon.removeClass("fa-close").addClass("fa-bars");
            
        }
   });
   
});
  </script>
</body>
</html>