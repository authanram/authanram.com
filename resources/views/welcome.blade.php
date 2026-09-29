<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daniel Seuffer</title>
    <meta name="description" content="Hallo, I'm Daniel. I do open source and work for InnoGE.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Monoton&family=Kaushan+Script&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        :root { --pink: #ff3cac; --blue: #2b86ff; --cyan: #3ef2ff; --sun: #ffc93c; --orange: #ff6b3d; --bg: #07001a; }
        * { box-sizing: border-box; margin: 0; }
        html { background: var(--bg); color: #eae6ff; font: 18px/1.5 'Share Tech Mono', ui-monospace, monospace; }
        body { overflow-x: hidden; }
        a { color: inherit; }
        a:focus-visible { outline: 2px dashed var(--cyan); outline-offset: 6px; }

        .drive { position: relative; height: min(78svh, 720px); min-height: 520px; overflow: hidden; isolation: isolate;
            background: linear-gradient(#07001a 0%, #1d0b4d 30%, #6a0f7a 52%, #ff3cac 62%, #ff6b3d 66%, var(--bg) 66.2%); }
        .drive::after { content: ''; position: absolute; inset: 0; pointer-events: none; background: linear-gradient(transparent 82%, var(--bg)); }
        .sun { position: absolute; left: 50%; bottom: 34%; width: min(64vw, 420px); aspect-ratio: 1; translate: -50% 0; border-radius: 50%; z-index: -1;
            background: linear-gradient(#fff3b0, var(--sun) 30%, var(--orange) 60%, var(--pink));
            mask: linear-gradient(#000 50%, transparent 50% 52%, #000 52% 60%, transparent 60% 63%, #000 63% 70%, transparent 70% 74%, #000 74% 80%, transparent 80% 85%, #000 85% 89%, transparent 89%);
            filter: drop-shadow(0 0 50px var(--pink)); }
        .city { position: absolute; left: 0; bottom: 34%; width: 100%; height: min(26vh, 220px); z-index: -1; }
        .city .far { fill: #2a0c55; }
        .city .near { fill: #0c0326; }
        .city .edge { fill: none; stroke: var(--pink); stroke-width: 1.5; vector-effect: non-scaling-stroke; filter: drop-shadow(0 0 3px var(--pink)); }
        .ground { position: absolute; left: 0; right: 0; bottom: 0; height: 34%; z-index: -2; background: linear-gradient(#1b0540, var(--bg)); border-top: 2px solid var(--cyan); box-shadow: 0 0 18px var(--cyan); }
        .road { position: absolute; left: 0; bottom: 0; width: 100%; height: 34%; z-index: -1; }
        .road .edge { stroke: var(--cyan); stroke-width: 2; vector-effect: non-scaling-stroke; filter: drop-shadow(0 0 4px var(--cyan)); }
        .road .lane { stroke: var(--sun); stroke-width: .8; stroke-dasharray: 6 8; animation: lane .8s linear infinite; }
        @keyframes lane { to { stroke-dashoffset: -14; } }

        .hero { position: absolute; inset: 0; display: grid; place-content: center; justify-items: center; gap: .3rem; padding: 0 16px 14vh; text-align: center; }
        .avatar { width: 132px; height: 132px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 4px var(--blue), 0 0 30px var(--blue); margin-bottom: .6rem; }
        .hello { font-family: 'Kaushan Script', cursive; font-size: clamp(1.8rem, 6vw, 2.8rem); color: var(--sun); rotate: -5deg; text-shadow: 0 0 10px var(--orange), 0 0 26px var(--pink); }
        .neon { font-family: 'Monoton', cursive; font-weight: normal; font-size: clamp(3rem, 14vw, 7.5rem); line-height: 1; color: #fff; letter-spacing: .02em;
            text-shadow: 0 0 4px #fff, 0 0 12px var(--pink), 0 0 26px var(--pink), 0 0 52px var(--pink), 0 0 90px var(--blue); }

        main { max-width: 760px; margin: 0 auto; padding: 1.5rem 16px 2rem; display: grid; gap: 3rem; }
        section { display: grid; gap: 1rem; justify-items: center; text-align: center; }
        h2 { font-family: 'Kaushan Script', cursive; font-weight: normal; font-size: clamp(1.8rem, 6vw, 2.4rem); color: var(--cyan); rotate: -3deg; text-shadow: 0 0 10px var(--cyan), 0 0 30px var(--blue); }

        .tape { width: min(100%, 520px); aspect-ratio: 1.6; padding: 5%; border-radius: 14px; position: relative;
            background: linear-gradient(145deg, #2b2440, #16112a); border: 2px solid #3d3560; box-shadow: 0 16px 40px rgba(0, 0, 0, .6), 0 0 30px rgba(255, 60, 172, .25); }
        .tape .label { height: 72%; border-radius: 6px; padding: 4% 5% 0; display: grid; grid-template-rows: auto 1fr; text-align: left;
            background: linear-gradient(#f4ecd8 0 30%, var(--sun) 30% 40%, var(--orange) 40% 50%, var(--pink) 50% 60%, #f4ecd8 60%); color: #1b1030; }
        .tape .label b { font-family: 'Kaushan Script', cursive; font-weight: normal; font-size: clamp(1rem, 4vw, 1.5rem); line-height: 1.1; }
        .tape .label span { font-size: .7rem; letter-spacing: .15em; }
        .tape .window { position: absolute; left: 25%; right: 25%; top: 45%; height: 22%; border-radius: 40px; background: #0d0a1c; border: 2px solid #3d3560;
            display: flex; justify-content: space-between; align-items: center; padding: 0 6%; }
        .reel { height: 78%; aspect-ratio: 1; border-radius: 50%; border: 4px dotted #eae6ff; animation: spin 3s linear infinite; }
        .tape:hover .reel { animation-duration: .6s; }
        @keyframes spin { to { rotate: 360deg; } }
        .tape .side { position: absolute; left: 7%; top: 8%; font-family: 'Share Tech Mono', monospace; font-size: .75rem; background: #1b1030; color: #f4ecd8; padding: 0 .4rem; border-radius: 3px; }
        .tape::after { content: ''; position: absolute; left: 18%; right: 18%; bottom: 0; height: 18%; background: #221b38; border: 2px solid #3d3560; border-bottom: 0; clip-path: polygon(8% 0, 92% 0, 100% 100%, 0 100%); }
        .tape .screws { z-index: 1; position: absolute; inset: auto 10% 6%; display: flex; justify-content: space-between; }
        .tape .screws i { width: 10px; height: 10px; border-radius: 50%; background: #3d3560; }

        .tracks { width: min(100%, 520px); list-style: none; padding: 0; text-align: left; border-top: 1px solid rgba(62, 242, 255, .3); }
        .tracks a { display: grid; grid-template-columns: 2.2rem 1fr auto; gap: 0 .6rem; padding: .6rem .3rem; text-decoration: none; border-bottom: 1px solid rgba(62, 242, 255, .3); }
        .tracks a:hover { background: rgba(255, 60, 172, .12); }
        .tracks .no { color: var(--pink); } .tracks .name { color: var(--cyan); overflow-wrap: anywhere; } .tracks .len { color: var(--sun); }
        .tracks small { grid-column: 2 / -1; color: #b3a9d6; font-size: .8rem; }
        .total { width: min(100%, 520px); display: flex; justify-content: space-between; color: #b3a9d6; font-size: .9rem; }
        .total b { color: #58e78e; font-weight: normal; text-shadow: 0 0 8px #58e78e; }
        .play { display: inline-flex; gap: .6rem; align-items: center; padding: .5rem 1.3rem; border-radius: 999px; text-decoration: none; color: #fff; border: 2px solid var(--pink);
            box-shadow: 0 0 14px var(--pink), inset 0 0 14px rgba(255, 60, 172, .5); transition: background .2s; }
        .play:hover { background: var(--pink); }

        .sign { display: block; width: min(100%, 360px); padding: 1.3rem 1.8rem; border-radius: 16px; border: 3px solid #58e78e;
            box-shadow: 0 0 10px #58e78e, 0 0 36px rgba(88, 231, 142, .45), inset 0 0 18px rgba(88, 231, 142, .35); transition: box-shadow .2s; }
        .sign:hover { box-shadow: 0 0 16px #58e78e, 0 0 60px rgba(88, 231, 142, .7), inset 0 0 24px rgba(88, 231, 142, .5); }
        .sign svg { display: block; width: 100%; height: auto; }
        .sign .st0 { fill: #58e78e; } .sign .st1 { fill: #48aa7b; } .sign .st2 { fill: #f4f2ff; } .sign .st3 { fill: #284898; }

        .cya { font-family: 'Monoton', cursive; font-size: clamp(2.4rem, 10vw, 4rem); color: #fff; text-shadow: 0 0 8px var(--cyan), 0 0 24px var(--cyan), 0 0 48px var(--blue); }

        footer { max-width: 760px; margin: 0 auto; padding: 1.5rem 16px 2rem; font-size: .85rem; color: #b3a9d6; border-top: 1px solid rgba(255, 60, 172, .35); }
        footer h3 { font-size: .75rem; letter-spacing: .3em; text-transform: uppercase; color: var(--pink); margin-bottom: .4rem; }
        footer address { font-style: normal; }
        footer a { color: var(--cyan); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
    </style>
</head>
<body>
    <header class="drive">
        <div class="sun" aria-hidden="true"></div>
        <svg class="city" viewBox="0 0 1000 200" preserveAspectRatio="none" aria-hidden="true">
            <path class="far" d="M0 200V120h40V90h30v30h20V60h35v60h25V100h30V40h20V20h10v20h20v80h30V80h40v40h30V110h30V70h25v50h260V70h25v40h30V60h40v60h25V30h15V10h10v20h15v90h30V90h35v30h20V70h30v130Z"/>
            <path class="near" d="M0 200V150h60V120h45v30h40V100h30v100h40V130h50v70h440V130h50v70h40V100h30v50h40V120h45v30h60v50Z"/>
            <path class="edge" d="M0 150h60V120h45v30h40V100h30v100M215 200V130h50v70M705 200V130h50v70M795 200V100h30v50h40V120h45v30h90"/>
        </svg>
        <div class="ground" aria-hidden="true"></div>
        <svg class="road" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            <polygon points="47,0 53,0 92,100 8,100" fill="#120330"/>
            <path class="edge" d="M47 0 8 100M53 0 92 100" fill="none"/>
            <path class="lane" d="M50 0V100"/>
        </svg>

        <div class="hero">
            <img class="avatar" src="https://avatars.githubusercontent.com/u/1874088?v=4&s=264" alt="Daniel Seuffer" width="132" height="132">
            <p class="hello">Hallo, I'm</p>
            <h1 class="neon">Daniel</h1>
        </div>
    </header>

    <main>
        <section>
            <h2>I do open source</h2>
            <div class="tape" aria-hidden="true">
                <span class="side">A</span>
                <div class="label"><b>authanram — Open Source Mix</b><span>C-90 · HIGH BIAS · PHP</span></div>
                <div class="window"><i class="reel"></i><i class="reel"></i></div>
                <div class="screws"><i></i><i></i><i></i></div>
            </div>
            <ol class="tracks">
                <li><a href="https://github.com/InnoGE/laravel-rclone">
                    <span class="no">01</span><span class="name">InnoGE/laravel-rclone</span><span class="len">★ 18</span>
                    <small>A sleek Laravel package that wraps rclone with an elegant, fluent API syntax.</small>
                </a></li>
                <li><a href="https://github.com/InnoGE/laravel-speculation-rules-api">
                    <span class="no">02</span><span class="name">InnoGE/laravel-speculation-rules-api</span><span class="len">★ 14</span>
                    <small>A streamlined solution to utilize the Speculation Rules API, allowing you to speed up your website performance significantly.</small>
                </a></li>
            </ol>
            <p class="total"><span>Contributions in the last year</span><b>3,171</b></p>
            <a class="play" href="https://github.com/authanram">▶ Play on GitHub</a>
        </section>

        <section>
            <h2>And I work for</h2>
            <a class="sign" href="https://innoge.de/ueber-uns" aria-label="InnoGE">
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
            <p class="cya">Cya <span role="img" aria-label="sunglasses">😎</span></p>
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
</body>
</html>
