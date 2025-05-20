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
<body class="bg-white text-gray-800 p-6">
@php
    // Divide las propiedades en bloques de 4
    $propertyChunks = collect($enrichedProperties)->chunk(4);
@endphp

@foreach ($propertyChunks as $chunk)
    <!-- Encabezado principal -->
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

    <!-- Listado de Propiedades -->
    <section class="grid grid-cols-4 gap-6">
        @foreach ($chunk as $property)
        <div class="border border-gray-300 rounded-lg p-4 shadow max-w-sm overflow-hidden max-w-[500px]">
            <!-- Encabezado de propiedad -->
			<div class="flex flex-col md:flex-row justify-between items-start mb-4">
				<h2
				  class="text-md font-semibold text-gray-700 break-words whitespace-normal"
				>
				  {{ $property['subtype_name'] ?? 'Propiedad sin título' }}
				</h2>
				<span
				  class="bg-blue-100 text-blue-600 text-sm px-2 py-1 rounded break-words whitespace-normal w-full md:w-auto mt-2 md:mt-0"
				>
				  {{ $property['status'] ?? 'listing sin estado' }}
				</span>
			  </div>


            <!-- Imagen -->
            <div class="flex justify-center mb-4 max-w-[500px]">
                @if(!empty($property['multimedias']) && isset($property['multimedias'][0]['link']))
                    <img src="{{ public_path('storage/' . $property['multimedias'][0]['link']) }}"
                        alt="Imagen de la propiedad"
                        class="w-full h-40 object-cover rounded">
                @else
                    <img src="{{ public_path('storage/default.gif') }}"
                        alt="Imagen por defecto"
                        class="w-full h-40 object-cover rounded">
                @endif
            </div>



            <!-- Detalles -->
            <div>
                <p class="text-sm text-gray-600 mb-2"><strong>Ubicación:</strong> {{ $property['location']['city'] ?? 'N/A' }}, {{ $property['location']['zone'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>MLSID:</strong> {{ $property['MLSID'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Precio de captación:</strong> US$ {{ $property['prices'][0]['amount'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Dormitorios:</strong> {{ $property['listing_information']['number_bedrooms'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Baños:</strong> {{ $property['listing_information']['number_bathrooms'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Área:</strong> {{ $property['listing_information']['total_area'] ?? 'N/A' }} m²</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Días en el mercado:</strong> {{ $property['days_in_market'] ?? 'N/A' }}</p>
            </div>

            <!-- Características -->
            <div class="mt-4">
                <h3 class="text-sm font-semibold mb-2">Características:</h3>
                <ul class="list-disc list-inside text-sm text-gray-600">
                    @foreach ($property['features'] as $feature)
                    <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </section>

    <!-- Pie de página -->
    <footer class="text-sm text-gray-500 mt-8 text-center">
        <p>Generado por RE/MAX Bolivia</p>
    </footer>

    <!-- Salto de página -->
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

