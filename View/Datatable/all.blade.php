@extends("layouts.layouts")

@section('content')

<style>

:root {
  --brand: #8e44ad;
  --brand-dark: #6c3483;
  --bg: linear-gradient(135deg,#f3eaff,#f9f6ff);
  --text: #2d2d2d;
  --brand-gradient: linear-gradient(135deg,#8e44ad,#6f42c1);
}

body {
  background: var(--bg);
  color: var(--text);
  font-family: system-ui, sans-serif;
}

/* MAIN */
main {
  max-width: 950px;
  margin: 2rem auto;
}

/* HEADER */
h2 {
  margin-bottom: 0;
  color: var(--brand-dark);
  font-weight: 600;
}

/* BACK BUTTON */
.back-btn {
  background: var(--brand-gradient);
  color: white;
  padding: 6px 14px;
  border-radius: 10px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: 0.25s ease;
}

.back-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(142,68,173,.4);
}

/* ADD BUTTON */
#openCreateModalBtn {
  border-radius: 12px;
  padding: 8px 14px;
  transition: 0.25s ease;
}

#openCreateModalBtn:hover {
  transform: scale(1.1);
}

/* TABLE WRAPPER */
.table-responsive {
  margin-top: 1.5rem;
  border-radius: 16px;
  overflow: hidden;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(10px);
  box-shadow: 0 20px 40px rgba(0,0,0,.08);
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
}

/* HEADER */
thead {
  background: var(--brand-gradient);
  color: white;
}

th {
  font-weight: 500;
}

/* CELLS */
th, td {
  padding: 14px 16px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* ROW HOVER */
tbody tr {
  transition: 0.2s ease;
}

tbody tr:hover {
  background: rgba(142,68,173,0.08);
}

/* ACTION ICONS */
.action-btn {
  margin: 0 6px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.2s ease;
}

.action-btn:hover {
  transform: scale(1.2);
}

/* BADGES */
.badge {
  padding: 6px 10px;
  border-radius: 10px;
  font-size: 12px;
}

/* MODAL */
.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.5);
  backdrop-filter: blur(4px);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:9999;
}

.modal-box{
  background:rgba(255,255,255,0.95);
  backdrop-filter: blur(10px);
  padding:1.5rem;
  border-radius:16px;
  width:100%;
  max-width:420px;
  box-shadow:0 20px 50px rgba(0,0,0,.2);
  animation: scaleIn .25s ease;
}

/* MODAL ANIMATION */
@keyframes scaleIn{
  from{
    transform: scale(0.9);
    opacity: 0;
  }
  to{
    transform: scale(1);
    opacity: 1;
  }
}

/* INPUT */
.form-control{
  border-radius:12px;
  padding:10px;
  border:1px solid rgba(0,0,0,0.1);
}

.form-control:focus{
  border-color: var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

/* BUTTONS */
.btn-primary{
  background: var(--brand-gradient);
  border:none;
  border-radius:12px;
}

.btn-secondary{
  border-radius:12px;
}

</style>

<main>

<a href="{{ route('user.dashboard') }}">
  <i class="fa-solid fa-arrow-left text-white back-btn"></i>
</a>

<div class="d-flex justify-content-around align-items-center">
  <h2 class="mt-3">Datatable</h2>

  <span class="btn btn-dark" id="openCreateModalBtn">+</span>
</div>

<div class="table-responsive mt-3">

<table id="datatable" class="table table-hover align-middle mb-0 evm-table">

<thead>
<tr>
<th>#</th>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th class="text-center">Action</th>
</tr>
</thead>

<tbody></tbody>

</table>

</div>

<!-- CREATE MODAL -->
<div id="createModal" class="modal-overlay">

<div class="modal-box">

<h4>Add User</h4>

<form id="user_createForm">

<div class="mb-2">
<label>Name</label>
<input type="text" name="name" class="form-control">
</div>

<div class="mb-2">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="mb-2">
<label>Password</label>
<input type="password" name="password" class="form-control">
</div>

<div class="text-end mt-3">
<button type="button" class="btn btn-secondary" onclick="closeModalFade()">Cancel</button>
<button type="submit" class="btn btn-primary">Create</button>
</div>

</form>

</div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal-overlay">

<div class="modal-box">

<h4>Edit User</h4>

<form id="user_editForm">

<input type="hidden" id="edit_id" name="id">

<div class="mb-2">
<label>Name</label>
<input type="text" id="edit_name" name="name" class="form-control">
</div>

<div class="mb-2">
<label>Email</label>
<input type="email" id="edit_email" name="email" class="form-control">
</div>

<div class="mb-2">
<label>Status</label>
<select id="edit_status" name="account_status" class="form-control">
<option value="1">Active</option>
<option value="0">Inactive</option>
</select>
</div>

<div class="text-end mt-3">
<button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
<button type="submit" class="btn btn-primary">Update</button>
</div>

</form>

</div>
</div>

</main>


@endsection

@section("scripts")

<script src="/public/js/ds-datatable.js"></script>

<script>
DS_Table.init("datatable", {
  ajax: "{{ route('table.data') }}",
  dataSrc: "data",
  columns: {
    Id: "id",
    Name: "name",
    Email: "email",
    Status: {
      label: "account_status",
      render: row => {
        return row.account_status == 1
          ? `<span class="badge bg-success text-white">active</span>`
          : `<span class="badge bg-danger text-white">inactive</span>`;
      }
    },
    action: {
      label: "Action",
      render: row => `
        <span class="text-primary action-btn"
          onclick='openEditModal(${JSON.stringify(row)})'>
          <i class="fa-solid fa-pen-to-square"></i>
        </span>

        <span class="text-danger action-btn"
          onclick="DS_Table.delete('datatable','/datatable/delete', ${row.id})">
          <i class="fa-solid fa-trash"></i>
        </span>
      `
    }
  },
  disableAutoSearchInput: true
});

/* MODALS */
$("#openCreateModalBtn").click(() => $("#createModal").fadeIn());

function closeModalFade(){
  $("#createModal").fadeOut();
}

function openEditModal(row){
  $("#edit_id").val(row.id);
  $("#edit_name").val(row.name);
  $("#edit_email").val(row.email);
  $("#edit_status").val(row.account_status);

  $("#editModal").fadeIn();
}

function closeModal(){
  $("#editModal").fadeOut();
}

/* CREATE */
$(document).on("submit","#user_createForm",function(e){
  e.preventDefault();

  DS_Form.submit(
    "user_createForm",
    "{{ route('datatable.create') }}",
    "datatable",
    "createModal"
  );
});

/* UPDATE */
$(document).on("submit","#user_editForm",function(e){
  e.preventDefault();

  DS_Form.submit(
    "user_editForm",
    "{{ route('datatable.update') }}",
    "datatable",
    "editModal"
  );
});
</script>
@endsection