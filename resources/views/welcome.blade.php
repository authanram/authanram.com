<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daniel Seuffer</title>
    <meta name="description" content="Hallo, I'm Daniel. I do open source and work for InnoGE.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root { --pink: #ff2bd6; --cyan: #00f0ff; --violet: #8a2be2; --sun: #ffb800; --bg: #0b0016; }
        * { box-sizing: border-box; margin: 0; }
        html { background: var(--bg); color: #e8e6ff; font: 22px/1.4 'VT323', ui-monospace, monospace; }
        body { min-height: 100vh; overflow-x: hidden; }
        a { color: inherit; }
        a:focus-visible { outline: 2px dashed var(--cyan); outline-offset: 6px; }

        .sky { position: fixed; inset: 0; z-index: -2; background: linear-gradient(#0b0016 0%, #1a0033 45%, #3d0055 62%, #0b0016 62.1%); }
        .sun { position: absolute; left: 50%; top: 18vh; width: min(60vw, 420px); aspect-ratio: 1; translate: -50% 0; border-radius: 50%;
            background: linear-gradient(var(--sun), var(--pink) 75%);
            mask: repeating-linear-gradient(#000 0 58%, transparent 58% 61%, #000 61% 66%, transparent 66% 70%, #000 70% 74%, transparent 74% 79%, #000 79% 83%, transparent 83% 89%, #000 89% 92%, transparent 92%);
            filter: drop-shadow(0 0 40px var(--pink)); opacity: .55; }
        .grid { position: fixed; left: -50%; right: -50%; bottom: 0; height: 38vh; z-index: -1;
            background: linear-gradient(transparent 0 calc(100% - 4px), var(--pink) calc(100% - 3px) calc(100% - 1px), transparent 100%) 0 0 / 100% 80px, linear-gradient(90deg, transparent 0 calc(100% - 4px), var(--pink) calc(100% - 3px) calc(100% - 1px), transparent 100%) 0 0 / 80px 100%;
            transform: perspective(600px) rotateX(50deg); transform-origin: bottom; opacity: .4; will-change: background-position;
            mask: linear-gradient(transparent 10%, #000 70%); animation: drive 4s linear infinite; }
        @keyframes drive { to { background-position: 0 80px, 0 0; } }
        body::after { content: ''; position: fixed; inset: 0; pointer-events: none; background: repeating-linear-gradient(transparent 0 2px, rgba(0,0,0,.25) 2px 4px); }

        main { max-width: 760px; margin: 0 auto; padding: 12vh 16px 4rem; display: grid; gap: 4.5rem; }
        section { display: grid; gap: 1.25rem; justify-items: center; text-align: center; }
        .prompt { color: var(--cyan); text-shadow: 0 0 8px var(--cyan); font-size: 1.1rem; }
        .prompt::before { content: '$ '; color: var(--pink); }

        h1, h2 { font-family: 'Orbitron', sans-serif; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; }
        h1 { font-size: clamp(2rem, 8vw, 3.6rem); line-height: 1.05; color: #fff;
            text-shadow: 0 0 6px #fff, 0 0 18px var(--pink), 0 0 42px var(--pink), 0 0 80px var(--violet); }
        h1 .cursor { color: var(--cyan); animation: blink 1s steps(1) infinite; }
        @keyframes blink { 50% { opacity: 0; } }
        h2 { font-size: clamp(1.1rem, 4vw, 1.5rem); color: var(--cyan); text-shadow: 0 0 10px var(--cyan); }

        .avatar { position: relative; z-index: 1; width: 160px; height: 160px; border-radius: 50%; border: 3px solid var(--cyan);
            box-shadow: 0 0 0 6px var(--bg), 0 0 0 8px var(--pink), 0 0 40px var(--pink); animation: pulse 3s ease-in-out infinite; }
        @keyframes pulse { 50% { box-shadow: 0 0 0 6px var(--bg), 0 0 0 8px var(--cyan), 0 0 60px var(--cyan); } }

        .term { width: 100%; text-align: left; background: rgba(11, 0, 22, .8); border: 1px solid var(--pink); border-radius: 6px;
            box-shadow: 0 0 24px rgba(255, 43, 214, .35); backdrop-filter: blur(4px); }
        .term header { display: flex; gap: 6px; align-items: center; padding: .4rem .75rem; border-bottom: 1px solid rgba(255, 43, 214, .4); color: #b9a6d9; font-size: .8rem; }
        .term header i { width: 10px; height: 10px; border-radius: 50%; background: var(--pink); }
        .term header i:nth-child(2) { background: var(--sun); } .term header i:nth-child(3) { background: var(--cyan); margin-right: .5rem; }
        .term .body { padding: 1rem 1.25rem; display: grid; gap: .9rem; }
        .repo { display: block; text-decoration: none; padding: .6rem .8rem; border-left: 3px solid var(--cyan); background: rgba(0, 240, 255, .05); transition: background .2s, transform .2s; }
        .repo:hover { background: rgba(0, 240, 255, .14); transform: translateX(4px); }
        .repo b { color: var(--cyan); font-weight: normal; font-size: 1.15rem; }
        .repo small { display: block; color: #cfc6ea; font-size: .9rem; }
        .repo em { font-style: normal; color: var(--sun); font-size: .85rem; }
        .stat { color: #58e78e; text-shadow: 0 0 8px #58e78e; }

        .btn { display: inline-block; padding: .5rem 1.2rem; border: 2px solid var(--pink); color: #fff; text-decoration: none; text-transform: uppercase; letter-spacing: .1em;
            box-shadow: 0 0 12px var(--pink), inset 0 0 12px var(--pink); transition: background .2s, color .2s; }
        .btn:hover { background: var(--pink); color: var(--bg); }

        .logo { display: block; width: min(100%, 340px); padding: 1rem; filter: drop-shadow(0 0 12px rgba(88, 231, 142, .6)); transition: transform .2s; }
        .logo:hover { transform: scale(1.04); }
        .logo svg { display: block; width: 100%; height: auto; }
        .logo .st0 { fill: #58e78e; } .logo .st1 { fill: #48aa7b; } .logo .st2 { fill: #f4f2ff; } .logo .st3 { fill: #284898; }

        .cya { font-family: 'Orbitron', sans-serif; font-size: clamp(2rem, 7vw, 3rem); color: #fff; text-shadow: 0 0 12px var(--cyan), 0 0 36px var(--cyan); }

        footer { max-width: 760px; margin: 0 auto; padding: 2rem 16px 3rem; font-size: .9rem; color: #b9a6d9; border-top: 1px dashed rgba(255, 43, 214, .4); }
        footer h3 { font-family: 'Orbitron', sans-serif; font-size: .75rem; letter-spacing: .2em; text-transform: uppercase; color: var(--pink); margin-bottom: .5rem; }
        footer address { font-style: normal; }
        footer a { color: var(--cyan); text-decoration: none; }
        footer a:hover { text-decoration: underline; }

        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
    </style>
</head>
<body>
    <div class="sky" aria-hidden="true"><div class="sun"></div></div>
    <div class="grid" aria-hidden="true"></div>

    <main>
        <section>
            <img class="avatar" src="https://avatars.githubusercontent.com/u/1874088?v=4&s=320" alt="Daniel Seuffer" width="160" height="160">
            <p class="prompt">whoami</p>
            <h1>Hallo, I'm Daniel<span class="cursor" aria-hidden="true">_</span></h1>
        </section>

        <section>
            <h2>I do open source</h2>
            <div class="term">
                <header><i></i><i></i><i></i>~/github/authanram — pinned</header>
                <div class="body">
                    <a class="repo" href="https://github.com/InnoGE/laravel-rclone">
                        <b>InnoGE/laravel-rclone</b> <em>★ 18 · PHP</em>
                        <small>A sleek Laravel package that wraps rclone with an elegant, fluent API syntax.</small>
                    </a>
                    <a class="repo" href="https://github.com/InnoGE/laravel-speculation-rules-api">
                        <b>InnoGE/laravel-speculation-rules-api</b> <em>★ 14 · PHP</em>
                        <small>A streamlined solution to utilize the Speculation Rules API, allowing you to speed up your website performance significantly.</small>
                    </a>
                    <p><span class="stat">3,171</span> contributions in the last year</p>
                </div>
            </div>
            <a class="btn" href="https://github.com/authanram">github.com/authanram</a>
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
            <p class="cya">Cya <span role="img" aria-label="space invader">👾</span></p>
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
