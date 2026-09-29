<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daniel Seuffer</title>
    <meta name="description" content="Hallo, I'm Daniel. I do open source and work for InnoGE.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Mr+Dafoe&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root { --pink: #ff2bd6; --cyan: #00f0ff; --violet: #7b2ff7; --sun: #ffd319; --orange: #ff901f; --bg: #0d0221; }
        * { box-sizing: border-box; margin: 0; }
        html { background: var(--bg); color: #ece8ff; font: 22px/1.4 'VT323', ui-monospace, monospace; }
        body { overflow-x: hidden; }
        a { color: inherit; }
        a:focus-visible { outline: 2px dashed var(--cyan); outline-offset: 6px; }

        .hero { position: relative; min-height: min(82svh, 760px); display: grid; place-items: center; overflow: hidden; isolation: isolate; z-index: 5;
            background: radial-gradient(1px 1px at 12% 18%, #fff, transparent), radial-gradient(1px 1px at 28% 8%, #fff, transparent), radial-gradient(1.5px 1.5px at 44% 22%, #fff, transparent),
                radial-gradient(1px 1px at 63% 12%, #fff, transparent), radial-gradient(1.5px 1.5px at 78% 26%, #fff, transparent), radial-gradient(1px 1px at 90% 9%, #fff, transparent),
                radial-gradient(1px 1px at 6% 34%, #fff, transparent), radial-gradient(1px 1px at 52% 4%, #fff, transparent), radial-gradient(1px 1px at 84% 40%, #fff, transparent),
                linear-gradient(#0d0221 0%, #261447 40%, #6b1e70 58%, #ff3d8b 63%, #0d0221 63.2%); }
        .sun { position: absolute; left: 50%; bottom: 37%; width: min(70vw, 460px); aspect-ratio: 1; translate: -50% 35%; border-radius: 50%; z-index: -1;
            background: linear-gradient(var(--sun) 10%, var(--orange) 45%, var(--pink) 80%);
            mask: linear-gradient(#000 45%, transparent 45% 48%, #000 48% 55%, transparent 55% 59%, #000 59% 65%, transparent 65% 70%, #000 70% 76%, transparent 76% 82%, #000 82%);
            filter: drop-shadow(0 0 60px rgba(255, 61, 139, .8)); }
        .mountains { position: absolute; left: 0; right: 0; bottom: 37%; width: 100%; height: min(22vh, 180px); z-index: -1; }
        .mountains path { fill: #13052e; stroke: var(--cyan); stroke-width: 2; stroke-linejoin: round; filter: drop-shadow(0 0 4px var(--cyan)); vector-effect: non-scaling-stroke; }
        .mountains .wire { fill: none; stroke: rgba(0, 240, 255, .35); stroke-width: 1; filter: none; }
        .palm { position: absolute; bottom: 30%; height: min(46vh, 440px); z-index: -1; fill: #0d0221; filter: drop-shadow(0 0 1px var(--pink)); }
        .palm.l { left: -2vw; } .palm.r { right: -2vw; transform: scaleX(-1); height: min(38vh, 360px); }
        .grid { position: absolute; left: -50%; right: -50%; bottom: 0; height: 37%; z-index: -1;
            background: linear-gradient(transparent 0 calc(100% - 4px), var(--pink) calc(100% - 3px) calc(100% - 1px), transparent 100%) 0 0 / 100% 80px,
                linear-gradient(90deg, transparent 0 calc(100% - 4px), var(--pink) calc(100% - 3px) calc(100% - 1px), transparent 100%) 0 0 / 80px 100%,
                linear-gradient(#2a0845, var(--bg));
            transform: perspective(600px) rotateX(50deg); transform-origin: top; will-change: background-position;
            animation: drive 4s linear infinite; }
        @keyframes drive { to { background-position: 0 80px, 0 0, 0 0; } }

        .hero::after { content: ''; position: absolute; inset: 0; z-index: 0; pointer-events: none; background: linear-gradient(transparent 80%, var(--bg)), repeating-linear-gradient(transparent 0 2px, rgba(0, 0, 0, .18) 2px 4px); }
        .title { position: relative; z-index: 1; text-align: center; display: grid; justify-items: center; gap: .4rem; padding: 3rem 16px 14vh; }
        .avatar { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 5px var(--pink), 0 0 36px var(--pink); margin-bottom: .5rem; }
        .script { font-family: 'Mr Dafoe', cursive; font-size: clamp(2.6rem, 10vw, 4.6rem); line-height: 1; color: #fff; rotate: -6deg; margin-bottom: -1.2rem; position: relative; z-index: 1;
            text-shadow: 0 0 4px #fff, 0 0 12px var(--pink), 0 0 28px var(--pink), 0 0 56px var(--pink); }
        .chrome { font-family: 'Audiowide', sans-serif; font-size: clamp(2.6rem, 13vw, 8rem); line-height: 1; letter-spacing: .04em;
            background: linear-gradient(#1c3a8c 5%, #8fd3ff 42%, #fff 49%, #2b1638 51%, #b44bd6 72%, #ffd6f6 95%);
            -webkit-background-clip: text; background-clip: text; color: transparent; -webkit-text-stroke: 1px rgba(255, 255, 255, .6);
            filter: drop-shadow(0 2px 0 #3a0a4a) drop-shadow(0 0 18px rgba(255, 43, 214, .6)); }
        .tagline { color: var(--cyan); letter-spacing: .3em; text-transform: uppercase; text-shadow: 0 0 8px var(--cyan); font-size: 1rem; }

        .osd { position: fixed; top: 14px; z-index: 6; font-size: 1.3rem; color: #fff; text-shadow: 2px 2px 0 rgba(0, 0, 0, .6); pointer-events: none; }
        .osd.l { left: 18px; } .osd.r { right: 18px; text-align: right; }
        .tracking { position: fixed; inset: 0; z-index: 4; pointer-events: none;
            background: linear-gradient(transparent 0 46%, rgba(255, 255, 255, .05) 48%, rgba(255, 255, 255, .09) 50%, rgba(255, 255, 255, .05) 52%, transparent 54%) 0 0 / 100% 200%,
                repeating-linear-gradient(transparent 0 2px, rgba(0, 0, 0, .18) 2px 4px);
            animation: tracking 9s linear infinite; }
        @keyframes tracking { from { background-position: 0 100%, 0 0; } to { background-position: 0 -100%, 0 0; } }

        main { max-width: 820px; margin: 0 auto; padding: 2rem 16px 2rem; display: grid; gap: 3rem; }
        section { display: grid; gap: 1rem; justify-items: center; text-align: center; }
        h2 { font-family: 'Mr Dafoe', cursive; font-weight: normal; font-size: clamp(2.2rem, 8vw, 3.2rem); line-height: 1; color: #fff; rotate: -4deg;
            text-shadow: 0 0 10px var(--cyan), 0 0 30px var(--cyan); }

        .cabinet { width: 100%; text-align: left; padding: 1.4rem 1.4rem 1.1rem; border: 3px double var(--cyan); background: linear-gradient(rgba(38, 20, 71, .85), rgba(13, 2, 33, .95));
            box-shadow: 0 0 24px rgba(0, 240, 255, .25), inset 0 0 40px rgba(123, 47, 247, .25); }
        .cabinet h3 { font-family: 'Audiowide', sans-serif; font-weight: normal; text-align: center; color: var(--sun); letter-spacing: .15em; font-size: 1rem; margin-bottom: 1rem; text-shadow: 0 0 8px var(--orange); }
        .row { display: grid; grid-template-columns: 3.2rem 1fr auto; gap: .2rem .8rem; padding: .55rem .5rem; text-decoration: none; border-bottom: 1px dashed rgba(0, 240, 255, .25); }
        .row:hover { background: rgba(255, 43, 214, .12); }
        .row .rank { color: var(--pink); } .row .name { color: var(--cyan); overflow-wrap: anywhere; } .row .score { color: var(--sun); }
        .row small { grid-column: 2 / -1; color: #bfb3e0; font-size: .85rem; }
        .hiscore { display: flex; justify-content: space-between; gap: 1rem; padding: .8rem .5rem 0; color: #fff; }
        .hiscore b { font-weight: normal; color: #58e78e; text-shadow: 0 0 8px #58e78e; }
        .coin { display: inline-block; padding: .55rem 1.4rem; text-decoration: none; font-family: 'Audiowide', sans-serif; font-size: .8rem; letter-spacing: .15em; color: var(--bg);
            background: linear-gradient(var(--sun), var(--orange) 55%, var(--pink)); box-shadow: 0 4px 0 #8a1c6b, 0 0 24px rgba(255, 144, 31, .6); transition: translate .1s, box-shadow .1s; }
        .coin:hover { translate: 0 2px; box-shadow: 0 2px 0 #8a1c6b, 0 0 32px rgba(255, 144, 31, .9); }

        .logo { display: block; width: min(100%, 360px); padding: 1.4rem 1.8rem; border: 1px solid rgba(88, 231, 142, .5); border-radius: 4px; background: rgba(13, 2, 33, .6);
            box-shadow: 0 0 30px rgba(88, 231, 142, .25); transition: box-shadow .2s; }
        .logo:hover { box-shadow: 0 0 44px rgba(88, 231, 142, .55); }
        .logo svg { display: block; width: 100%; height: auto; }
        .logo .st0 { fill: #58e78e; } .logo .st1 { fill: #48aa7b; } .logo .st2 { fill: #f4f2ff; } .logo .st3 { fill: #284898; }

        .cya { font-family: 'Audiowide', sans-serif; font-size: clamp(2.4rem, 9vw, 4rem); }
        .cya b { font-weight: normal; background: linear-gradient(#1c3a8c 5%, #8fd3ff 42%, #fff 49%, #2b1638 51%, #b44bd6 72%, #ffd6f6 95%);
            -webkit-background-clip: text; background-clip: text; color: transparent; filter: drop-shadow(0 0 16px rgba(255, 43, 214, .6)); }
        .hint { color: #8d7fb8; font-size: .85rem; letter-spacing: .12em; }

        footer { max-width: 820px; margin: 0 auto; padding: 1.5rem 16px 2rem; font-size: .9rem; color: #bfb3e0; border-top: 1px solid rgba(255, 43, 214, .35); }
        footer h3 { font-family: 'Audiowide', sans-serif; font-weight: normal; font-size: .75rem; letter-spacing: .25em; text-transform: uppercase; color: var(--pink); margin-bottom: .5rem; }
        footer address { font-style: normal; }
        footer a { color: var(--cyan); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        .turbo .grid { animation-duration: .5s; }
        .turbo .sun { filter: hue-rotate(160deg) drop-shadow(0 0 60px var(--cyan)); }

        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
    </style>
</head>
<body>
    <div class="osd l" aria-hidden="true">PLAY ▶<br><span id="counter">SP 0:00:00</span></div>
    <div class="osd r" aria-hidden="true">{{ strtoupper(now()->format('M. d')) }}<br>1986</div>
    <div class="tracking" aria-hidden="true"></div>

    <header class="hero">
        <div class="sun" aria-hidden="true"></div>
        <svg class="mountains" viewBox="0 0 1000 200" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0 200 L0 120 L90 60 L170 130 L260 40 L360 140 L420 110 L500 170 L580 110 L640 140 L740 30 L830 120 L910 70 L1000 130 L1000 200 Z"/>
            <path class="wire" d="M90 60 L130 200 M90 60 L40 200 M260 40 L220 200 M260 40 L310 200 M740 30 L700 200 M740 30 L790 200 M910 70 L880 200 M910 70 L960 200 M0 160 L1000 160"/>
        </svg>
        <svg class="palm l" viewBox="0 0 200 400" aria-hidden="true">
            <path d="M104 400 C98 300 92 200 104 110 L112 110 C104 200 110 300 118 400 Z"/>
            <path d="M108 110 C80 80 40 80 0 110 C40 90 70 96 108 116 Z M108 110 C90 70 60 40 20 40 C60 56 84 80 104 118 Z M108 110 C120 70 150 44 196 46 C154 60 130 84 112 118 Z M108 110 C140 90 176 96 200 130 C170 110 140 108 110 118 Z M108 110 C104 70 110 36 132 10 C120 44 116 80 112 116 Z M108 112 C80 120 50 150 44 190 C60 156 84 132 110 120 Z"/>
        </svg>
        <svg class="palm r" viewBox="0 0 200 400" aria-hidden="true">            <path d="M104 400 C98 300 92 200 104 110 L112 110 C104 200 110 300 118 400 Z"/>
            <path d="M108 110 C80 80 40 80 0 110 C40 90 70 96 108 116 Z M108 110 C90 70 60 40 20 40 C60 56 84 80 104 118 Z M108 110 C120 70 150 44 196 46 C154 60 130 84 112 118 Z M108 110 C140 90 176 96 200 130 C170 110 140 108 110 118 Z M108 110 C104 70 110 36 132 10 C120 44 116 80 112 116 Z"/>
        </svg>
        <div class="grid" aria-hidden="true"></div>

        <div class="title">
            <img class="avatar" src="https://avatars.githubusercontent.com/u/1874088?v=4&s=320" alt="Daniel Seuffer" width="150" height="150">
            <p class="script">Hallo, I'm</p>
            <h1 class="chrome">DANIEL</h1>
            <p class="tagline">Code · Coffee · Open Source</p>
        </div>
    </header>

    <main>
        <section>
            <h2>I do open source</h2>
            <div class="cabinet">
                <h3>— HIGH SCORES —</h3>
                <a class="row" href="https://github.com/InnoGE/laravel-rclone">
                    <span class="rank">1ST</span><span class="name">InnoGE/laravel-rclone</span><span class="score">★ 18</span>
                    <small>A sleek Laravel package that wraps rclone with an elegant, fluent API syntax.</small>
                </a>
                <a class="row" href="https://github.com/InnoGE/laravel-speculation-rules-api">
                    <span class="rank">2ND</span><span class="name">InnoGE/laravel-speculation-rules-api</span><span class="score">★ 14</span>
                    <small>A streamlined solution to utilize the Speculation Rules API, allowing you to speed up your website performance significantly.</small>
                </a>
                <p class="hiscore"><span>CONTRIBUTIONS / YEAR</span><b>003171</b></p>
            </div>
            <a class="coin" href="https://github.com/authanram">INSERT COIN · GITHUB</a>
        </section>

        <section>
            <h2>And I work for</h2>
            <a class="logo" href="https://innoge.de/ueber-uns" aria-label="InnoGE">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1049.1 309.1" aria-hidden="true">
<path class="st0" d="M348.6,51.5c0,27.1-21,49.6-48.1,51.4c-1.1,0.1-2.2,0.1-3.3,0.1H149.6c-56.9,0-103,46.2-103,103.1
	c0,48.4,33.7,90.2,80.9,100.6c-84-15-140.1-95.2-125.1-179.3C15.5,53.7,79.7,0,154.7,0H297C325.5,0,348.6,23.1,348.6,51.5
	C348.6,51.5,348.6,51.5,348.6,51.5z"/>
<path class="st1" d="M274.3,154.6c0,28.4-23.1,51.5-51.5,51.5h-79.5c-28.5,0-51.5,23-51.5,51.5c0,22.6,14.7,42.6,36.3,49.2l-0.4-0.1
	c0,0,0,0-0.1,0C71.9,294.5,36.8,239.6,49,184c10.4-47.2,52.2-80.9,100.6-80.9h73.1C251.2,103.1,274.3,126.1,274.3,154.6z"/>
<path class="st2" d="M348.6,257.6c0,28.5-23.1,51.5-51.5,51.5H154.7c-0.9,0-1.7,0-2.6,0c-1.5,0-3.1-0.1-4.6-0.1
	c-6.5-0.3-13-1-19.5-2.1c-27.2-8.3-42.5-37.1-34.2-64.4c6.6-21.7,26.7-36.5,49.4-36.4H297c1.1,0,2.2,0,3.3,0.1
	C327.5,208,348.5,230.5,348.6,257.6z"/>
<path class="st3" d="M154.7,309.1h-5c0.8,0,1.6,0,2.5,0S153.8,309.1,154.7,309.1z"/>
<path class="st3" d="M152.1,309.1c-0.8,0-1.6,0-2.5,0c-1.2,0-2.5,0-3.7-0.1c0.5,0,1.1,0,1.6-0.1C149,309,150.5,309.1,152.1,309.1z"
	/>
<path class="st3" d="M149.6,309.1h-6.4c0.9,0,1.8,0,2.6-0.1l0,0C147.1,309.1,148.4,309.1,149.6,309.1z"/>
<path class="st3" d="M147.4,309c-0.5,0-1,0.1-1.6,0.1l0,0c-5.9-0.2-11.8-0.9-17.6-2.2c-0.1,0-0.2,0-0.2-0.1
	C134.4,307.9,140.9,308.7,147.4,309z"/>
<path class="st3" d="M145.9,309.1c-0.9,0-1.8,0.1-2.6,0.1c-5.1,0-10.1-0.7-15-2.2C134,308.1,139.9,308.8,145.9,309.1z"/>
<path class="st3" d="M128.2,306.9l-0.6-0.1l0.4,0.1C128.1,306.9,128.1,306.9,128.2,306.9z"/>
<path class="st2" d="M434.3,89.6v137.5h-30V89.6H434.3z"/>
<path class="st2" d="M486.1,167.5v59.6h-29.5V124h28.1v18.2h1.2c2.3-5.9,6.4-10.9,11.8-14.3c5.5-3.5,12.3-5.3,20.1-5.3
	c6.7-0.2,13.4,1.5,19.3,4.7c5.5,3.1,9.9,7.8,12.8,13.4c3,5.8,4.6,12.7,4.6,20.7v65.6h-29.5v-60.5c0-6.3-1.6-11.2-5-14.8
	s-8-5.3-13.9-5.3c-3.6-0.1-7.3,0.8-10.5,2.5c-3,1.7-5.5,4.2-7.1,7.2C487,159.6,486.1,163.5,486.1,167.5z"/>
<path class="st2" d="M604.2,167.5v59.6h-29.5V124h28.1v18.2h1.2c2.3-5.9,6.4-10.9,11.8-14.3c5.5-3.5,12.2-5.3,20.1-5.3
	c6.8-0.2,13.4,1.4,19.3,4.7c5.5,3.1,9.9,7.8,12.8,13.4c3,5.8,4.6,12.7,4.6,20.7v65.6h-29.5v-60.5c0-6.3-1.6-11.2-5-14.8
	s-8-5.3-13.8-5.3c-3.6-0.1-7.3,0.8-10.5,2.5c-3,1.6-5.5,4.2-7.1,7.2C605.1,159.6,604.2,163.5,604.2,167.5z"/>
<path class="st2" d="M740.1,229.1c-10.7,0-20-2.2-27.9-6.7c-7.7-4.3-14-10.8-18.1-18.7c-4.2-8-6.4-17.3-6.4-27.8
	c0-10.5,2.1-19.9,6.4-28c4.1-7.8,10.4-14.3,18.1-18.6c7.8-4.5,17.1-6.7,27.9-6.7c10.7,0,20,2.2,27.8,6.7c7.7,4.3,14,10.8,18.1,18.6
	c4.3,8,6.4,17.3,6.4,28c0,10.7-2.1,19.9-6.4,27.8c-4.1,7.8-10.4,14.3-18.1,18.7C760.1,226.9,750.8,229.1,740.1,229.1z M740.2,206.9
	c4.4,0.1,8.8-1.3,12.2-4.1c3.4-3,6-6.8,7.4-11.1c3.4-10.4,3.4-21.7,0-32.1c-1.4-4.3-4-8.2-7.4-11.1c-3.5-2.8-7.8-4.2-12.2-4.1
	c-4.5-0.1-8.9,1.3-12.4,4.1c-3.5,2.9-6.1,6.8-7.5,11.1c-3.4,10.4-3.4,21.7,0,32.1c1.4,4.3,4,8.2,7.5,11.1
	C731.3,205.6,735.7,207.1,740.2,206.9L740.2,206.9z"/>
<path class="st2" d="M902,134c-0.9-3.1-2.3-6-4.1-8.7c-1.7-2.5-3.9-4.7-6.4-6.4c-2.6-1.8-5.5-3.2-8.6-4.1c-3.5-1-7-1.4-10.6-1.4
	c-13.8-0.5-26.5,7.7-31.8,20.4c-3,6.7-4.6,14.8-4.6,24.3c0,9.6,1.5,17.7,4.5,24.4c2.6,6.3,7,11.6,12.7,15.3
	c5.8,3.6,12.6,5.4,19.4,5.3c6,0.2,11.9-1,17.4-3.5c4.6-2.2,8.5-5.6,11.1-10c2.6-4.6,4-9.9,3.8-15.2l6.1,0.9h-36.5v-21.9h59.3v17.3
	c0,12.1-2.6,22.5-7.9,31.1c-5.2,8.6-12.7,15.5-21.7,20c-9.2,4.7-19.8,7-31.7,7c-13.3,0-24.9-2.9-35-8.6c-10-5.7-18.2-14.1-23.5-24.4
	c-5.6-10.5-8.4-23.1-8.4-37.5c0-11.1,1.7-21.1,5-29.8c3.1-8.3,7.9-15.9,14.1-22.2c6-6,13.2-10.8,21.1-13.9
	c8.3-3.2,17.2-4.9,26.1-4.8c7.6-0.1,15.2,1.1,22.4,3.4c6.6,2.1,12.9,5.4,18.4,9.6c5.3,4,9.8,9,13.2,14.7c3.4,5.7,5.7,12.1,6.6,18.7
	L902,134z"/>
<path class="st2" d="M953.3,227.1V89.6h95.5v24h-65.5v32.8h60.6v24h-60.6v32.8h65.8v24L953.3,227.1z"/>
</svg>
            </a>
        </section>

        <section>
            <p class="cya"><b>Cya</b> <span role="img" aria-label="space invader">👾</span></p>
            <p class="hint">↑ ↑ ↓ ↓ ← → ← → B A</p>
        </section>
    </main>

    <footer>
        <h3>Imprint</h3>
        <address>
            Daniel Seuffer<br>
            Judengasse 3<br>
            75387 Neubulach<br>
            Germany<br><br>
            <a href="mailto:authanram@gmail.com">authanram@gmail.com</a><br>
            <a href="tel:+4915735800060">+49 157 358 000 60</a>
        </address>
    </footer>

    <script>
        const start = Date.now(), counter = document.getElementById('counter');
        setInterval(() => {
            const s = Math.floor((Date.now() - start) / 1000);
            counter.textContent = `SP ${Math.floor(s / 3600)}:${String(Math.floor(s / 60) % 60).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        }, 1000);

        const konami = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
        let pos = 0;
        addEventListener('keydown', e => {
            pos = e.key === konami[pos] ? pos + 1 : (e.key === konami[0] ? 1 : 0);
            if (pos === konami.length) { document.body.classList.toggle('turbo'); pos = 0; }
        });
    </script>
</body>
</html>
