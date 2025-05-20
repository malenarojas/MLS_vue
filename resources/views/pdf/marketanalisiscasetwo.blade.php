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

        .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }
    .header h1 {
        font-size: 28px;
        font-weight: bold;
    }
    .header p {
        font-size: 16px;
        font-weight: bold;
        color: #4B5563;
    }
    .property-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px; /* opcional */
    }
    .property-card {
        width: 24%;
        box-sizing: border-box;
        margin-bottom: 24px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .property-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .property-header h2 {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        word-break: break-word;
    }
    .property-status {
        background-color: #DBEAFE;
        color: #2563EB;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        word-break: break-word;
    }
    .property-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .property-details p {
        font-size: 12px;
        color: #4B5563;
        margin-bottom: 8px;
    }
    .property-features {
        margin-top: 16px;
    }
    .property-features h3 {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .property-features ul {
        list-style-type: disc;
        padding-left: 20px;
        font-size: 12px;
        color: #4B5563;
    }

    .section-agent {
    margin-bottom: 2rem;
}
.property-card {
        page-break-inside: avoid;
    }
    .property-grid {
        page-break-after: always;
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
.text-red {
    color: #ef4444;
}
.text-blue {
    color: #3b82f6;
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

/* Tarjeta del Agente */
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

/* Título de subsección */
.sub-section-title {
    font-size: 1.125rem;
    font-weight: bold;
    color: #374151;
    margin: 1.5rem 0 1rem;
}

/* Tabla de propiedades */
.property-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    table-layout: fixed;
}
.property-table th,
.property-table td {
    padding: 0.5rem 0.3rem;
    border: 1px solid #d1d5db;
    text-align: center;
    vertical-align: middle;
    word-break: break-word;
}
.property-table th {
    background-color: #f3f4f6;
    color: #1f2937;
    font-weight: 600;
}

/* Columnas específicas de la tabla */
.property-table th:nth-child(1),
.property-table td:nth-child(1) { width: 4%; }
.property-table th:nth-child(2),
.property-table td:nth-child(2) { width: 18%; }
.property-table th:nth-child(3),
.property-table td:nth-child(3) { width: 14%; }
.property-table th:nth-child(4),
.property-table td:nth-child(4) { width: 8%; }
.property-table th:nth-child(5),
.property-table td:nth-child(5) { width: 8%; }
.property-table th:nth-child(6),
.property-table td:nth-child(6) { width: 10%; }
.property-table th:nth-child(7),
.property-table td:nth-child(7) { width: 10%; }
.property-table th:nth-child(8),
.property-table td:nth-child(8) { width: 14%; }
.property-table th:nth-child(9),
.property-table td:nth-child(9) { width: 14%; }

    </style>
</head>
<body class="bg-white text-gray-800 p-6">
@php
    // Divide las propiedades en bloques de 4
    $propertyChunks = collect($enrichedProperties)->chunk(4);
@endphp

@foreach ($propertyChunks as $chunk)
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

<table style="width: 100%; border-spacing: 16px; margin-bottom: 2rem;">
    <tr>
        @foreach ($chunk as $property)
            <td style="width: 25%; vertical-align: top;">
                <div style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 12px; box-shadow: 2px 2px 4px rgba(0,0,0,0.1); font-size: 12px;">
                    <h2 style="font-size: 14px; font-weight: bold; color: #374151;">
                        {{ $property['subtype_name'] ?? 'Propiedad sin título' }}
                    </h2>

                    <div style="background-color: #DBEAFE; color: #2563EB; font-size: 12px; padding: 2px 6px; border-radius: 4px; display: inline-block; margin: 4px 0;">
                        {{ $property['status'] ?? 'listing sin estado' }}
                    </div>

                    @if(!empty($property['multimedias']) && isset($property['multimedias'][0]['link']))
                        <img src="file://{{ $property['multimedias'][0]['link'] }}" alt="Imagen" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin: 8px 0;">
                    @else
                        <img src="file://{{ public_path('storage/default.gif') }}" alt="Imagen por defecto" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin: 8px 0;">
                    @endif
                    <p><strong>Ubicación:</strong> {{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</p>
                    <p><strong>MLSID:</strong> {{ $property['MLSID'] ?? 'N/A' }}</p>
                    <p><strong>Precio de captación:</strong> US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</p>
                    <p><strong>Dormitorios:</strong> {{ $property['listing_information']['number_bedrooms'] ?? 'N/A' }}</p>
                    <p><strong>Baños:</strong> {{ $property['listing_information']['number_bathrooms'] ?? 'N/A' }}</p>
                    <p><strong>Área:</strong> {{ $property['listing_information']['total_area'] ?? 'N/A' }} m²</p>
                    <p><strong>Días en el mercado:</strong> {{ $property['days_in_market'] ?? 'N/A' }}</p>

                    @if (!empty($property['features']))
                        <p><strong>Características:</strong></p>
                        <ul style="padding-left: 1rem; margin: 0;">
                            @foreach ($property['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </td>

            @if (($loop->iteration % 4) == 0)
                </tr><tr>
            @endif
        @endforeach
    </tr>
</table>

<div style="page-break-after: always;"></div>
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
                    <tr>
                        <td><strong>Ubicación Oficina:</strong></td>
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



   <!-- Propiedades en el mercado > 90 días -->
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
                        <td>US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</td>
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
                <td>US$ {{ $summary['Precio captacion'] ?? 'N/A' }}</td>
                <td>US$ {{ $summary['Precio venta'] ?? 'N/A' }}</td>
                <td>US$ {{ $summary['Precio/M²'] ?? 'N/A' }}</td>
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
                <p class="subtitle">Gráfica de Días en el Mercado</p>
                <hr class="divider">
            </td>
            <td class="header-right">
                <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="logo-img">
            </td>
        </tr>
    </table>
</section>

	<div class="w-full" id="chart" >
	</div>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Obtener los datos de Laravel pasados a la vista
			var properties = @json($enrichedProperties);

			if (!Array.isArray(properties) || properties.length === 0) {
				console.warn("⚠️ No hay datos disponibles.");
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

