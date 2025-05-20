
@php
    $symbol = match($selectedCurrency['symbol'] ?? '') {
        'USD', '$' => 'US$',
        'BOB', 'Bs' => 'Bs',
        default => 'Bs',
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Analysis Report</title>
    <style>
        @media print {
            .page-break {
                page-break-after: always;
            }
        }

        body {
            background-color: #ffffff;
            color: #1f2937; /* gris oscuro */
            padding: 2rem;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 1.5rem;
        }

        .header h1 {
            font-size: 1.875rem; /* text-3xl */
            font-weight: bold;
        }

        .header p {
            font-size: 1.125rem; /* text-lg */
            font-weight: bold;
            color: #4b5563; /* text-gray-600 */
        }

        .header hr {
            margin-top: 0.5rem;
            border-color: #d1d5db; /* border-gray-300 */
        }

        .header img {
            height: 4rem;
        }

        .listing-section {
            margin-bottom: 2rem;
        }

        .listing-card {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .listing-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ef4444; /* text-red-500 */
            margin-bottom: 0.5rem;
        }

        .listing-info p {
            font-size: 0.875rem;
            color: #4b5563; /* text-gray-600 */
            margin-bottom: 0.25rem;
        }

        .listing-price {
            font-size: 1.125rem;
            font-weight: bold;
            color: #10b981; /* text-green-500 */
            margin-top: 0.5rem;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .gallery img {
            width: 100%;
            height: 12rem;
            object-fit: cover;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem 2rem;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .features-grid span {
            font-weight: 600;
            color: #1f2937;
        }

        .tags {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .tag {
            font-size: 0.75rem;
            background-color: #dbeafe;
            color: #2563eb;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .header-remax {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .header-left {
            flex: 1;
        }

        .text-red, .logo-red {
         color: #ef4444;
        }

        .text-blue, .logo-blue {
            color: #3b82f6;
        }

        .subtitle {
            font-size: 1.125rem;
            font-weight: bold;
            color: #4b5563;
            margin: 0.25rem 0 0 0;
        }

        .header-line {
            border: none;
            border-top: 1px solid #d1d5db;
            margin-top: 0.5rem;
        }
        .property-section {
            margin-bottom: 2rem;
        }

        .property-card {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .property-header {
            margin-bottom: 1rem;
        }

        .property-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ef4444;
            margin: 0 0 0.25rem;
        }

        .property-meta {
            font-size: 0.875rem;
            color: #4b5563;
            margin: 0.25rem 0;
        }

        .property-price {
            font-size: 1.125rem;
            font-weight: bold;
            color: #10b981;
            margin-top: 0.5rem;
        }

        .gallery {
            display: flex;
            justify-content: center;
            margin: 1rem 0;
        }

        .gallery-img {
            width: 10rem;
            height: 10rem;
            object-fit: contain;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .label {
            font-weight: 600;
            color: #1f2937;
        }
        .features-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .feature-tag {
            padding: 0.25rem 0.5rem;
            background-color: #dbeafe;
            color: #2563eb;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }

        .footer {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            margin-top: 2rem;
        }
        .pdf-body {
            background-color: #ffffff; /* bg-white */
            color: #1f2937; /* text-gray-800 */
            padding: 2rem; /* p-8 */
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        .section-agent {
            margin-bottom: 2rem;
        }

        .header-agent {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 1.5rem;
        }

        .header-left {
            flex-grow: 1;
        }

        .logo-red {
            color: #ef4444;
        }

        .logo-blue {
            color: #3b82f6;
        }

        .market-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #4b5563;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .divider {
            border: 1px solid #d1d5db;
            margin-top: 0.5rem;
        }
        .agent-card {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: linear-gradient(to right, #4c89d8, #9beaf8, #e48c8c); /* azul claro -> blanco -> rojo claro */
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .agent-photo {
            flex-shrink: 0;
        }

        .agent-img {
            width: 6rem;
            height: 6rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .agent-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .agent-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: #1f2937; /* text-gray-800 */
        }

        .agent-sub {
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .agent-details p {
            font-size: 0.875rem;
            margin: 0.25rem 0;
            color: #374151; /* text-gray-700 */
        }

        .agent-details strong {
            font-weight: 600;
            color: #1f2937;
        }


        .sub-section-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #374151;
            margin: 1.5rem 0 1rem;
        }

        .section-agent {
            margin-bottom: 2rem;
        }

        .market-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #4b5563;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .divider {
            border: 1px solid #d1d5db;
            margin-top: 0.5rem;
        }

        .logo-img {
            height: 4rem;
        }

        .sub-section-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #374151;
            margin: 1.5rem 0 1rem;
        }

        .property-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            table-layout: fixed; /* control total de anchos */
        }

        .property-table th,
        .property-table td {
            padding: 0.5rem 0.3rem;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
            word-break: break-word;
        }

        /* Columnas específicas */
        .property-table th:nth-child(1),
        .property-table td:nth-child(1) { width: 4%; } /* # */
        .property-table th:nth-child(2),
        .property-table td:nth-child(2) { width: 18%; } /* Dirección */
        .property-table th:nth-child(3),
        .property-table td:nth-child(3) { width: 14%; } /* Propiedad */
        .property-table th:nth-child(4),
        .property-table td:nth-child(4) { width: 8%; }  /* Dormitorios */
        .property-table th:nth-child(5),
        .property-table td:nth-child(5) { width: 8%; }  /* Baños */
        .property-table th:nth-child(6),
        .property-table td:nth-child(6) { width: 10%; } /* SqM */
        .property-table th:nth-child(7),
        .property-table td:nth-child(7) { width: 10%; } /* Precio/SqM */
        .property-table th:nth-child(8),
        .property-table td:nth-child(8) { width: 14%; } /* Precio o Precio Captación */
        .property-table th:nth-child(9),
        .property-table td:nth-child(9) { width: 14%; } /* DoM */

        /* Encabezado */
        .property-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: 600;
        }

        .section-market-90 {
            margin-top: 2rem;
        }

        .market-title-90 {
            font-size: 1.125rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 1rem;
        }

        .footer {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            margin-top: 3rem;
        }
        .section-averages,
        .section-graph {
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 1.5rem;
        }

        .header-left {
            flex-grow: 1;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .header-line {
            border: none;
            border-top: 1px solid #d1d5db;
            margin-top: 0.5rem;
        }
        .chart-container {
            width: 100%;
        }

        .header-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 1.5rem !important;
            table-layout: fixed !important;
        }

        .header-table td {
            vertical-align: bottom !important;
            padding: 0 !important;
        }

        .header-left {
            width: 100% !important;
        }

        .header-right {
            width: 1% !important;
            text-align: right !important;
            vertical-align: bottom !important;
        }

        .logo-title {
            font-size: 1.875rem !important;
            font-weight: bold !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .subtitle {
            font-size: 1.125rem !important;
            font-weight: bold !important;
            color: #4b5563 !important;
            margin: 0.25rem 0 !important;
        }

        .divider {
            margin-top: 0.5rem !important;
            border: none !important;
            border-top: 1px solid #d1d5db !important;
        }

        .logo-img {
            height: 3.5rem !important;
            width: auto !important;
            object-fit: contain !important;
            display: block !important;
        }

        .agent-card-table {
            width: 100% !important;
            border-spacing: 1.5rem !important;
            background: linear-gradient(to right, #4c89d8, #9beaf8, #e48c8c) !important;
            border-radius: 8px !important;
            margin-bottom: 2rem !important;
        }

        .agent-photo-cell {
            width: 100px !important;
            vertical-align: top !important;
        }

        .agent-img {
            width: 6rem !important;
            height: 6rem !important;
            border-radius: 9999px !important;
            object-fit: cover !important;
            border: 4px solid #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .agent-info-cell {
            vertical-align: top !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
        }

        .agent-name {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            margin: 0 0 0.25rem 0 !important;
            color: #1f2937 !important;
        }

        .agent-sub {
            font-size: 0.875rem !important;
            color: #4b5563 !important;
            margin-bottom: 0.5rem !important;
        }

        .agent-details-table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 0.875rem !important;
        }

        .agent-details-table td {
            padding: 0.2rem 0.5rem !important;
            vertical-align: top !important;
            color: #374151 !important;
        }

        .agent-details-table td strong {
            color: #1f2937 !important;
            font-weight: 600 !important;
        }

    </style>
</head>
<body class="pdf-body">
    @foreach ($enrichedProperties as $property)
    <table class="header-table" role="presentation">
        <tr>
            <td class="header-left">
                <h1 class="logo-title">
                    <span class="text-red">RE</span><span class="text-blue">/</span><span class="text-red">MAX</span>
                </h1>
                <p class="subtitle">Análisis comparativo de mercado</p>
                <hr class="divider">
            </td>
            <td class="header-right">
                <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="logo-img">
            </td>
        </tr>
    </table>


   <!-- Detalles del listado -->
    <section class="property-section">
        <div class="property-card">
            <!-- Título y Precio -->
            <div class="property-header">
                <h2 class="property-title">{{ $property['subtype_name'] ?? 'Propiedad sin título' }}</h2>
                <p class="property-meta">MLSID: {{ $property['MLSID'] ?? 'N/A' }}</p>
                <p class="property-meta">Ubicación: {{ $property['location']['city'] ?? 'Ciudad no especificada' }}, {{ $property['location']['zone'] ?? 'Zona no especificada' }}</p>
                <h3 class="property-price">
                    {{ $selectedCurrency['symbol'] ?? 'Bs' }} {{ is_numeric($property['first_price']) ? number_format($property['first_price'], 2, '.', ',') : $property['first_price'] }}
                </h3>

            </div>
            <!-- Galería de imágenes -->
            <!-- Galería de imágenes (en tabla de 3 columnas) -->
            <table style="width: 100%; border-spacing: 12px; margin-top: 16px;">
                @php
                    $chunks = array_chunk($property['multimedias'] ?? [], 3);
                @endphp

                @forelse ($chunks as $row)
                    <tr>
                        @foreach ($row as $media)
                            <td style="width: 33.33%; vertical-align: top;">
                                <img src="file://{{ $media['link'] }}"
                                    alt="Imagen de la propiedad"
                                    style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px;">
                            </td>
                        @endforeach

                        {{-- Si la fila tiene menos de 3 imágenes, rellenar las columnas vacías --}}
                        @for ($i = count($row); $i < 3; $i++)
                            <td style="width: 33.33%;"></td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <img src="file://{{ public_path('storage/default.gif') }}"
                                alt="Imagen por defecto"
                                style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px;">
                        </td>
                    </tr>
                @endforelse
            </table>

            <!-- Características -->
            <div class="features-grid">
                <div><span class="label">Dormitorios:</span> {{ $property['listing_information']['number_bedrooms'] ?? 'N/A' }}</div>
                <div><span class="label">Baños:</span> {{ $property['listing_information']['number_bathrooms'] ?? 'N/A' }}</div>
                <div><span class="label">Área Total:</span> {{ $property['listing_information']['total_area'] ?? 'N/A' }} m²</div>
                <div><span class="label">Precio por m²:</span> {{ $property['usd_per_m2'] ?? 'N/A' }}/m²</div>
                <div><span class="label">Año de Construcción:</span> {{ $property['year_built'] ?? 'N/A' }}</div>
                <div><span class="label">Días en el mercado:</span> {{ $property['days_in_market'] ?? 'N/A' }}</div>
            </div>

            <!-- Etiquetas de características -->
            <div class="features-tags">
                @foreach ($property['features'] as $feature)
                    <span class="feature-tag">{{ $feature }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pie de página -->
    <footer class="footer">
        <p>Generado por RE/MAX Bolivia</p>
    </footer>


    <div class="page-break"></div>
    @endforeach

    <section class="section-agent">
        <table class="header-table" role="presentation">
            <tr>
                <td class="header-left">
                    <h1 class="logo-title">
                        <span class="text-red">RE</span><span class="text-blue">/</span><span class="text-red">MAX</span>
                    </h1>
                    <p class="subtitle">Análisis comparativo de mercado</p>
                    <hr class="divider">
                </td>
                <td class="header-right">
                    <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="logo-img">
                </td>
            </tr>
        </table>
        <table class="agent-card-table" role="presentation">
            <tr>
                <td class="agent-photo-cell">
                    <img src="file://{{ $loggedInAgentData['image_url'] }}" alt="Foto del Agente" class="agent-img">
                </td>
                <td class="agent-info-cell">
                    <h3 class="agent-name">{{ $loggedInAgentData['name'] ?? 'Sin Nombre' }}</h3>
                    <p class="agent-sub">RE/MAX Bolivia</p>

                    <table class="agent-details-table" role="presentation">
                        <tr><td><strong>Email:</strong></td><td>{{ $loggedInAgentData['email'] ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Teléfono:</strong></td><td>{{ $loggedInAgentData['phone_number'] ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Oficina:</strong></td><td>{{ $loggedInAgentData['office_name'] ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Ubicación Oficina:</strong></td>
                            <td>
                                {{ $loggedInAgentData['office_location']['city'] ?? 'N/A' }},
                                {{ $loggedInAgentData['office_location']['province'] ?? 'N/A' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <h2 class="sub-section-title">En el mercado (Menos de 90 días)</h2>
        <table class="property-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Dirección</th>
                    <th>Propiedad</th>
                    <th>Dormitorios</th>
                    <th>Baños</th>
                    <th>SqM</th>
                    <th>Precio/SqM</th>
                    <th>Precio</th>
                    <th>DoM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrichedProperties as $key => $property)
                    @if ($property['days_in_market'] <= 90)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</td>
                            <td>{{ $property['subtype_name'] ?? 'N/A' }}</td>
                            <td>{{ $property['listing_information']['number_bedrooms'] ?? 0 }}</td>
                            <td>{{ $property['listing_information']['number_bathrooms'] ?? 0 }}</td>
                            <td>{{ $property['listing_information']['total_area'] ?? 0 }} m²</td>
                            <td>{{ $property['usd_per_m2'] ?? 'N/A' }}/m²</td>
                            <td>US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</td>
                            <td>{{ $property['days_in_market'] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="section-market-90">
        <h2 class="market-title-90">En el mercado (Más de 90 días)</h2>
        <table class="property-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Dirección</th>
                    <th>Propiedad</th>
                    <th>Dormitorios</th>
                    <th>Baños</th>
                    <th>SqM</th>
                    <th>Precio/SqM</th>
                    <th>Precio Captación</th>
                    <th>DoM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrichedProperties as $key => $property)
                    @if ($property['days_in_market'] > 90)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</td>
                            <td>{{ $property['subtype_name'] ?? 'N/A' }}</td>
                            <td>{{ $property['listing_information']['number_bedrooms'] ?? 0 }}</td>
                            <td>{{ $property['listing_information']['number_bathrooms'] ?? 0 }}</td>
                            <td>{{ $property['listing_information']['total_area'] ?? 0 }} m²</td>
                            <td>{{ $property['usd_per_m2'] ?? 'N/A' }}/m²</td>
                            <td>
                                {{ $selectedCurrency['symbol'] ?? 'Bs' }}
                                {{ is_numeric($property['first_price']) ? number_format($property['first_price'], 2, '.', ',') : $property['first_price'] }}
                              </td>
                            <td>{{ $property['days_in_market'] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </section>

    <footer class="footer">
        <p>Generado por RE/MAX Bolivia</p>
    </footer>
    <section class="section-averages">
        <header class="section-header">
            <div class="header-left">
                <p class="section-title">Promedios del Mercado</p>
                <hr class="header-line">
            </div>
        </header>

        <table class="property-table">
            <thead>
                <tr>
                    <th>Días en el mercado</th>
                    <th>Precio Captación</th>
                    <th>Precio Venta</th>
                    <th>Precio/M²</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $summary['Días en el mercado'] ?? 'N/A' }}</td>
                    <td> {{ $symbol }} {{ $summary['Precio captacion'] ?? 'N/A' }}</td>
                    <td> {{ $symbol }} {{ $summary['Precio venta'] ?? 'N/A' }}</td>
                    <td>{{ $symbol }} {{ $summary['Precio/M²'] ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <div class="page-break"></div>

    <section class="section-graph">
        <table class="header-table" role="presentation">
            <tr>
                <td class="header-left">
                    <h1 class="logo-title">
                        <span class="text-red">RE</span><span class="text-blue">/</span><span class="text-red">MAX</span>
                    </h1>
                    <p class="subtitle">Grafica dias en el mercado </p>
                    <hr class="divider">
                </td>
                <td class="header-right">
                    <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="logo-img">
                </td>
            </tr>
        </table>
    </section>

	<div class="chart-container" id="chart">
	</div>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Obtener los datos de Laravel pasados a la vista
			var properties = @json($enrichedProperties);

			if (!Array.isArray(properties) || properties.length === 0) {
				console.warn("No hay datos disponibles.");
				return;
			}

			// Clasificar propiedades
			const enMercado = properties.filter(p => p.status !== "Venta Aceptada/Vendida" && p.status !== "Alquilado");
			const vendidas = properties.filter(p => p.status === "Venta Aceptada/Vendida" || p.status === "Alquilado");

			// Formatear datos para ApexCharts
			const enMercadoData = enMercado.map(p => ({
				x: `${p.location.city ?? 'Desconocido'}, ${p.location.zone ?? 'Desconocido'}`,
				y: p.days_in_market ?? 0
			}));

			const vendidasData = vendidas.map(p => ({
				x: `${p.location.city ?? 'Desconocido'}, ${p.location.zone ?? 'Desconocido'}`,
				y: p.days_in_market ?? 0
			}));

			// Configurar ApexCharts con datos de Laravel
			var options = {
				chart: {
					type: "bar",
					height: 400
				},
				plotOptions: {
					bar: {
						horizontal: true,
						dataLabels: { position: "end" }
					}
				},
				dataLabels: {
					enabled: true,
					style: { colors: ["#000000"] }
				},
				series: [
					{ name: "En el mercado", data: enMercadoData },
					{ name: "Vendidas Recientemente", data: vendidasData }
				],
				colors: ["#1E90FF", "#FFA500"],
				xaxis: { title: { text: "Días en el Mercado" } },
				yaxis: { title: { text: "Ubicación" } },
				title: { text: "Gráfica de Días en el Mercado", align: "center" },
				legend: { show: true, position: "bottom", horizontalAlign: "center" }
			};

			var chart = new ApexCharts(document.querySelector("#chart"), options);
			chart.render();
		});
	</script>

</body>
</html>
