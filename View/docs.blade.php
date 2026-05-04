@extends("layouts.homel")

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
  background: #f5f6fa;
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
}

/* WRAPPER */
.docs-wrapper {
  max-width: 900px;
  margin: 30px auto;
  padding: 0 12px;
}

/* HEADER */
h1 {
  font-size: 24px;
  margin-bottom: 15px;
  color: #2c3e50;
}

/* BACK BUTTON */
.back-btn {
  display: inline-block;
  margin-bottom: 15px;
  background: #8e44ad;
  font-weight: 600;
  text-decoration: none;
  font-size: 14px;
}

.back-btn:hover {
  text-decoration: underline;
}

/* CARD */
.doc-card {
  background: #fff;
  padding: 16px;
  margin-bottom: 14px;
  border-radius: 12px;
  border: 1px solid #eee;
  box-shadow: 0 4px 14px rgba(0,0,0,0.05);
  transition: 0.2s ease;
}

.doc-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.doc-card h2 {
  font-size: 18px;
  margin-bottom: 8px;
  color: #8e44ad;
}

.doc-card div {
  color: #555;
  line-height: 1.5;
  font-size: 13px;
}

.doc-card a {
  display: inline-block;
  margin-top: 8px;
  color: #8e44ad;
  font-weight: 600;
  font-size: 13px;
  text-decoration: none;
}

.doc-card a:hover {
  text-decoration: underline;
}

/* 📱 MOBILE (Small devices) */
@media (max-width: 480px) {
  .docs-wrapper {
    margin: 20px auto;
    padding: 0 10px;
  }

  h1 {
    font-size: 20px;
  }

  .doc-card {
    padding: 14px;
    border-radius: 10px;
  }

  .doc-card h2 {
    font-size: 16px;
  }

  .doc-card div {
    font-size: 12px;
  }

  .doc-card a {
    font-size: 12px;
  }
}

/* 📱📱 TABLET */
@media (min-width: 481px) and (max-width: 768px) {
  .docs-wrapper {
    max-width: 95%;
  }

  .doc-card {
    padding: 18px;
  }
}

/* 💻 DESKTOP */
@media (min-width: 769px) {
  .docs-wrapper {
    max-width: 900px;
  }
}
</style>

<div class="docs-wrapper">

<h1>Documentation</h1>

<a href="{{ route('home') }}" class="back-btn">Back to Home</a>

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
       
        <div>${content}...</div>
        <a href="/docs/${doc.slug}">Read More →</a>
      </div>
    `;
  });

  $("#docs-container").html(html);
}

</script>

@endsection