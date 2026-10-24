<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $isElectricAvenue ? 'ELECTRIC AVENUE' : 'Vehiculos terminados' }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f3eee7;
                --panel: #fffdfa;
                --ink: #2e251d;
                --muted: #6e5f51;
                --line: #ddd0c2;
                --accent: #9f451d;
                --accent-soft: #fce3d4;
                --stripe: #f8f2eb;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Trebuchet MS", "Segoe UI", sans-serif;
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.55), transparent 24%),
                    linear-gradient(135deg, #efe1d0, var(--bg));
                color: var(--ink);
            }

            .shell {
                width: min(1180px, calc(100% - 28px));
                margin: 0 auto;
                padding: 24px 0 40px;
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                align-items: center;
                margin-bottom: 24px;
                padding: 18px 20px;
                border: 1px solid var(--line);
                border-radius: 24px;
                background: rgba(255, 253, 250, 0.92);
                box-shadow: 0 16px 32px rgba(92, 62, 36, 0.08);
            }

            .token {
                display: inline-flex;
                margin-bottom: 10px;
                padding: 7px 12px;
                border-radius: 999px;
                background: var(--accent-soft);
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .headline h1 {
                margin: 0 0 6px;
                font-size: clamp(1.7rem, 3.4vw, 2.7rem);
            }

            .headline p {
                margin: 0;
                color: var(--muted);
            }

            .topbar form button {
                padding: 12px 16px;
                border: 0;
                border-radius: 14px;
                background: #2e251d;
                color: #fff;
                font-weight: 700;
                cursor: pointer;
            }

            .list-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }

            .list-tabs a {
                padding: 12px 16px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: var(--panel);
                color: var(--ink);
                text-decoration: none;
                font-weight: 700;
            }

            .list-tabs a[aria-current="page"] {
                background: var(--accent);
                color: #fff;
            }

            .table-panel {
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 24px;
                background: var(--panel);
                box-shadow: 0 16px 32px rgba(92, 62, 36, 0.08);
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            thead th {
                padding: 18px 16px;
                background: #f4e8db;
                color: #5e4635;
                text-align: left;
                font-size: 13px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            tbody td {
                padding: 18px 16px;
                border-top: 1px solid var(--line);
                vertical-align: top;
            }

            .cell-label {
                display: none;
                margin-bottom: 8px;
                color: var(--accent);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            tbody tr:nth-child(even) {
                background: var(--stripe);
            }

            .plate {
                display: inline-flex;
                padding: 8px 12px;
                border: 1px solid var(--line);
                border-radius: 999px;
                background: #fff;
                font-size: 13px;
                font-weight: 800;
                letter-spacing: 0.08em;
            }

            .vehicle {
                font-weight: 700;
            }

            .description {
                color: var(--muted);
                line-height: 1.6;
                white-space: pre-line;
            }

            .photos-cell {
                min-width: 200px;
            }

            .photo-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .photo-item { width: 94px; }
            .photo-retry { width: 100%; padding: 8px 2px; cursor: pointer; }
            .pagination { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; margin: 20px 0; }
            .pagination a { color: var(--accent); font-weight: bold; }

            .photo-link {
                display: block;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid var(--line);
                background: #fff;
                box-shadow: 0 8px 18px rgba(92, 62, 36, 0.08);
            }

            .photo-thumb {
                display: block;
                width: 92px;
                height: 92px;
                object-fit: contain;
            }

            .photo-empty {
                color: var(--muted);
                font-size: 14px;
            }

            .send-payment-form {
                display: grid;
                gap: 10px;
                margin: 0;
            }

            .comment-box {
                width: 100%;
                min-height: 96px;
                padding: 12px 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: #fff;
                color: var(--ink);
                font: inherit;
                resize: vertical;
            }

            .empty {
                padding: 28px;
                border: 1px dashed var(--line);
                border-radius: 24px;
                background: rgba(255, 253, 250, 0.8);
                text-align: center;
                color: var(--muted);
            }

            .status-banner {
                margin-bottom: 18px;
                padding: 14px 16px;
                border: 1px solid #bad6bf;
                border-radius: 18px;
                background: #eef9ef;
                color: #235731;
                font-weight: 700;
            }

            .actions-cell {
                width: 280px;
            }

            .send-payment-button {
                width: 100%;
                padding: 12px 14px;
                border: 0;
                border-radius: 14px;
                background: var(--accent);
                color: #fff;
                font-weight: 700;
                cursor: pointer;
            }

            .send-payment-button:hover {
                filter: brightness(0.96);
            }

            @media (max-width: 720px) {
                .shell {
                    width: min(100% - 20px, 100%);
                    padding: 16px 0 28px;
                }

                .topbar {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 16px;
                    border-radius: 20px;
                }

                .topbar form,
                .topbar form button {
                    width: 100%;
                }

                .table-panel {
                    padding: 14px;
                    background: transparent;
                    border-radius: 24px;
                }

                table {
                    display: block;
                }

                thead {
                    display: none;
                }

                tbody {
                    display: grid;
                    gap: 14px;
                }

                tbody tr {
                    display: block;
                    border: 1px solid var(--line);
                    border-radius: 20px;
                    background: rgba(255, 253, 250, 0.92);
                    box-shadow: 0 14px 24px rgba(92, 62, 36, 0.08);
                    overflow: hidden;
                }

                tbody tr:nth-child(even) {
                    background: rgba(255, 253, 250, 0.92);
                }

                tbody td {
                    display: block;
                    padding: 14px 16px;
                    border-top: 0;
                }

                tbody td + td {
                    border-top: 1px solid var(--line);
                }

                .cell-label {
                    display: block;
                }

                .actions-cell {
                    width: auto;
                }

                .comment-box {
                    min-height: 110px;
                }

                .send-payment-button {
                    padding: 14px 16px;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="topbar">
                <div class="headline">
                    <span class="token">Acceso validado</span>
                    <h1>{{ $isElectricAvenue ? 'ELECTRIC AVENUE' : 'Vehiculos terminados' }}</h1>
                    <p>
                        @if ($isElectricAvenue)
                        Accediste con {{ $accessName }}. Esta vista muestra los vehiculos EV AVENUE terminados y no pagados, incluso con pago enviado.
                        @else
                        Accediste con {{ $accessName }}. Esta vista muestra solo trabajos terminados, no pagados y con pago no enviado.
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('frontend.logout') }}">
                    @csrf
                    <button type="submit">Cerrar acceso</button>
                </form>
            </section>

            <nav class="list-tabs" aria-label="Listas de vehiculos">
                <a href="{{ route('frontend.vehicle-works') }}" @if (! $isElectricAvenue) aria-current="page" @endif>Vehiculos terminados</a>
                @if ($canViewElectricAvenue)
                    <a href="{{ route('frontend.electric-avenue') }}" @if ($isElectricAvenue) aria-current="page" @endif>ELECTRIC AVENUE</a>
                @endif
            </nav>

            @if (session('status'))
                <section class="status-banner">
                    {{ session('status') }}
                </section>
            @endif

            @if ($vehicleWorks->isEmpty())
                <section class="empty">
                    {{ $isElectricAvenue ? 'No hay vehiculos EV AVENUE terminados y no pagados en este momento.' : 'No hay trabajos terminados pendientes de pago por enviar en este momento.' }}
                </section>
            @else
                <section class="table-panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Placa</th>
                                <th>Vehiculo</th>
                                <th>Descripcion del trabajo</th>
                                <th>Fotos</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicleWorks as $vehicleWork)
                                <tr>
                                    <td>
                                        <span class="cell-label">Placa</span>
                                        <span class="plate">{{ strtoupper($vehicleWork->plate) }}</span>
                                    </td>
                                    <td class="vehicle">
                                        <span class="cell-label">Vehiculo</span>
                                        {{ $vehicleWork->vehicle }}
                                    </td>
                                    <td class="description">
                                        <span class="cell-label">Descripcion del trabajo</span>
                                        {{ $vehicleWork->job_description }}
                                    </td>
                                    <td class="photos-cell">
                                        <span class="cell-label">Fotos</span>
                                        @if (filled($vehicleWork->job_photos))
                                            <div class="photo-grid">
                                                @foreach ($vehicleWork->job_photos as $photoIndex => $photo)
                                                    <div class="photo-item">
                                                    <a
                                                        href="{{ route('frontend.vehicle-works.photo', [$vehicleWork, $photoIndex]) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="photo-link"
                                                    >
                                                        <img
                                                            data-src="{{ route('frontend.vehicle-works.photo', [$vehicleWork, $photoIndex, 'thumbnail' => 1]) }}"
                                                            alt="Foto del trabajo de {{ $vehicleWork->vehicle }}"
                                                            class="photo-thumb"
                                                            decoding="async"
                                                            width="92"
                                                            height="92"
                                                        >
                                                    </a>
                                                    <button type="button" class="photo-retry" hidden>Reintentar foto</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="photo-empty">Sin fotos</span>
                                        @endif
                                    </td>
                                    <td class="actions-cell">
                                        <span class="cell-label">Accion</span>
                                        @if ($isElectricAvenue && $vehicleWork->payment_sent)
                                            <span class="token">Pago enviado</span>
                                            @if (filled($vehicleWork->comment))
                                                <p class="description">{{ $vehicleWork->comment }}</p>
                                            @endif
                                        @else
                                        <form
                                            method="POST"
                                            action="{{ route('frontend.vehicle-works.payment-sent', $vehicleWork) }}"
                                            class="send-payment-form"
                                        >
                                            @csrf
                                            @if ($isElectricAvenue)
                                                <input type="hidden" name="list" value="electric-avenue">
                                            @endif
                                            <textarea
                                                name="comment"
                                                class="comment-box"
                                                placeholder="Agrega un comentario"
                                                maxlength="2000"
                                            >{{ old('comment') }}</textarea>
                                            <button type="submit" class="send-payment-button">
                                                Enviar pago
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif
            <nav class="pagination" aria-label="Paginacion de trabajos">
                @if ($vehicleWorks->previousPageUrl())
                    <a href="{{ $vehicleWorks->previousPageUrl() }}" rel="prev">Anterior</a>
                @endif
                <span>Pagina {{ $vehicleWorks->currentPage() }} de {{ $vehicleWorks->lastPage() }} · {{ $vehicleWorks->total() }} trabajos</span>
                @if ($vehicleWorks->nextPageUrl())
                    <a href="{{ $vehicleWorks->nextPageUrl() }}" rel="next">Siguiente</a>
                @endif
            </nav>
        </main>
        <script>
            (() => {
                const queue = [];
                let active = 0;
                function enqueue(img) {
                    if (img.dataset.pending === '1') return;
                    img.dataset.pending = '1';
                    img.closest('.photo-item').querySelector('button').hidden = true;
                    queue.push(img);
                    drain();
                }
                function drain() {
                    while (active < 2 && queue.length) {
                        const img = queue.shift();
                        active++;
                        const finish = (ok) => {
                            img.onload = img.onerror = null;
                            active--;
                            img.dataset.pending = '0';
                            if (!ok && !img.dataset.retried) {
                                img.dataset.retried = '1';
                                setTimeout(() => enqueue(img), 1500);
                            } else {
                                img.closest('.photo-item').querySelector('button').hidden = ok;
                            }
                            drain();
                        };
                        img.onload = () => finish(true);
                        img.onerror = () => finish(false);
                        const url = new URL(img.dataset.src);
                        if (img.dataset.retried) url.searchParams.set('retry', Date.now());
                        img.src = url.href;
                    }
                }
                const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            observer.unobserve(entry.target);
                            enqueue(entry.target);
                        }
                    });
                }, { rootMargin: '200px' }) : null;
                document.querySelectorAll('.photo-thumb').forEach(img => {
                    img.closest('.photo-item').querySelector('button').addEventListener('click', () => enqueue(img));
                    if (observer) observer.observe(img);
                    else enqueue(img);
                });
            })();
        </script>
    </body>
</html>
