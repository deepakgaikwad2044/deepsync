<!DOCTYPE html>
<html lang="en">
<head>
  <title>Deep-Sync-PHP-Framework</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <meta name="csrf-token" content="<?= csrf_token() ?>">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  
<link rel="icon" href="/public/favicon/favicon.ico" type="image/x-icon">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

<!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<!------sweet alert--->
  
    <!-- Toastr CSS CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    
    <!-- Toastr JS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
     <!-- Select2 CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet"
    />

    <!-- Select2 Bootstrap 4 Theme -->
    <link
      href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
      rel="stylesheet"
    />
  <style>
:root {
  --darkmode: #dbcbfa;
  --lightmode1: #824be9; /* renamed to avoid duplicate */
  --lightmode2: #eeeaea;
  --brand-color: #b2af00; /* select */
   --brand: #8e44ad!important;
  --second-color1: #8362c4;
  --second-color2: #6a29e7;
  --brand-gradient: linear-gradient(135deg, #6f42c1, #8e44ad);
  
}

/*
html {
  user-select: none;
} */

  .login_container , .register_conatainer{
    background:#fff;
    display:flex;
    justify-content: center;
    align-items: center;
    height:100vh;
  }
  


@media (max-width: 576px) {
  h2 {
    font-size: 1.5rem;
  }
}



.btn-primary, .btn-dark {
  padding: 0.5rem 2.5rem;
  outline: none;
  border: none;
  background: var(--brand);
}

.btn-primary {
  background: var(--brand-gradient)!important;
}


.fa-arrow-left {
  color: #333;
}

.btn-dark {
  background: #131010;
}

.change_password_text {
  color: var(--second-color2);
  margin-bottom: 1rem;
}

.res_img {
  width: 200px;
  height: 200px;
  background: red !important;
}

.card .btn {
  background: #8362c4;
  text-transform: uppercase;
  font-weight: bold;
  border: none;
  transition: background 0.3s ease, color 0.3s ease;
}

.card .btn:hover {
  background: #fff;
  color: #909090;
}

.body_darkmode {
  background: #1b1917 !important;
  color: #f3f3f3 !important;
}

.dashboard_heading {
  font-size: 1rem;
  color: #333 !important;
  text-align: left;
  margin-left: 1rem;
}

i {
  font-size: 2.1rem;
}

label {
  font-weight: 700;
}

.brand_logo {
  background-size: cover;
  box-shadow: 0 0.1rem 0.2rem rgba(0, 0, 0, 0.2);
}

.fas {
  font-size: 2rem;
}

body {
  background: #faf8f6;
  color: #545454 !important;
}


.success-alert {
  background: #d4edda;
  color: #155724;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  border: 1px solid #c3e6cb;
}

.error-alert {
  background: #f8d7da;
  color: #721c24;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  border: 1px solid #f5c6cb;
}

/* Datatable pagination UI */


#datatable {
  margin-bottom: 2rem!important;
}
#datatable thead th {
    background: var(--brand-gradient)
    color: #fff;
    border: none;
    font-weight: 500;
}

#datatable tbody tr {
    transition: all .15s ease-in-out;
}

#datatable tbody tr:hover {
    background-color: #f6f2fb;
    transform: scale(1.003);
}

#datatable .action-btn {
   font-size: .1rem!important;
    width: 34px;
    height: 34px;
    padding: 0;
    line-height: 34px;
}

#datatable .badge {
    font-size: 0.8rem;
    padding: 6px 10px;
}



@media (max-width: 768px) {
  
  
  .datatable-searchbox {
    max-width:100%!important;
  }
  
  .table {
    background:none!important;
  }
  
  #datatable thead {
    display: none;
  }

  #datatable tr {
    display: block;
    margin-bottom: 16px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgb(142 68 173 / 0.15);
    padding: 14px 16px;
    transition: box-shadow 0.3s ease;
    animation: fadeInUp 0.4s ease forwards;
  }

  #datatable tr:hover {
    box-shadow: 0 8px 20px rgb(142 68 173 / 0.3);
  }

  #datatable td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border: none;
    text-align: right;
  }

#datatable td::before {
  content: attr(data-label);
  font-weight: 700;
  font-size: 0.95rem;
  color: #8e44ad;
  width: 100px;
  text-align: left;
}


 #datatable .action-btn {
    font-size: 20px;
    margin-left: 10px;
    padding: 6px 10px;
    border-radius: 6px;
    transition: background-color 0.25s ease;
  }

 #datatable .action-btn.text-primary:hover {
    background-color: #d6bcfa;
    color: #6c3483;
  }

 #datatable .action-btn.text-danger:hover {
    background-color: #f8d7da;
    color: #842029;
  }
}

#datatable thead th {
  background: linear-gradient(135deg, #6f42c1, #8e44ad);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}


.pagination{
  display:flex;
  gap:6px;
  list-style:none;
  padding:0;
}
.pagination li a{
  padding:6px 10px;
  text-decoration:none;
  border:1px solid #333;
  color:#333;
}
.pagination li.active a{
  background:var(--brand-gradient);
  color:#fff;
  border: none;
}


.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.4);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
}

.modal-box{
  background:#fff;
  padding:1.2rem;
  border-radius:.5rem;
  width:100%;
  max-width:400px;
  box-shadow:0 10px 30px rgba(0,0,0,.2);
}

.modal-box h4{
  margin-bottom:1rem;
  color:var(--brand-dark);
}


.ds-flash {
  animation: dsFlash 1.2s ease-in-out;
}

@keyframes dsFlash {
  0% {
    background-color: #f3e8ff;
  }
  40% {
    background-color: #e0c3ff;
  }
  100% {
    background-color: transparent;
  }
} 

.ds-loader {
  display: none;
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.7);
  z-index: 10;
  align-items: center;
  justify-content: center;
}

.ds-loader .spinner-border {
  width: 3rem;
  height: 3rem;
}

.ds-table-wrapper {
  position: relative;
}

</style>

<script src="/js/ds-datatable.js"></script>
</head>
<body>
  
  