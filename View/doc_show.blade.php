@extends("layouts.homel")

@section('content')

<style>
body {
  background: #f5f6fa;
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
}

/* MAIN WRAPPER */
.doc-single {
  max-width: 850px;
  margin: 30px auto;
  padding: 15px;
}

/* CARD */
#doc-container {
  background: #fff;
  padding: 22px;
  border-radius: 14px;
  border: 1px solid #eee;
  box-shadow: 0 8px 25px rgba(0,0,0,0.05);
  overflow: hidden;
}

/* TITLE */
#doc-container h1 {
  font-size: 26px;
  color: #8e44ad;
  margin-bottom: 10px;
  line-height: 1.3;
}

/* HEADINGS INSIDE CONTENT */
#doc-container h2 {
  font-size: 20px;
  margin-top: 20px;
  color: #2c3e50;
}

#doc-container h3 {
  font-size: 16px;
  margin-top: 15px;
  color: #444;
}

/* TEXT CONTENT */
#doc-container div {
  margin-top: 15px;
  font-size: 15px;
  line-height: 1.7;
  color: #444;
}

/* CODE BLOCK */
#doc-container pre {
  background: #2d2d2d;
  color: #f1f1f1;
  padding: 12px;
  border-radius: 10px;
  overflow-x: auto;
  font-size: 13px;
}

/* INLINE CODE */
#doc-container code {
  background: #eee;
  padding: 2px 6px;
  border-radius: 6px;
  font-size: 13px;
}

/* LIST */
#doc-container ul {
  padding-left: 18px;
}

#doc-container li {
  margin-bottom: 6px;
}

/* HR */
#doc-container hr {
  border: none;
  height: 1px;
  background: #eee;
  margin: 15px 0;
}

/* BACK BUTTON */
.doc-single a {
  display: inline-block;
  margin-top: 18px;
  padding: 8px 14px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  text-decoration: none;
  color: #333;
  font-size: 14px;
  transition: 0.2s;
}

.doc-single a:hover {
  background: #8e44ad;
  color: #fff;
  border-color: #8e44ad;
}

/* LOADING */
#doc-container p {
  text-align: center;
  color: #888;
  font-size: 14px;
}

/* 📱 MOBILE */
@media (max-width: 480px) {

  .doc-single {
    margin: 20px auto;
    padding: 10px;
  }

  #doc-container {
    padding: 16px;
    border-radius: 12px;
  }

  #doc-container h1 {
    font-size: 20px;
  }

  #doc-container h2 {
    font-size: 17px;
  }

  #doc-container h3 {
    font-size: 14px;
  }

  #doc-container div {
    font-size: 13px;
  }

  #doc-container pre {
    font-size: 12px;
    padding: 10px;
  }

  .doc-single a {
    font-size: 13px;
    padding: 7px 12px;
  }
}

/* 📱 TABLET */
@media (min-width: 481px) and (max-width: 768px) {
  .doc-single {
    max-width: 95%;
  }
}


/* Default (desktop) */
.top-back {
  display: none;
}

.bottom-back {
  display: inline-block;
}

/* Mobile */
@media (max-width: 480px) {
  .top-back {
    display: inline-block;
    margin-bottom: 12px;
  }

  .bottom-back {
    display: none;
  }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="doc-single">

<a href="{{ route('deep.docs')}}" class="back-btn top-back">← Back</a>

<div id="doc-container">
  <p>Loading... ⏳</p>
</div>

<a href="{{ route('deep.docs')}}" class="back-btn bottom-back">← Back</a>

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