@php
    $defaultImage = $listing->multimedias->first();
    $galleryImages = $listing->multimedias->slice(1)->take(5);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $listing->MLSID }}</title>
    <style>
        html, body {
            font-family: DejaVu Sans, sans-serif;
            color: #626262;
            margin: 0px;
            line-height: 1.6;
            background-color: #fff;
            font-size: 12px;
        }
        .container {
            /* max-width: 900px; */
            margin: 0;
            padding: 5px;
            /* border: 2px solid #ccc; */
            /* border-radius: 8px; */
            /* background-color: #fefefe; */
        }

        h1 {
            font-size: 20px;
        }

        h2 {
            font-size: 16px;
        }

        p, li {
            font-size: 12px;
            line-height: 1.5;
        }

        h1, h2, h3, h4 {
            margin: 0;
            padding: 0;
            color: #0056a3;
        }

        p {
            margin: 5px 0;
        }

        section {
            /* margin-top: 30px; */
        }

        section > div {
            /* margin-bottom: 10px; */
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-text,
        .header-logo {
            display: table-cell;
            vertical-align: top;
        }

        .header-text {
            width: 80%;
        }

        .header-text .price {
            font-size: 20px;
            color: #0056a3;
            margin: 0;
        }

        .header-text .subtitle {
            font-size: 13px;
            color: #333;
            margin-top: 4px;
        }

        .header-logo {
            text-align: right;
            width: 20%;
        }

        .header-logo img {
            height: 50px;
        }

        .main-image-wrapper {
            text-align: center;
            /* margin: 15px 0; */
            margin: 0;
        }

        .main-image {
            width: 500px;
            height: 350px;
            border: 1px solid #ccc;
        }

        .gallery-table {
            width: 100%;
            margin-top: 10px;
            text-align: center;
            border-spacing: 5px;
        }

        .gallery-cell img {
            width: 120px;
            height: 80px;
            border: 1px solid #ccc;
            display: block;
            margin: 0 auto;
        }

        .feature-badge {
            font-family: inherit;
            font-size: 12px;
            /* background-color: #2669a5; */
            /* color: white; */
            padding: 5px;
            border-radius: 20px;
            display: inline-block;
            margin: 5px 5px 0 0;
        }

        .agent-office-table {
            display: table;
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
            font-size: 12px;
        }

        .agent-cell,
        .office-cell {
            display: table-cell;
            vertical-align: top;
            padding: 10px;
            width: 50%;
        }

        .agent-info-table {
            width: 100%;
        }

        .agent-photo img {
            width: 100px;
            height: 140px;
            border: 1px solid #ccc;
        }

        .agent-text {
            padding-left: 10px;
            font-size: 12px;
        }

        .office-cell {
            border-left: 1px solid #ddd;
            padding-left: 20px;
            color: #555;
        }

        .property-details-wrapper {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 20px;
            margin-left: 5px;
        }

        .property-details-grid {
            width: 100%;
            font-size: 11px;
            margin-top: 15px;
            text-align: left;
        }

        .property-details-grid .item {
            page-break-inside: avoid;
            break-inside: avoid;
            display: inline-block;
            width: 28%;
            background-color: #e6eef7;
            padding: 6px 8px;
            margin: 1% 1% 10px 0;
            box-sizing: border-box;
            border-left: 4px solid #0056a3;
            border-radius: 3px;
            min-height: auto;
            color: #333;
        }

        .property-details-grid .row {
            display: table;
            width: 100%;
        }

        .property-details-grid .label,
        .property-details-grid .value {
            display: table-cell;
            vertical-align: middle;
            font-size: 11px;
        }

        .property-details-grid .label {
            font-weight: bold;
            color: #0056a3;
        }

        .property-details-grid .value {
            text-align: right;
            font-weight: normal;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
        }

        .disclaimer {
            background-color: #f1f1f1;
            font-size: 11px;
            color: #555;
            text-align: center;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 30px;
        }

        .description-wrapper {
            background-color: #f9f9f9;
            border-left: 4px solid #0056a3;
            padding: 15px;
            font-size: 11px;
            line-height: 1.2;
            color: #333;
            margin-top: 20px;
            border-radius: 4px;
        }

        .description-wrapper h3 {
            color: #0056a3;
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .description-wrapper strong {
            color: #0056a3;
            font-weight: bold;
        }

        .description-wrapper ul {
            padding-left: 10px;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .description-wrapper ul li {
            margin-bottom: 3px;
        }

        .listing-header {
            display: table;
            width: 100%;
            margin-top: 20px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        .listing-info {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }

        .listing-info h1 {
            font-size: 14px;
            color: #0056a3;
            margin-bottom: 5px;
        }

        .listing-info p {
            font-size: 11px;
            margin: 2px 0;
        }

        .listing-exclusiva {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .exclusiva-badge {
            background-color: #0f63ad;
            color: #fff;
            font-size: 10px;
            font-weight: 400;
            padding: 4px 5px;
            border-radius: 10px;
            text-transform: uppercase;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-text">
                <h2 class="price">{{ $formattedPrice }}</h2>
                <div class="subtitle">
                    {{ $listing?->listing_information?->subtype_property?->name }} - {{ $listing?->transaction_type?->name }}
                </div>
            </div>

            <div class="header-logo">
                <img src="{{ 'file://' . public_path('img/LogoMarcaAgua.png') }}" alt="Logo">
            </div>
        </div>

        {{-- Imagen principal --}}
        @if($defaultImage)
            <div class="main-image-wrapper">
                <img src="{{ 'file://' . public_path($defaultImage->url) }}" alt="Imagen principal" class="main-image">
            </div>
        @endif

        {{-- Galería de hasta 5 imágenes --}}
        @if($galleryImages->isNotEmpty())
            <table class="gallery-table">
                <tr>
                    @foreach($galleryImages as $image)
                        <td class="gallery-cell">
                            <img src="{{ 'file://' . public_path($image->url) }}" alt="Imagen galería">
                        </td>
                    @endforeach
                </tr>
            </table>
        @endif

        <section class="listing-header">
            <div class="listing-info">
                <h1>{{ $listing->title }}</h1>
                <p>{{ $listing->location?->full_address }}</p>
                <p>ID de la Captación:{{ $listing->MLSID }}</p>
            </div>

            <div class="listing-exclusiva">
                <span class="exclusiva-badge">RE/MAX Exclusiva</span>
            </div>
        </section>

        @if($listing->description_website)
            <section class="description-wrapper">
                <h3>Descripción del Inmueble</h3>
                <p>
                    {{ $listing->marketing_description }}
                </p>
            </section>
        @endif

        <section class="property-details-wrapper">
            <div class="property-details-grid">
                <div class="item">
                    <div class="row">
                        <div class="label">Habitaciones:</div>
                        <div class="value">{{ $listing->listing_information?->total_number_rooms ?? 0 }}</div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <div class="label">Dormitorios:</div>
                        <div class="value">{{ $listing->listing_information?->number_bedrooms ?? 0 }}</div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <div class="label">Baños:</div>
                        <div class="value">{{ $listing->listing_information?->number_bathrooms ?? 0 }}</div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <div class="label">Parqueos:</div>
                        <div class="value">{{ $listing->listing_information?->parking_slots ?? 0 }}</div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <div class="label">Área Total:</div>
                        <div class="value">{{ $listing->listing_information?->total_area ?? 0 }} m²</div>
                    </div>
                </div>
                @if($listing->listing_information?->year_construction)
                    <div class="item">
                        <div class="row">
                            <div class="label">Año Construcción:</div>
                            <div class="value">{{ \Carbon\Carbon::parse($listing->listing_information->year_construction)->year }}</div>
                        </div>
                    </div>
                @endif
                <div class="item">
                    <div class="row">
                        <div class="label">Área Construcción:</div>
                        <div class="value">{{ $listing->listing_information?->construction_area_m ?? 0 }} m²</div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <div class="label">Terreno:</div>
                        <div class="value">{{ $listing->listing_information->land_m2 ?? 0 }} m²</div>
                    </div>
                </div>
            </div>
        </section>

        @if($listing->features)
        <section>
            <h3>Características</h3>
            <div style="margin-top: 10px;">
                @foreach($listing->features as $feature)
                    <div class="feature-badge">
                        &#10003; {{ $feature->name }}
                    </div>
                @endforeach
            </div>
        </section> 
        @endif

        {{-- Agente y oficina --}}
        <section>
        @if($listing->agents && $listing->agents->isNotEmpty())
            <div class="agent-office-table">
                {{-- Columna Agente --}}
                <div class="agent-cell">
                    <table class="agent-info-table">
                        <tr>
                            <td class="agent-photo">
                                <img src="{{ 'file://' . public_path($listing->agents[0]->image_url) }}" alt="Agente">
                            </td>
                            <td class="agent-text">
                                <strong>{{ $listing->agents[0]->user->name_to_show }}</strong><br>
                                {{ $listing->agents[0]->user->email }}<br>
                                {{ $listing->agents[0]->user->phone_number }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Columna Oficina --}}
                @if($listing->agents[0]->office)
                    <div class="office-cell">
                        <strong>{{ $listing->agents[0]->office->name }}</strong><br>
                        <span>{{ $listing->agents[0]->office->full_address }}</span>
                    </div>
                @endif
            </div>
        @endif
        </section>

        <div class="disclaimer">
            Toda la información proporcionada por el agente o broker de bienes raíces se considera confiable, pero no está garantizada y debe ser verificada de forma independiente. No se ofrecen garantías ni representaciones de ningún tipo.
        </div>
    </div>
</body>
</html>
