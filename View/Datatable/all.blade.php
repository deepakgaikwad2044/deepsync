@extends("layouts.layouts")

@section('content')

<style>
:root {
  --brand: #8e44ad;
  --brand-dark: #6c3483;
  --bg: #f1f1fe;
  --text: #2d2d2d;
  --muted: #6c757d;
  --brand-gradient: linear-gradient(355deg, #8e44ad, #6f42c1);
}

body {
  background: var(--bg);
  color: var(--text);
  font-family: system-ui, sans-serif;
  margin: 0;
  padding: 0 1rem;
}

main {
  max-width: 900px;
  margin: 2rem auto;
}

h2 {
  margin-bottom: 0.5rem;
  color: var(--brand-dark);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-btn {
  background: var(--brand-gradient);
  color: white;
  border: none;
  padding: 0.4rem 1rem;
  border-radius: 0.3rem;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 3px 6px rgb(142 68 173 / 0.4);
  text-decoration: none;
}

.table-responsive {
  margin-top: 1rem;
  overflow-x: auto;
  border-radius: 0.4rem;
  box-shadow: 0 0 10px rgb(0 0 0 / 0.05);
  background: white;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: var(--brand-gradient);
  color: white;
}

th, td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #eee;
}

tr:hover {
  background: #f9f2ff;
}

.action-btn {
  margin: 0 0.25rem;
  font-size: 1.1rem;
  cursor: pointer;
}

#openCreateModalBtn:active {
  transform: scale(1.2);
}

/* MODAL */
.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.4);
  display:none;
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