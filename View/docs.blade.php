@extends("layouts.homel")

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
  background: #f5f6fa;
  font-family: 'Segoe UI', sans-serif;
}

.docs-wrapper {
  max-width: 900px;
  margin: 40px auto;
  padding: 0 15px;
}

h1 {
  font-size: 28px;
  margin-bottom: 20px;
  color: #2c3e50;
}

/* CARD */
.doc-card {
  background: #fff;
  padding: 20px 22px;
  margin-bottom: 18px;
  border-radius: 14px;
  border: 1px solid #eee;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
  transition: 0.2s ease;
}

.doc-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.doc-card h2 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #8e44ad;
}

.doc-card div {
  color: #555;
  line-height: 1.6;
  font-size: 14px;
}

.doc-card a {
  display: inline-block;
  margin-top: 10px;
  color: #8e44ad;
  font-weight: 600;
  text-decoration: none;
}

.doc-card a:hover {
  text-decoration: underline;
}

</style>

<div class="docs-wrapper">

<h1>Documentation</h1>

<a href="{{ route('home') }}" class="back-btn">← Back</a>

<div id="docs-container"></div>

</div>

<script src="/js/ds_pagination.js"></script>
<script>

function loadDocs(page = null) {

    if (!page) {
        page = DS_Pagination.getUrlPage();
    }

    $.ajax({
        url: "{{ route('deep.docs')}}",
        data: { page: page },

        success: function(res) {

            let docs = res.data.docs;

            renderDocs(docs.data);

            DS_Pagination.render("pagination", docs.meta, loadDocs);
        }
    });
}

$(document).ready(function () {
    loadDocs(); // ONLY ONCE
});

function renderDocs(docs) {

  let html = "";

  docs.forEach(doc => {

    let content = (doc.content || "").substring(0, 200);

    html += `
      <div class="doc-card">
        <h2>${doc.title || 'No Title'}i4</h2>
        <div>${content}...</div>
        <a href="/docs/${doc.slug}">Read More →</a>
      </div>
    `;
  });

  $("#docs-container").html(html);
}

$(document).ready(function() {
  loadDocs(1);
});
</script>

@endsection