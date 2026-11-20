@verbatim
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>50th DITF 2026 — Official Opening Ceremony</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700">
<style>
  body{font-family:"Montserrat",Verdana,sans-serif;background:#eef2f5;}
  .hero{background:linear-gradient(135deg,#0b6e4f 0%,#10985f 50%,#f4b400 140%);}
  .cd-box{min-width:70px;}
  .cd-num{font-size:28px;font-weight:700;line-height:1;}
  .cd-lbl{font-size:11px;text-transform:uppercase;letter-spacing:1px;opacity:.85;}
  td .countdown{font-variant-numeric:tabular-nums;font-weight:600;white-space:nowrap;}
  .resp{font-size:13px;color:#555;}
  table.w3-table-all td{vertical-align:top;}
  .now-row{box-shadow:inset 4px 0 0 #0b6e4f;}
  @media(max-width:600px){.cd-num{font-size:20px}.hide-sm{display:none}}
</style>
</head>
<body>

<!-- HERO -->
<header class="hero w3-text-white w3-center w3-padding-32">
  <div class="w3-content" style="max-width:900px">
    <p class="w3-large w3-opacity">🇹🇿 Dar es Salaam International Trade Fair</p>
    <h1 class="w3-jumbo w3-hide-small" style="font-weight:700;margin:6px 0">50<sup>th</sup> DITF · SabaSaba 2026</h1>
    <h1 class="w3-hide-medium w3-hide-large" style="font-weight:700">50th DITF 2026</h1>
    <h3 id="subtitle" style="font-weight:400">Official Opening Ceremony</h3>
    <p id="eventmeta" class="w3-large"></p>

    <div id="masterCountdown" class="w3-margin-top">
      <div class="w3-bar w3-center" style="display:inline-block">
        <span class="cd-box w3-bar-item"><span class="cd-num" id="d">0</span><br><span class="cd-lbl" id="lblDays">Days</span></span>
        <span class="cd-box w3-bar-item"><span class="cd-num" id="h">0</span><br><span class="cd-lbl" id="lblHrs">Hours</span></span>
        <span class="cd-box w3-bar-item"><span class="cd-num" id="m">0</span><br><span class="cd-lbl" id="lblMin">Min</span></span>
        <span class="cd-box w3-bar-item"><span class="cd-num" id="s">0</span><br><span class="cd-lbl" id="lblSec">Sec</span></span>
      </div>
      <p id="masterMsg" class="w3-margin-top"></p>
    </div>

    <div class="w3-margin-top">
      <button onclick="setLang('en')" id="btnEn" class="w3-button w3-round-large w3-white">English</button>
      <button onclick="setLang('sw')" id="btnSw" class="w3-button w3-round-large w3-green">Kiswahili</button>
    </div>
  </div>
</header>

<!-- TIMETABLE -->
<div class="w3-content w3-margin-top w3-margin-bottom" style="max-width:960px">
  <div class="w3-card w3-white w3-round-large" style="overflow:hidden">
    <table class="w3-table-all">
      <thead>
        <tr class="w3-green">
          <th id="thTime" style="width:120px">Time</th>
          <th id="thProg">Program</th>
          <th id="thResp" class="hide-sm">Responsible</th>
          <th id="thCd" style="width:130px">Countdown</th>
        </tr>
      </thead>
      <tbody id="rows"></tbody>
    </table>
  </div>
  <p class="w3-small w3-text-grey w3-center w3-padding">Live countdowns based on your device's local time · Event time zone: EAT (UTC+3)</p>
</div>

<footer class="hero w3-text-white w3-center w3-padding-16">
  <p>“Dar es Salaam International Trade Fair · SabaSaba · Pride of Tanzania”</p>
</footer>

<script>
// Event date: Friday 3 July 2026, times in EAT (UTC+3)
const EVENT_DATE = "2026-07-03";

const events = [
  {start:"09:00", sw:{t:"09.00 - 10.00 Asubuhi", a:"Kuwasili kwa Waheshimiwa Mabalozi, Viongozi wa Serikali na Sekta Binafsi na Wageni Waalikwa – Ukumbi wa Ali Hassan Mwinyi", r:"Timu ya Itifaki"}, en:{t:"09:00 - 10:00 AM", a:"Arrival of Ambassadors, Government Officials, Private Sector Representatives and Invited Guests at Ali Hassan Mwinyi Hall", r:"Protocol Team"}},
  {start:"10:00", sw:{t:"10.00 - 10.15 Asubuhi", a:"Kuwasili kwa Mgeni Rasmi – Uwanja wa Maonesho wa Mwl. J.K. Nyerere", r:"Mawaziri, Makatibu Wakuu, Mwenyekiti wa Bodi na Mkurugenzi Mkuu TanTrade"}, en:{t:"10:00 - 10:15 AM", a:"Arrival of Guest of Honor at the Mwl. J.K. Nyerere Trade Fair Ground", r:"Ministers, Permanent Secretaries, Board Chairperson & TanTrade DG"}},
  {start:"10:15", sw:{t:"10.15 - 10.55 Asubuhi", a:"Kutembelea Banda la Kimataifa (Karume)", r:"Mawaziri, Makatibu Wakuu, Mwenyekiti wa Bodi na Mkurugenzi Mkuu TanTrade"}, en:{t:"10:15 - 10:55 AM", a:"Visit of the International Pavilion (Karume Hall)", r:"Ministers, Permanent Secretaries, Board Chairperson & TanTrade DG"}},
  {start:"10:55", sw:{t:"10.55 - 11.00 Asubuhi", a:"Kuwasili kwa Mgeni Rasmi – Ukumbi wa Ali Hassan Mwinyi", r:"Mawaziri, Makatibu Wakuu, Mwenyekiti wa Bodi na Mkurugenzi Mkuu TanTrade"}, en:{t:"10:55 - 11:00 AM", a:"Arrival of the Guest of Honor at Ali Hassan Mwinyi Hall", r:"Ministers, Permanent Secretaries, Board Chairperson & TanTrade DG"}},
  {start:"11:00", sw:{t:"11.00 - 11.05 Asubuhi", a:"Nyimbo za Taifa na Wimbo wa Jumuiya ya Afrika Mashariki", r:"Brass Band"}, en:{t:"11:00 - 11:05 AM", a:"National Anthems and East Africa Song", r:"Brass Band"}},
  {start:"11:05", sw:{t:"11.05 - 11.10 Asubuhi", a:"Wimbo Maalum wa Maonesho ya 50 ya DITF 2026", r:"Banana Zoro na Khadija Kopa"}, en:{t:"11:05 - 11:10 AM", a:"Special Song for the 50th DITF 2026", r:"Banana Zoro & Khadija Kopa"}},
  {start:"11:10", sw:{t:"11.10 - 11.15 Asubuhi", a:"Salamu kutoka kwa Mkuu wa Mkoa wa Dar es Salaam", r:"Mhe. Albert Chalamila"}, en:{t:"11:10 - 11:15 AM", a:"Remarks by the Dar es Salaam Regional Commissioner", r:"Hon. Albert Chalamila"}},
  {start:"11:15", sw:{t:"11.15 - 11.20 Asubuhi", a:"Salamu za Mdhamini Mkuu wa Maonesho ya 50 ya Sabasaba 2026", r:"Cathy Wang – EACLC"}, en:{t:"11:15 - 11:20 AM", a:"Remarks by the Main 50th DITF 2026 Sponsor", r:"Cathy Wang – EACLC"}},
  {start:"11:25", sw:{t:"11.25 - 11.30 Asubuhi", a:"Burudani kutoka kwa Wanafunzi", r:"“Watoto wetu Urithi Wetu”"}, en:{t:"11:25 - 11:30 AM", a:"Song Performance", r:"“Watoto wetu Urithi Wetu” students"}},
  {start:"11:30", sw:{t:"11.30 - 11.40 Asubuhi", a:"Taarifa fupi ya Maonesho ya 50 ya DITF, 2026", r:"Dkt. Latifa M. Khamis – Mkurugenzi Mkuu, TanTrade"}, en:{t:"11:30 - 11:40 AM", a:"Brief Overview of 50th DITF, 2026", r:"Dr. Latifa M. Khamis – Director General, TanTrade"}},
  {start:"11:40", sw:{t:"11.40 - 11.45 Asubuhi", a:"Salamu kutoka Serikali ya Mapinduzi ya Zanzibar", r:"Katibu Mkuu, Wizara ya Biashara na Maendeleo ya Viwanda"}, en:{t:"11:40 - 11:45 AM", a:"Remarks from Revolutionary Government of Zanzibar", r:"Permanent Secretary – Ministry of Trade and Industrial Development"}},
  {start:"11:45", sw:{t:"11.45 - 11.55 Asubuhi", a:"Kikundi cha Ngoma", r:"Wasanii"}, en:{t:"11:45 - 11:55 AM", a:"Traditional Dance Performance", r:"Performing Artists"}},
  {start:"11:55", sw:{t:"11.55 - 12.00 Mchana", a:"Salamu kutoka Wizara ya Viwanda na Biashara na Kumkaribisha Mhe. Rais wa Tanzania", r:"Mhe. Judith Kapinga (Mb) – Waziri wa Viwanda na Biashara"}, en:{t:"11:55 - 12:00 PM", a:"Remarks from the Minister of Industry and Trade and Introduction of H.E. the President", r:"Hon. Judith Kapinga (MP) – Minister of Industry and Trade"}},
  {start:"12:00", sw:{t:"12.00 - 12.10 Mchana", a:"Rais wa Jamhuri ya Muungano wa Tanzania Kumkaribisha Mgeni Rasmi", r:"Mhe. Dkt. Samia Suluhu Hassan"}, en:{t:"12:00 - 12:10 PM", a:"H.E. the President to Introduce and Welcome the Guest of Honor", r:"H.E. Dr. Samia Suluhu Hassan"}},
  {start:"12:10", sw:{t:"12.10 - 12.20 Mchana", a:"Hotuba ya Mgeni Rasmi", r:"Mheshimiwa Mgeni Rasmi"}, en:{t:"12:10 - 12:20 PM", a:"Address by the Guest of Honor", r:"Guest of Honor"}},
  {start:"12:20", sw:{t:"12.20 - 12.30 Mchana", a:"Tuzo za Washindi wa Miaka 50 na Kutambua Michango ya Wadau", r:"Mheshimiwa Mgeni Rasmi"}, en:{t:"12:20 - 12:30 PM", a:"Presentation of 50th Anniversary Awards & Recognition of Stakeholders", r:"Guest of Honor"}},
  {start:"12:30", sw:{t:"12.30 - 12.50 Mchana", a:"Zawadi kwa Mgeni Rasmi", r:"Mhe. Judith Kapinga (Mb); Prof. Ulingeta O.L Mbamba; Dkt. Latifa M. Khamis"}, en:{t:"12:30 - 12:50 PM", a:"Presentation of Memento to the Guest of Honour", r:"Hon. Judith Kapinga (MP); Prof. Ulingeta O.L Mbamba; Dr. Latifa M. Khamis"}},
  {start:"12:50", sw:{t:"12.50 - 13.10 Mchana", a:"Picha", r:"MC"}, en:{t:"12:50 - 13:10 PM", a:"Group Photo", r:"MC"}},
  {start:"13:10", sw:{t:"13.10 - 13.20 Mchana", a:"Mgeni Rasmi Kuondoka Ukumbini", r:"Timu ya Itifaki"}, en:{t:"13:10 - 13:20 PM", a:"Departure of Guest of Honor", r:"Protocol Team"}},
  {start:"13:20", sw:{t:"13.20 Mchana", a:"Wageni Waalikwa Kuondoka Ukumbini", r:"Wote"}, en:{t:"13:20 PM", a:"The End", r:"ALL"}},
];

const T = {
  en:{title:"Official Opening Ceremony", meta:"Friday, 3 July 2026 · Ali Hassan Mwinyi Hall – Mbarouk Mwandoro Square, Dar es Salaam",
      days:"Days",hrs:"Hours",min:"Min",sec:"Sec", time:"Time",prog:"Program",resp:"Responsible",cd:"Countdown",
      before:"until the ceremony begins", live:"🔴 Ceremony in progress", done:"Ceremony concluded — Karibu tena!",
      upcoming:"Upcoming",now:"NOW",ended:"Done"},
  sw:{title:"Sherehe ya Ufunguzi Rasmi", meta:"Ijumaa, Tarehe 3 Julai 2026 · Ukumbi wa Ali Hassan Mwinyi – Mbarouk Mwandoro Square, Dar es Salaam",
      days:"Siku",hrs:"Saa",min:"Dak",sec:"Sek", time:"Muda",prog:"Shughuli",resp:"Mhusika",cd:"Hesabu",
      before:"hadi sherehe kuanza", live:"🔴 Sherehe inaendelea", done:"Sherehe imekamilika — Karibu tena!",
      upcoming:"Inakuja",now:"SASA",ended:"Imeisha"},
};

let lang = "en";
const rowsEl = document.getElementById("rows");

function startDate(){ return new Date(EVENT_DATE + "T09:00:00+03:00"); }
function evStart(i){ return new Date(EVENT_DATE + "T" + events[i].start + ":00+03:00"); }
function pad(n){ return String(n).padStart(2,"0"); }

function buildRows(){
  rowsEl.innerHTML = "";
  events.forEach((ev,i)=>{
    const d = ev[lang];
    const tr = document.createElement("tr");
    tr.id = "row"+i;
    tr.innerHTML =
      `<td><b>${d.t}</b></td>`+
      `<td>${d.a}</td>`+
      `<td class="resp hide-sm">${d.r}</td>`+
      `<td class="countdown"><span id="cd${i}"></span></td>`;
    rowsEl.appendChild(tr);
  });
}

function setLang(l){
  lang = l;
  const t = T[l];
  document.getElementById("subtitle").textContent = t.title;
  document.getElementById("eventmeta").textContent = t.meta;
  document.getElementById("lblDays").textContent = t.days;
  document.getElementById("lblHrs").textContent = t.hrs;
  document.getElementById("lblMin").textContent = t.min;
  document.getElementById("lblSec").textContent = t.sec;
  document.getElementById("thTime").textContent = t.time;
  document.getElementById("thProg").textContent = t.prog;
  document.getElementById("thResp").textContent = t.resp;
  document.getElementById("thCd").textContent = t.cd;
  document.getElementById("btnEn").className = "w3-button w3-round-large " + (l==="en"?"w3-white":"w3-green");
  document.getElementById("btnSw").className = "w3-button w3-round-large " + (l==="sw"?"w3-white":"w3-green");
  buildRows();
  update();
}

function update(){
  const now = new Date();
  const t = T[lang];
  const start = startDate();
  const last = evStart(events.length-1);
  const diff = start - now;

  if(diff > 0){
    const days = Math.floor(diff/86400000);
    const hrs = Math.floor((diff%86400000)/3600000);
    const mins = Math.floor((diff%3600000)/60000);
    const secs = Math.floor((diff%60000)/1000);
    document.getElementById("d").textContent = days;
    document.getElementById("h").textContent = pad(hrs);
    document.getElementById("m").textContent = pad(mins);
    document.getElementById("s").textContent = pad(secs);
    document.getElementById("masterMsg").textContent = t.before;
  } else if(now <= last){
    ["d","h","m","s"].forEach(id=>document.getElementById(id).textContent="—");
    document.getElementById("masterMsg").textContent = t.live;
  } else {
    ["d","h","m","s"].forEach(id=>document.getElementById(id).textContent="✓");
    document.getElementById("masterMsg").textContent = t.done;
  }

  events.forEach((ev,i)=>{
    const cd = document.getElementById("cd"+i);
    const row = document.getElementById("row"+i);
    if(!cd||!row) return;
    const s = evStart(i);
    const next = (i<events.length-1)? evStart(i+1) : new Date(s.getTime()+15*60000);
    const d = s - now;
    row.className = "";
    if(d > 0){
      const dd=Math.floor(d/86400000), hh=Math.floor((d%86400000)/3600000),
            mm=Math.floor((d%3600000)/60000), ss=Math.floor((d%60000)/1000);
      const str = dd>0 ? `${dd}d ${pad(hh)}:${pad(mm)}:${pad(ss)}` : `${pad(hh)}:${pad(mm)}:${pad(ss)}`;
      cd.innerHTML = `<span class="w3-text-grey">${str}</span>`;
    } else if(now < next){
      cd.innerHTML = `<span class="w3-tag w3-green w3-round">● ${t.now}</span>`;
      row.className = "w3-pale-green now-row";
    } else {
      cd.innerHTML = `<span class="w3-tag w3-light-grey w3-round">${t.ended}</span>`;
      row.className = "w3-text-grey";
    }
  });
}

setLang("en");
setInterval(update, 1000);
</script>

</body>
</html>
@endverbatim
