<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Consulta de Vehiculos</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f7efe5;
                --paper: #fffaf4;
                --line: #decbb6;
                --ink: #2f2318;
                --muted: #786452;
                --accent: #a5491c;
                --accent-soft: #ffe2d2;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Trebuchet MS", "Segoe UI", sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(165, 73, 28, 0.18), transparent 28%),
                    radial-gradient(circle at right, rgba(98, 62, 35, 0.14), transparent 26%),
                    linear-gradient(135deg, #f2e1d0, var(--bg));
                color: var(--ink);
            }

            .shell {
                width: min(960px, calc(100% - 32px));
                margin: 0 auto;
                padding: 48px 0;
            }

            .hero {
                display: grid;
                gap: 24px;
                grid-template-columns: 1.3fr 1fr;
                align-items: stretch;
            }

            .panel,
            .info {
                border: 1px solid var(--line);
                border-radius: 28px;
                background: rgba(255, 250, 244, 0.94);
                box-shadow: 0 20px 40px rgba(92, 62, 36, 0.12);
            }

            .panel {
                padding: 32px;
            }

            .info {
                padding: 18px;
                background:
                    linear-gradient(180deg, rgba(255, 226, 210, 0.9), rgba(255, 250, 244, 0.96));
            }

            .eyebrow {
                display: inline-flex;
                margin-bottom: 16px;
                padding: 8px 12px;
                border-radius: 999px;
                background: var(--accent-soft);
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0 0 12px;
                font-size: clamp(2rem, 5vw, 3.4rem);
                line-height: 0.95;
            }

            p {
                margin: 0;
                color: var(--muted);
                line-height: 1.6;
            }

            form {
                display: grid;
                gap: 16px;
                margin-top: 28px;
            }

            label {
                font-size: 14px;
                font-weight: 700;
            }

            input[type="password"] {
                width: 100%;
                padding: 16px 18px;
                border: 1px solid var(--line);
                border-radius: 18px;
                background: #fff;
                font-size: 16px;
                color: var(--ink);
            }

            input[type="password"]:focus {
                outline: 3px solid rgba(165, 73, 28, 0.18);
                border-color: var(--accent);
            }

            button {
                padding: 15px 18px;
                border: 0;
                border-radius: 18px;
                background: linear-gradient(135deg, #b85628, #8b3d19);
                color: white;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
            }

            .error {
                padding: 14px 16px;
                border-radius: 16px;
                background: #ffe1dd;
                color: #83261d;
                font-size: 14px;
            }

            .tesla-image {
                display: block;
                width: 100%;
                height: 100%;
                min-height: 420px;
                border-radius: 20px;
                object-fit: cover;
                object-position: center;
            }

            @media (max-width: 800px) {
                .hero {
                    grid-template-columns: 1fr;
                }

                .shell {
                    padding: 24px 0;
                }

                .panel,
                .info {
                    border-radius: 22px;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="hero">
                <article class="panel">
                    <span class="eyebrow">Taller</span>
                    <h1>Consulta el estado de tus vehiculos</h1>
                    <p>
                        Ingresa una contrasena frontend valida para abrir la tabla con todos los trabajos
                        registrados en el sistema.
                    </p>

                    <form method="POST" action="{{ route('frontend.login') }}">
                        @csrf

                        @if ($errors->has('password'))
                            <div class="error">{{ $errors->first('password') }}</div>
                        @endif

                        <div>
                            <label for="password">Contrasena de acceso</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Escribe la contrasena"
                                required
                                autofocus
                            >
                        </div>

                        <button type="submit">Entrar a la consulta</button>
                    </form>
                </article>

                <aside class="info">
                    <img
                        class="tesla-image"
                        src="https://images.unsplash.com/photo-1536883442700-ffaa4d76e372?auto=format&fit=crop&fm=jpg&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&q=80&w=1400"
                        alt="Tesla negro en carretera"
                    >
                </aside>
            </section>
        </main>
    </body>
</html>
