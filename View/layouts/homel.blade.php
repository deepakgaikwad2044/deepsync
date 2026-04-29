@php 
define("BRAND_COLOR", "#8e44ad");
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Welcome to Deep Sync Framework</title>
 <style>
:root {
  --brand: #8e44ad;
  --brand-dark: #6c3483;
  --bg: linear-gradient(135deg,#f3eaff,#f9f6ff);
  --gradient: linear-gradient(135deg,#8e44ad,#6f42c1);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', sans-serif;
  background: var(--bg);
  color: #333;
}

/* CONTAINER */
.container {
  max-width: 1100px;
  margin: auto;
  padding: 1.5rem;
}

/* HEADER */
.header {
  background: var(--gradient);
  color: #fff;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* LOGO */
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-logo {
  height: 40px;
}

.brand-name {
  font-weight: 700;
  font-size: 1.2rem;
}

/* TITLE */
.title {
  font-size: 3rem;
  margin-top: 2rem;
  text-align: center;
  background: var(--gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* TEXT */
.lead {
  text-align: center;
  margin: 1rem 0 2rem;
  color: #666;
  font-size: 1.2rem;
}

/* BUTTON */
.btn-primary {
  display: block;
  margin: auto;
  background: var(--gradient);
  color: white;
  padding: 12px 28px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  width: fit-content;
  transition: 0.3s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(142,68,173,0.3);
}

.btn-outline {
  border: 2px solid white;
  padding: 8px 18px;
  border-radius: 6px;
  color: white;
  text-decoration: none;
}

/* FEATURES */
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.feature {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  transition: 0.3s;
}

.feature:hover {
  transform: translateY(-5px);
}

.icon {
  font-size: 2rem;
  margin-bottom: 10px;
}

.feature h3 {
  margin-bottom: 10px;
  color: var(--brand-dark);
}

.feature p {
  color: #666;
}


/* FOOTER */
.footer {
  background: #111;
  color: #bbb;
  margin-top: 3rem;
  padding: 1.5rem 0;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.footer strong {
  color: #8e44ad;
}

.footer-links {
  display: flex;
  gap: 1rem;
}

.footer-links a {
  color: #bbb;
  text-decoration: none;
  transition: 0.3s;
}

.footer-links a:hover {
  color: #fff;
}

/* RESPONSIVE */
@media(max-width:768px){
  .title{
    font-size:2rem;
  }
}

</style>

<style>

/* ================= ABOUT PAGE ================= */

.about {
  max-width: 1100px;
  margin: auto;
  padding: 3rem 1.5rem;
  text-align: center;
}

.about-title {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 1rem;
  background: linear-gradient(135deg,#8e44ad,#6f42c1);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.about-lead {
  font-size: 1.2rem;
  color: #666;
  max-width: 700px;
  margin: 0 auto 3rem;
  line-height: 1.6;
}

/* GRID */
.about-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
  gap: 2rem;
}

/* CARD */
.about-card {
  background: #fff;
  padding: 2rem;
  border-radius: 18px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

/* hover effect */
.about-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 18px 40px rgba(142,68,173,0.2);
}

/* top gradient line */
.about-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: linear-gradient(135deg,#8e44ad,#6f42c1);
}

/* headings */
.about-card h3 {
  margin-bottom: 10px;
  color: #6c3483;
  font-size: 1.3rem;
}

/* text */
.about-card p {
  color: #666;
  line-height: 1.5;
}

/* footer text */
.about-footer {
  margin-top: 3rem;
  font-size: 1rem;
  color: #888;
}

/* ================= RESPONSIVE ================= */

@media (max-width: 768px) {
  .about-title {
    font-size: 2.2rem;
  }

  .about-lead {
    font-size: 1rem;
  }
}

</style>

<style>

/* ================= DOCS ================= */

.docs {
  max-width: 1100px;
  margin: auto;
  padding: 3rem 1.5rem;
  text-align: center;
}

.docs-title {
  font-size: 2.8rem;
  margin-bottom: 1rem;
  background: linear-gradient(135deg,#8e44ad,#6f42c1);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.docs-lead {
  color: #666;
  margin-bottom: 3rem;
  font-size: 1.1rem;
}

/* GRID */
.docs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
  gap: 2rem;
}

/* CARD */
.doc-card {
  background: white;
  padding: 1.8rem;
  border-radius: 16px;
  text-align: left;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  transition: 0.3s;
}

.doc-card:hover {
  transform: translateY(-5px);
}

/* TITLE */
.doc-card h3 {
  margin-bottom: 10px;
  color: #6c3483;
}

/* TEXT */
.doc-card p {
  color: #666;
  margin-bottom: 10px;
}

/* CODE BLOCK */
.doc-card code {
  display: block;
  background: #f4f4f4;
  padding: 10px;
  border-radius: 6px;
  font-size: 0.9rem;
  color: #333;
  overflow-x: auto;
}

/* RESPONSIVE */
@media (max-width:768px){
  .docs-title{
    font-size:2rem;
  }
}

</style>

  <!-- FontAwesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

@yield('content')

</body>
</html>