<?php if(isset($_SESSION['success'])){?>
  
  <script>
$(document).ready(function(){
            
                toastr.success('<?php echo $_SESSION["success"]; ?>', 'Success');
          
        });
        </script> <?php
        unset($_SESSION["success"]);
    
    } ?>
    
    
    
    <?php if(isset($_SESSION['error'])){?>
  
  <script>
$(document).ready(function(){
            
                toastr.error('<?php echo $_SESSION["error"]; ?>', 'Error');
          
        });
        </script> <?php
        unset($_SESSION["error"]);
        
  
    } ?>