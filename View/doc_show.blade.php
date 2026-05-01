@extends("layouts.homel")

@section('content')

<style>
body {
  background: #f5f6fa;
  font-family: 'Segoe UI', sans-serif;
}

/* MAIN WRAPPER */
.doc-single {
  max-width: 850px;
  margin: 40px auto;
  padding: 25px;
}

/* CARD STYLE */
#doc-container {
  background: #fff;
  padding: 30px;
  border-radius: 16px;
  border: 1px solid #eee;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

/* TITLE */
#doc-container h1 {
  font-size: 30px;
  color: #8e44ad;
  margin-bottom: 10px;
}

/* CONTENT */
#doc-container div {
  margin-top: 20px;
  font-size: 16px;
  line-height: 1.8;
  color: #444;
}

/* CLEAN HR */
#doc-container hr {
  border: none;
  height: 1px;
  background: #eee;
  margin: 15px 0;
}

/* BACK BUTTON */
.doc-single a {
  display: inline-block;
  margin-top: 20px;
  padding: 8px 14px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  text-decoration: none;
  color: #333;
  transition: 0.2s;
}

.doc-single a:hover {
  background: #8e44ad;
  color: #fff;
  border-color: #8e44ad;
}

/* LOADING STYLE */
#doc-container p {
  text-align: center;
  color: #888;
  font-size: 14px;
}

/* MOBILE */
@media (max-width: 600px) {
  .doc-single {
    padding: 15px;
  }

  #doc-container {
    padding: 18px;
  }

  #doc-container h1 {
    font-size: 22px;
  }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="doc-single">

  <!-- JS yaha content dalega -->
  <div id="doc-container">
    <p>Loading... ⏳</p>
  </div>

  <br>
  <a href="{{ route('deep.docs')}}">← Back</a>

</div>

<script>
$(document).ready(function() {

  // 👉 URL se slug nikaalo
  let path = window.location.pathname;
  let slug = path.split("/").pop();

  loadDoc(slug);

});

// 🚀 Load single doc
function loadDoc(slug) {

  $.ajax({
    url: "/docs/" + slug,
    type: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest"
    },

    success: function(res) {
    
    console.log(res)
    
      if (!res.success) {
        $("#doc-container").html("<h2>❌ Not found</h2>");
        return;
      }

      let doc = res.data.doc;

      let html = `
        <h1>${doc.title || 'No Title'}</h1>
        <hr>
        <div>${doc.content || ''}</div>
      `;

      $("#doc-container").html(html);
    },

    error: function(xhr) {
      console.log(xhr.responseText);
      $("#doc-container").html("<h2>❌ Error loading doc</h2>");
    }

  });

}
</script>

@endsection