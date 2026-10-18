<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.html");
    exit();
}
$name = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : htmlspecialchars($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard – Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #particles { position:fixed; inset:0; pointer-events:none; z-index:0; }
        .grid-overlay {
            position:fixed; inset:0; z-index:0; pointer-events:none;
            background-image:
                linear-gradient(rgba(108,99,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg,rgba(108,99,255,0.04) 1px,transparent 1px);
            background-size:60px 60px;
        }
        .hero {
            position:relative; z-index:1;
            text-align:center; padding:80px 24px 20px;
        }
        .hero-eyebrow {
            display:inline-flex; align-items:center; gap:8px;
            font-size:0.78rem; font-weight:600; letter-spacing:0.12em;
            text-transform:uppercase; color:var(--accent);
            background:rgba(108,99,255,0.12); border:1px solid rgba(108,99,255,0.25);
            padding:6px 16px; border-radius:20px; margin-bottom:28px;
        }
        .hero-eyebrow .dot {
            width:7px; height:7px; background:var(--accent); border-radius:50%;
            animation:pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%,100%{opacity:1;transform:scale(1)}
            50%{opacity:.4;transform:scale(.7)}
        }
        .hero h1 { font-size:clamp(2.2rem,5vw,3.6rem); line-height:1.1; margin-bottom:16px; }
        .hero h1 .gradient {
            background:linear-gradient(120deg,var(--accent) 0%,var(--accent2) 100%);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
        }
        .hero-sub { color:var(--muted); font-size:1.05rem; max-width:420px; margin:0 auto 48px; }

        .action-grid {
            position:relative; z-index:1;
            display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr));
            gap:18px; max-width:900px; margin:0 auto 80px; padding:0 28px;
        }
        .action-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:18px; padding:32px 22px 28px;
            text-align:center; text-decoration:none; color:var(--text);
            display:flex; flex-direction:column; align-items:center; gap:12px;
            position:relative; overflow:hidden;
            transition:transform .3s cubic-bezier(.34,1.56,.64,1), border-color .3s, box-shadow .3s;
            opacity:0; animation:card-in .5s cubic-bezier(.22,1,.36,1) forwards;
        }
        .action-card::before {
            content:''; position:absolute; width:120px; height:120px; border-radius:50%;
            top:-40px; left:50%; transform:translateX(-50%);
            opacity:0; transition:opacity .4s; pointer-events:none;
        }
        .action-card:hover::before { opacity:1; }
        .action-card:hover {
            transform:translateY(-8px) scale(1.02);
            border-color:var(--card-accent,var(--accent));
            box-shadow:0 0 32px var(--card-glow,rgba(108,99,255,0.25));
        }
        .action-card.c-lost   { --card-accent:var(--lost);  --card-glow:rgba(255,101,132,0.2); }
        .action-card.c-lost::before { background:radial-gradient(var(--lost),transparent 70%); }
        .action-card.c-found  { --card-accent:var(--found); --card-glow:rgba(34,211,165,0.2); }
        .action-card.c-found::before{ background:radial-gradient(var(--found),transparent 70%); }
        .action-card.c-view   { --card-accent:#a78bfa;       --card-glow:rgba(167,139,250,0.2); }
        .action-card.c-view::before { background:radial-gradient(#a78bfa,transparent 70%); }
        .action-card.c-search { --card-accent:#38bdf8;       --card-glow:rgba(56,189,248,0.2); }
        .action-card.c-search::before{ background:radial-gradient(#38bdf8,transparent 70%); }
        .action-card.c-admin  { --card-accent:#fbbf24;       --card-glow:rgba(251,191,36,0.2); }
        .action-card.c-admin::before { background:radial-gradient(#fbbf24,transparent 70%); }

        .card-icon {
            font-size:2.4rem; line-height:1;
            filter:drop-shadow(0 0 10px var(--card-glow,rgba(108,99,255,.4)));
            transition:transform .3s cubic-bezier(.34,1.56,.64,1);
        }
        .action-card:hover .card-icon { transform:scale(1.18) rotate(-4deg); }
        .card-label { font-family:'Syne',sans-serif; font-weight:700; font-size:1rem; color:var(--text); }
        .card-desc  { font-size:.8rem; color:var(--muted); line-height:1.5; }
        .card-tag {
            margin-top:4px; padding:4px 12px; border-radius:20px;
            font-size:.7rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;
            border:1px solid var(--card-accent,var(--accent));
            color:var(--card-accent,var(--accent));
            background:transparent; transition:background .2s,color .2s;
        }
        .action-card:hover .card-tag { background:var(--card-accent,var(--accent)); color:#0d0f14; }

        .action-card:nth-child(1){animation-delay:.1s}
        .action-card:nth-child(2){animation-delay:.2s}
        .action-card:nth-child(3){animation-delay:.3s}
        .action-card:nth-child(4){animation-delay:.4s}
        .action-card:nth-child(5){animation-delay:.5s}
        @keyframes card-in {
            from{opacity:0;transform:translateY(24px) scale(.97)}
            to{opacity:1;transform:translateY(0) scale(1)}
        }
        .hero-eyebrow{animation:fade-up .6s ease both}
        .hero h1{animation:fade-up .6s .1s ease both}
        .hero-sub{animation:fade-up .6s .2s ease both}
        @keyframes fade-up {
            from{opacity:0;transform:translateY(16px)}
            to{opacity:1;transform:translateY(0)}
        }
        @media(max-width:640px){
            .action-grid{grid-template-columns:1fr 1fr}
        }
    </style>
</head>
<body>
<div class="grid-overlay"></div>
<canvas id="particles"></canvas>

<nav class="navbar">
    <a href="index.php" class="brand">Lost<span>&</span>Found</a>
    <div class="nav-links">
        <a href="lost.html">Report Lost</a>
        <a href="found.html">Report Found</a>
        <a href="view.php">View Items</a>
        <a href="search.php">Search</a>
        <?php if(isset($_SESSION['admin'])): ?>
            <a href="admin.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-eyebrow"><span class="dot"></span>Welcome back</div>
    <h1>Hey, <span class="gradient"><?php echo $name; ?></span> 👋</h1>
    <p class="hero-sub">What would you like to do today? Browse lost items, report something found, or search the database.</p>
</section>

<div class="action-grid">
    <a href="lost.html" class="action-card c-lost">
        <div class="card-icon">🔴</div>
        <div class="card-label">Report Lost</div>
        <div class="card-desc">Lost something? Post a report so others can help.</div>
        <span class="card-tag">Lost Item</span>
    </a>
    <a href="found.html" class="action-card c-found">
        <div class="card-icon">🟢</div>
        <div class="card-label">Report Found</div>
        <div class="card-desc">Found something? Help the owner get it back.</div>
        <span class="card-tag">Found Item</span>
    </a>
    <a href="view.php" class="action-card c-view">
        <div class="card-icon">📋</div>
        <div class="card-label">View All Items</div>
        <div class="card-desc">Browse every lost & found listing in one place.</div>
        <span class="card-tag">Browse</span>
    </a>
    <a href="search.php" class="action-card c-search">
        <div class="card-icon">🔍</div>
        <div class="card-label">Search Items</div>
        <div class="card-desc">Look up items by name, location, or category.</div>
        <span class="card-tag">Search</span>
    </a>
    <?php if(isset($_SESSION['admin'])): ?>
    <a href="admin.php" class="action-card c-admin">
        <div class="card-icon">⚙️</div>
        <div class="card-label">Admin Panel</div>
        <div class="card-desc">Manage all listings, users and settings.</div>
        <span class="card-tag">Admin</span>
    </a>
    <?php endif; ?>
</div>

<script>
(function(){
    const c=document.getElementById('particles'),ctx=c.getContext('2d');
    let W,H,dots=[];
    function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
    resize();window.addEventListener('resize',resize);
    const N=55,A='108,99,255',P='255,101,132';
    for(let i=0;i<N;i++) dots.push({x:Math.random()*1920,y:Math.random()*1080,r:Math.random()*1.6+.4,vx:(Math.random()-.5)*.22,vy:(Math.random()-.5)*.22,c:Math.random()>.5?A:P,a:Math.random()*.5+.1});
    function draw(){
        ctx.clearRect(0,0,W,H);
        for(let i=0;i<N;i++) for(let j=i+1;j<N;j++){const dx=dots[i].x-dots[j].x,dy=dots[i].y-dots[j].y,d=Math.sqrt(dx*dx+dy*dy);if(d<140){ctx.beginPath();ctx.strokeStyle=`rgba(${dots[i].c},${.12*(1-d/140)})`;ctx.lineWidth=.6;ctx.moveTo(dots[i].x,dots[i].y);ctx.lineTo(dots[j].x,dots[j].y);ctx.stroke();}}
        dots.forEach(d=>{ctx.beginPath();ctx.arc(d.x,d.y,d.r,0,Math.PI*2);ctx.fillStyle=`rgba(${d.c},${d.a})`;ctx.fill();d.x+=d.vx;d.y+=d.vy;if(d.x<0||d.x>W)d.vx*=-1;if(d.y<0||d.y>H)d.vy*=-1;});
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>
</body>
</html>
