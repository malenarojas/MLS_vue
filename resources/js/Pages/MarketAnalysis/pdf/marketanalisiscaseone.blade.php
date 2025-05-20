<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Analysis Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-8">
    @foreach ($enrichedProperties as $property)
    <header class="flex justify-between items-end mb-6">
        <div class="flex-grow">
            <h1 class="text-3xl font-bold">
                <span class="text-red-500">RE</span><span class="text-blue-500">/</span><span class="text-red-500">MAX</span>
            </h1>
            <p class="text-lg font-bold text-gray-600">Análisis comparativo de mercado</p>
            <hr class="mt-2 border-gray-300">
        </div>
        <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="h-16">
    </header>


    <!-- Detalles del listado -->
    <section class="mb-8">
        <div class="border border-gray-300  rounded-lg p-6 shadow-s">
            <!-- Título y Precio -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-red-500">{{ $property['subtype_name'] ?? 'Propiedad sin título' }}</h2>
                <p class="text-sm text-gray-600">MLSID: {{ $property['MLSID'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">Ubicación: {{ $property['location']['city'] ?? 'Ciudad no especificada' }}, {{ $property['location']['zone'] ?? 'Zona no especificada' }}</p>
                <h3 class="text-lg text-green-500 font-bold mt-2">US$ {{ $property['prices'][0]['amount'] ?? 'Sin precio' }}</h3>
            </div>

            <!-- Galería de imágenes -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                @forelse ($property['multimedias'] ?? [] as $media)
                    <img src="{{ public_path('storage/' . $media['link']) }}" alt="Imagen de la propiedad" class="object-cover w-full h-48 rounded-lg shadow">
                @empty
                    <img src="{{ public_path('storage/default.gif') }}" alt="Imagen por defecto" class="object-cover w-full h-48 rounded-lg shadow">
                @endforelse
            </div>


            <!-- Características -->
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div>
                    <span class="font-semibold text-gray-800">Dormitorios:</span> {{ $property['listing_information']['number_bedrooms'] ?? 'N/A' }}
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Baños:</span> {{ $property['listing_information']['number_bathrooms'] ?? 'N/A' }}
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Área Total:</span> {{ $property['listing_information']['total_area'] ?? 'N/A' }} m²
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Precio por m²:</span> {{ $property['usd_per_m2'] ?? 'N/A' }}/m²
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Año de Construcción:</span> {{ $property['year_built'] ?? 'N/A' }}
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Días en el mercado:</span> {{ $property['days_in_market'] ?? 'N/A' }}
                </div>
            </div>

            <!-- Etiquetas de características -->
            <div class="flex space-x-2 mt-4">
                @foreach ($property['features'] as $feature)
                    <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs rounded">{{ $feature }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pie de página -->
    <footer class="text-sm text-gray-500 mt-8 text-center">
        <p>Generado por RE/MAX Bolivia</p>
    </footer>

    <div class="page-break"></div>
    @endforeach

    <!-- Propiedades en el mercado <= 90 días -->
    <section class="mb-8">
        <header class="flex justify-between items-end mb-6">
            <div class="flex-grow">
                <h1 class="text-3xl font-bold">
                    <span class="text-red-500">RE</span><span class="text-blue-500">/</span><span class="text-red-500">MAX</span>
                </h1>
                <p class="text-lg font-bold text-gray-600">Análisis comparativo de mercado</p>
                <hr class="mt-2 border-gray-300">
            </div>
            <img src="{{ public_path('storage/logo.jpeg') }}" alt="RE/MAX Logo" class="h-16">
        </header>
        <div class="flex items-center bg-gradient-to-r from-blue-100 via-white to-red-100 p-6 rounded-lg shadow-s">
            <!-- Imagen del agente -->
            <div class="flex-shrink-0">
                <img src="{{ public_path('storage/Agents/agent-defaul.gif') }}" alt="Foto del Agente" class="w-24 h-24 rounded-full border-4 border-white shadow-md">
            </div>

            <!-- Información del agente -->
            <div class="ml-6">
                <h3 class="text-2xl font-semibold text-gray-800">{{ $loggedInAgentData['name'] ?? 'Sin Nombre' }}</h3>
                <p class="text-sm text-gray-600 font-medium">RE/MAX Bolivia</p>

                <div class="mt-2">
                    <p class="text-sm">
                        <span class="font-bold text-gray-700">Email:</span> {{ $loggedInAgentData['email'] ?? 'N/A' }}
                    </p>
                    <p class="text-sm">
                        <span class="font-bold text-gray-700">Teléfono:</span> {{ $loggedInAgentData['phone_number'] ?? 'N/A' }}
                    </p>
                    <p class="text-sm">
                        <span class="font-bold text-gray-700">Oficina:</span> {{ $loggedInAgentData['office_name'] ?? 'N/A' }}
                    </p>
                    <p class="text-sm">
                        <span class="font-bold text-gray-700">Ubicación Oficina:</span>
                        {{ $loggedInAgentData['office_location']['city'] ?? 'N/A' }},
                        {{ $loggedInAgentData['office_location']['province'] ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold text-gray-700 mb-4">En el mercado (Menos de 90 días)</h2>
        <table class="w-full table-auto border-collapse border border-gray-300 mb-6">
            <thead>
                <tr class="bg-gray-100 text-gray-800 text-sm">
                    <th class="border p-3">#</th>
                    <th class="border p-3">Dirección</th>
                    <th class="border p-3">Propiedad</th>
                    <th class="border p-3">Dormitorios</th>
                    <th class="border p-3">Baños</th>
                    <th class="border p-3">SqM</th>
                    <th class="border p-3">Precio/SqM</th>
                    <th class="border p-3">Precio</th>
                    <th class="border p-3">DoM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrichedProperties as $key => $property)
                    @if ($property['days_in_market'] <= 90)
                        <tr class="hover:bg-gray-100 text-gray-600 text-sm">
                            <td class="border p-3">{{ $loop->iteration }}</td>
                            <td class="border p-3">{{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</td>
                            <td class="border p-3">{{ $property['subtype_name'] ?? 'N/A' }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['number_bedrooms'] ?? 0 }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['number_bathrooms'] ?? 0 }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['total_area'] ?? 0 }} m²</td>
                            <td class="border p-3 text-center">{{ $property['usd_per_m2'] ?? 'N/A' }}/m²</td>
                            <td class="border p-3 text-center">US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</td>
                            <td class="border p-3 text-center">{{ $property['days_in_market'] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </section>

    <!-- Propiedades en el mercado > 90 días -->
    <section>
        <h2 class="text-lg font-bold text-gray-700 mb-4">En el mercado (Más de 90 días)</h2>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-gray-800 text-sm">
                    <th class="border p-3">#</th>
                    <th class="border p-3">Dirección</th>
                    <th class="border p-3">Propiedad</th>
                    <th class="border p-3">Dormitorios</th>
                    <th class="border p-3">Baños</th>
                    <th class="border p-3">SqM</th>
                    <th class="border p-3">Precio/SqM</th>
                    <th class="border p-3">Precio Captación</th>
                    <th class="border p-3">DoM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrichedProperties as $key => $property)
                    @if ($property['days_in_market'] > 90)
                        <tr class="hover:bg-gray-100 text-gray-600 text-sm">
                            <td class="border p-3">{{ $loop->iteration }}</td>
                            <td class="border p-3">{{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</td>
                            <td class="border p-3">{{ $property['subtype_name'] ?? 'N/A' }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['number_bedrooms'] ?? 0 }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['number_bathrooms'] ?? 0 }}</td>
                            <td class="border p-3 text-center">{{ $property['listing_information']['total_area'] ?? 0 }} m²</td>
                            <td class="border p-3 text-center">{{ $property['usd_per_m2'] ?? 'N/A' }}/m²</td>
                            <td class="border p-3 text-center">US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</td>
                            <td class="border p-3 text-center">{{ $property['days_in_market'] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </section>
    <footer class="text-sm text-gray-500 mt-12 text-center">
        <p>Generado por RE/MAX Bolivia</p>
    </footer>
	<section class="mb-8">
		<header class="flex justify-between items-end mb-6">
			<div class="flex-grow">
				<p class="text-lg font-bold text-gray-700">Promedios del Mercado</p>
				<hr class="mt-2 border-gray-300">
			</div>
		</header>

		<table class="w-full table-auto border-collapse border border-gray-300 mb-6">
			<thead>
				<tr class="bg-gray-100 text-gray-800 text-sm">
					<th class="border p-3">Días en el mercado</th>
					<th class="border p-3">Precio Captación</th>
					<th class="border p-3">Precio Venta</th>
					<th class="border p-3">Precio/M²</th>
				</tr>
			</thead>
			<tbody>
				<tr class="hover:bg-gray-100 text-gray-600 text-sm">
					<td class="border p-3 text-center">{{ $summary['Días en el mercado'] ?? 'N/A' }}</td>
					<td class="border p-3 text-center">US$ {{ $summary['Precio captacion'] ?? 'N/A' }}</td>
					<td class="border p-3 text-center">US$ {{ $summary['Precio venta'] ?? 'N/A' }}</td>
					<td class="border p-3 text-center">US$ {{ $summary['Precio/M²'] ?? 'N/A' }}</td>
				</tr>
			</tbody>
		</table>
	</section>

    <div class="page-break"></div>
    <section class="mb-8">
        <header class="flex justify-between items-end mb-6">
            <div class="flex-grow">
                <h1 class="text-3xl font-bold">
                    <span class="text-red-500">RE</span>
                    <span class="text-blue-500">/</span>
                    <span class="text-red-500">MAX</span>
                </h1>
                <p class="text-lg font-bold text-gray-700">Gráfica de Días en el Mercado</p>
                <hr class="mt-2 border-gray-300">
            </div>
        </header>
    </section>
	<div class="w-full" id="chart" >
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
