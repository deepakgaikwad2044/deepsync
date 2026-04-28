@include("layouts.header")

<style>

:root{
 --brand:#8e44ad;
 --brand-dark:#6c3483;
 --bg:linear-gradient(135deg,#f3eaff,#f9f6ff);
 --brand-gradient:linear-gradient(135deg,#8e44ad,#6f42c1);
}

body{
 background:var(--bg);
 font-family:'Segoe UI',system-ui,sans-serif;
 margin:0;
}

main{
 max-width:750px;
 margin:4rem auto;
 text-align:center;
}

.user-card{
 background:white;
 padding:3rem 2rem;
 border-radius:1.8rem;
 box-shadow:0 25px 50px rgba(142,68,173,0.15);
}

.user-title{
 font-size:1.5rem;
 color:var(--brand-dark);
 margin-bottom:2rem;
 font-weight:600;
}

.big-number{
 font-size:4.5rem;
 font-weight:800;
 background:var(--brand-gradient);
 -webkit-background-clip:text;
 -webkit-text-fill-color:transparent;
}

.user-details{
 margin-top:1rem;
 font-size:1.1rem;
 color:#555;
}

.user-ratio{
 margin-top:0.5rem;
 font-size:1rem;
 color:#777;
}

.progress-wrapper{
 margin-top:2rem;
}

.progress-bar{
 width:100%;
 height:16px;
 background:#eee;
 border-radius:30px;
 overflow:hidden;
}

.progress-fill{
 height:100%;
 width:0%;
 background:var(--brand-gradient);
 border-radius:30px;
 transition:width .8s ease;
}

.update-time{
 margin-top:1.2rem;
 font-size:.8rem;
 color:#999;
}

</style>

<main>

<div class="user-card">

<div class="user-title">
👥 Live User Dashboard
</div>

<div id="totalUsers" class="big-number">0</div>

<div class="user-details">
Active Users: <strong id="activeUsers">0</strong>
</div>

<div class="user-details">
Inactive Users: <strong id="inactiveUsers">0</strong>
</div>

<div id="userRatio" class="user-ratio">
0 / 0 (0%)
</div>

<div class="progress-wrapper">
<div class="progress-bar">
<div id="userProgress" class="progress-fill"></div>
</div>
</div>

<div class="update-time">
Last updated: <span id="lastUpdated">--</span>
</div>

</div>

</main>

@include("layouts.footer")

<script>

const totalUsersEl = $("#totalUsers");
const activeUsersEl = $("#activeUsers");
const inactiveUsersEl = $("#inactiveUsers");
const userRatioEl = $("#userRatio");
const userProgressEl = $("#userProgress");
const lastUpdatedEl = $("#lastUpdated");

let TOTAL_USERS = 0;

/* =========================
   UI UPDATE FUNCTION
========================= */
function updateUI(activeUsers){

 if(!TOTAL_USERS) return;

 activeUsers = Number(activeUsers) || 0;

 const inactiveUsers = Math.max(TOTAL_USERS - activeUsers, 0);

 const percent = Math.min((activeUsers / TOTAL_USERS) * 100, 100);

 totalUsersEl.text(TOTAL_USERS);

 activeUsersEl.text(activeUsers);
 inactiveUsersEl.text(inactiveUsers);

 userRatioEl.text(
  TOTAL_USERS + " / " + activeUsers + " (" + percent.toFixed(0) + "%)"
 );

 userProgressEl.css("width", percent + "%");

 lastUpdatedEl.text(new Date().toLocaleTimeString());
}


/* =========================
   INITIAL DATA (AJAX)
========================= */
$.ajax({

 url:"{{ route('websocket.user.count') }}",
 method:"GET",

 success:function(response){

  console.log(response);

  if(!response.data) return;

  const data = response.data;

  TOTAL_USERS = Number(data.total_user) || 0;

  const inactiveUsers = Number(data.inactive_users) || 0;

  const activeUsers = TOTAL_USERS - inactiveUsers;

  updateUI(activeUsers);

 },

 error:function(err){
  console.log("AJAX Error:", err);
 }

});


/* =========================
   WEBSOCKET REALTIME
========================= */
const socket = new WebSocket("ws://localhost:8080");

socket.onopen = () => {

 console.log("Realtime Connected ✅");

 socket.send(JSON.stringify({
  action:"subscribe",
  channel:"UserChannel"
 }));

};

socket.onmessage = (event) => {

 try{

  const msg = JSON.parse(event.data);

  console.log("Realtime:", msg);

  if(msg.event === "userupdated"){

   const {inactive_users, total_user} = msg.data;

   TOTAL_USERS = Number(total_user) || 0;

   const activeUsers = TOTAL_USERS - Number(inactive_users);

   updateUI(activeUsers);

  }

 }catch(e){
  console.log("Parse Error:", e);
 }

};

socket.onerror = (err) => {
 console.log("WebSocket Error:", err);
};

socket.onclose = () => {
 console.log("Realtime Disconnected ❌");
};

</script>