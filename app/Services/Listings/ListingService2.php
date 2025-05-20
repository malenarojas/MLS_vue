<?php

namespace App\Services\Listings;

use App\Dtos\Listings\ListingDto;
use App\Http\Resources\Listings\ListingResource;
use App\Models\Agent;
use App\Models\AgentMonthlyMeasure;
use App\Models\Buyer;
use App\Models\Language;
use App\Models\Listing;
use App\Models\ListingPrice;
use App\Models\Location;
use App\Models\Transaction;
use App\Services\Agents\AgentService;
use App\Services\Agents\OfficeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingService2
{
    public function __construct(
        private AgentService $agentService,
        private OfficeService $officeService,
        private StatusListingService $statusListingService,
        private ListingInformationService $listingInformationService,
        private CommissionOptionService $commissionOptionService,
    ) {}

    /**
     * @param int $perPage
     * @return Collection
     */
    public function getAllWithPagination(array $data, int $perPage = 20)
    {
        $listings = Listing::select(
            'id',
            'key',
            'MLSID',
            'date_of_listing',
            'contract_end_date',
            'transaction_type_id',
            'status_listing_id',
            'area_id',
            'contract_type_id'
        )->with([
            'listing_information' => function ($query) {
                $query->select(
                    'id',
                    'number_bedrooms',
                    'number_bathrooms',
                    'listing_id',
                    'subtype_property_id'
                )->with([
                    'subtype_property:id,name',
                ]);
            },
            'transaction_type:id,name',
            'status_listing:id,name',
            'area:id,name',
            'agents' => function ($query) {
                $query->select('agents.id', 'agents.office_id', 'agents.user_id')
                    ->with([
                        'office' => function ($query) {
                            $query->select('id', 'office_id', 'name');
                        },
                        'user' => function ($query) {
                            $query->select('id', 'name_to_show');
                        }
                    ]);
            },
            'location' => function ($query) {
                $query->select('id', 'show_addres_on_website', 'listing_id', 'city_id', 'zone_id')
                    ->with([
                        'zone' => function ($query) {
                            $query->select('id', 'name');
                        },
                        'city' => function ($query) {
                            $query->select('id', 'name', 'province_id')
                                ->with([
                                    'province' => function ($query) {
                                        $query->select('id', 'name');
                                    }
                                ]);
                        },
                    ]);
            },
            'default_imagen:id,link,listing_id',
            'contract_type:id,name'
        ])
            ->when(isset($data['agent_id']), function ($query) use ($data) {
                $query->whereHas('agents', function ($subQuery) use ($data) {
                    $subQuery->where('agents.id', $data['agent_id']);
                });
            })
            ->when(isset($data['office_id']), function ($query) use ($data) {
                $query->whereHas('agents', function ($subQuery) use ($data) {
                    $subQuery->where('office_id', $data['office_id']);
                });
            })
            ->when(isset($data['status_id']), function ($query) use ($data) {
                $query->where('status_listing_id', $data['status_id']);
            })
            ->when(isset($date['search']), function ($query) use ($data) {
                $query->where('MLSID', 'like', '%' . $data['search'] . '%');
            })
            ->paginate($perPage);

        // Calcular dias en el mercado
        $listings->getCollection()->transform(function ($listing) {
            $listing->days_in_market = $this->calculateDaysInMarket($listing);
            return $listing;
        });

        return $listings;
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        $listings = Listing::with([
            'listing_information',
            'transaction_type',
            'status_listing',
            'contract_type',
            'price_type',
            'project',
            'documentation',
            'addition_payments',
        ])->get();

        return $listings;
    }

    /**
     * @param string $id
     * @return Listing
     */
    public function findByKey(string $key)
    {
        $listing = Listing::where('key', $key)->load([
            'area',
            'addition_payments',
            'agent',
            'commission_option',
            'contract_type',
            'documentation',
            'listing_information' => function ($query) {
                $query->with([
                    'subtype_property.type_property',
                    'rooms' => function ($roomQuery) {
                        $roomQuery->select(
                            'id',
                            'description',
                            'size',
                            'dimension_x',
                            'dimension_y',
                            'room_type_id',
                            'information_id'
                        )->take(10);
                    },
                ]);
            },
            'price_type',
            'project',
            'status_listing.transitions_from',
            'transaction_type',
            'location' => function ($query) {
                $query->with([
                    'city.province.state',
                    'zone',
                ]);
            },
        ])->first();

        $translations = $listing->translations;

        if (!isset($translations) || empty($translations)) {
            // No tiene datos traducidos
            $languages = Language::all()->where('is_default', 0)->pluck('code')->toArray(); // Obtener los idiomas que no son el idioma por defecto
            $translationFiels = $listing->getTranslatableFields(); // Obtener los campos traducibles
            $translations = [];
            foreach ($languages as $language) {
                foreach ($translationFiels as $field) {
                    // Inicializar los campos traducibles con valores nulos
                    $translations[$language][$field] = null;
                }
            }
            $listing->translations = $translations;
        }

        return new ListingResource($listing);
    }

    /**
     * @param ListingDto $listingDto
     * @return Listing
     */
    // public function store(ListingDto $listingDto): Listing
    // {
    //     return DB::transaction(function () use ($listingDto) {
    //         $listing = Listing::create($listingDto->toArray());
    //         return $listing;
    //     });
    // }

    /**
     * @param string $id
     * @param ListingDto $listingDto
     * @return Listing
     */
    public function update(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $listing = Listing::find($id);
            $listing->fill($data);

            if (isset($data['commission_option']) && !empty($data['commission_option'])) {
                $commission = $data['commission_option'];
                $commission['listing_id'] = $listing->id;
                $this->commissionOptionService->createOrUpdate($commission);
            }

            if (isset($data['listing_information']) && !empty($data['listing_information'])) {
                $listingInformation = $data['listing_information'];
                $listingInformation['listing_id'] = $listing->id;
                $this->listingInformationService->createOrUpdate($listingInformation);
            }

            if (isset($data['price']) && $data['price'] != null) {
                $newPrice = $data['price'];

                $price = ListingPrice::where('listing_id', $listing->id)
                    ->where('currency_id', 2) // Default USD
                    ->first();

                if ($price && $newPrice['amount']) {
                    // Existe un precio anterior y el nuevo precio es diferente
                    $price->amount = $newPrice['amount'];
                    $price->save();
                } else if ($newPrice['amount']) {
                    ListingPrice::create([
                        'listing_id' => $listing->id,
                        'amount' => $newPrice['amount'],
                        'currency_id' => 2, // Default BOB
                    ]);
                }
            }
            if (isset($data['location']) && $data['location'] != null) {
                $newLocation = $data['location'];

                $location = Location::where('listing_id', $listing->id)->first();

                if (!$location) {
                    // No existe una ubicación anterior
                    $location = new Location();
                }

                $location->show_addres_on_website = true;
                $location->listing_id = $listing->id;
                $location->fill($newLocation);
                $location->save();
            }

            // Obtener los idiomas que no son el idioma por defecto
            // $languages = Language::all()->where('is_default', 0)->pluck('code')->toArray();

            // $translatableFields = $listing->getTranslatableFields();
            // $translations = $listing->translations ?? [];

            // // Actualizar las traducciones
            // foreach ($languages as $language) {
            //     foreach ($translatableFields as $field) {
            //         if (isset($data[$language][$field])) {
            //             $translations[$language][$field] = $data[$language][$field];
            //         }
            //     }
            // }

            // $listing->translations = $translations;

            return $listing->save();
        });
    }

    public function getDatosCaptaciones($data)
    {

        $captaciones = Listing::leftJoin('listings_information', 'listings.id', '=', 'listings_information.listing_id')
            ->leftJoin('subtype_properties', 'listings_information.subtype_property_id', '=', 'subtype_properties.id')
            ->leftJoin('type_properties', 'subtype_properties.type_property_id', '=', 'type_properties.id');

        if (
            isset($data['anios']) && !empty($data['anios']) &&
            isset($data['meses']) && !empty($data['meses'])
        ) {
            $captaciones->where(function ($query) use ($data) {
                foreach ($data['anios'] as $anio) {
                    foreach ($data['meses'] as $mes) {
                        $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                        $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                        $query->orWhereBetween('listings.first_upload_date', [$fechaInicio->format('Y-m-d'), $fechaFinal]);
                        // $query->orWhere(function ($query) use($fechaFinal) {
                        //     $query->where('listings.contract_end_date', '>=', $fechaFinal)
                        //     ->where('listings.date_of_listing', '<=', $fechaFinal);
                        //     // ->where(function ($query) use ($fechaInicio) {
                        //     //     $query->where('listings.date_of_listing', '<' $fechaFinal);
                        //     // });
                        // });
                    }
                }
            });
        }

        if ($data['type'] == 'venta') {
            $captaciones->where('listings.transaction_type_id', 1);
        }

        if ($data['type'] == 'alquiler') {
            $captaciones->where('listings.transaction_type_id', 2);
        }

        if ($data['type'] == 'anticretico') {
            $captaciones->where('listings.transaction_type_id', 3);
        }

        if (isset($data['type_id']) && $data['type_id'] != null) {
            $captaciones->where('type_property_id', $data['type_id'])
                ->selectRaw('
                COUNT(listings.id) as total,
                subtype_properties.id,
                subtype_properties.name
            ')->groupBy('subtype_properties.id');
        } else {
            $captaciones->selectRaw('
                COUNT(listings.id) as total,
                type_properties.id,
                type_properties.name
            ')->groupBy('type_properties.id');
        }

        $resultado = $captaciones->orderBy('total', 'DESC')->get();

        return $resultado;
    }

    //99% Ineficiencia, 1% Funcional

    // public function getCaptacionesActivas ($data) {
    //     $meses = $data['meses'];
    //     $anios = $data['anio'];
    //     $agent_id = $data['agent_id'];
    //     $office_id = $data['office_id'];

    //     $captacionesActivas = [];

    //     foreach ($anios as $anio) {
    //         foreach ($meses as $mes) {

    //             if($agent_id) {
    //                 $cierreEncontrado = AgentMonthlyMeasure::where('agent_id', $agent_id)
    //                 ->where('month', $mes)
    //                 ->where('year', $anio)
    //                 ->whereNot(function ($subQuery) {
    //                     $subQuery->whereNull('active_listings');
    //                 })
    //                 ->first();
    //             }

    //             $captacionesActivas[] = [
    //                 'mes' => $mes,
    //                 'anio' => $anio,
    //                 'captacionesActivas' => $agent_id && $cierreEncontrado ?
    //                     $cierreEncontrado->active_listings :
    //                     $this->registrarCierreMensual($mes, $anio, $agent_id, $office_id),
    //             ];
    //         }
    //     }

    //     usort($captacionesActivas, function ($a, $b) {
    //         return $a['mes'] <=> $b['mes'];
    //     });

    //     return $captacionesActivas;
    // }

    public function getCaptacionesActivas($data)
    {
        $meses = $data['meses'];
        $anios = $data['anio'];
        $agent_id = $data['agent_id'];
        $office_id = $data['office_id'];

        $captacionesActivas = [];

        foreach ($anios as $anio) {
            foreach ($meses as $mes) {

                $captacionesActivas[] = [
                    'mes' => $mes,
                    'anio' => $anio,
                    'captacionesActivas' =>
                    $this->getInventario([
                        'meses' => [$mes],
                        'anio' => [$anio],
                        'agent_id' => $agent_id,
                        'office_id' => $office_id,
                        'cantidad' => true,
                    ]),
                ];
            }
        }

        return $captacionesActivas;
    }

    private function registrarCierreMensual($mes, $anio, $agent_id, $office_id)
    {

        if ($agent_id) {
            $anioComienzo = Carbon::createFromFormat('Y-m-d', Agent::find($agent_id)->date_joined)->format('Y') < '2020' ? Carbon::createFromFormat('Y-m-d', Agent::find($agent_id)->date_joined)->format('Y') : '2010';
            $mesComienzo = Carbon::createFromFormat('Y-m-d', Agent::find($agent_id)->date_joined)->format('Y') < '2020' ?  Carbon::createFromFormat('Y-m-d', Agent::find($agent_id)->date_joined)->format('m') : '01';
        } else {
            $anioComienzo = '2010';
            $mesComienzo = '01';
        }

        $captacionesActivas = 0;

        for ($anioRecorredor = $anioComienzo; $anioRecorredor < $anio; $anioRecorredor++) {
            for ($mesRecorredor = $anioRecorredor == $anioComienzo ? $mesComienzo : 1; $mesRecorredor <= 12; $mesRecorredor++) {
                $this->actualizarCaptacionesActivas($mesRecorredor, $anioRecorredor, $agent_id, $office_id, $captacionesActivas);
            }
        }

        for ($mesRecorredor = 1; $mesRecorredor <= $mes; $mesRecorredor++) {
            $this->actualizarCaptacionesActivas($mesRecorredor, $anio, $agent_id, $office_id, $captacionesActivas);
        }

        return $captacionesActivas;
    }

    private function actualizarCaptacionesActivas($mes, $anio, $agent_id, $office_id, &$captacionesActivas)
    {
        if ($agent_id) {
            $registro = AgentMonthlyMeasure::where('agent_id', $agent_id)
                ->where('month', $mes)
                ->where('year',  $anio)
                ->first();
        }

        if (!$agent_id || !$registro) {
            $trafico = $this->getTraficoCaptaciones($mes, $anio, $agent_id, $office_id);
            $captacionesActivas += $trafico;

            $today = Carbon::today();
            if ($agent_id && $today->format('Y-m') != "{$anio} {$mes}") {
                AgentMonthlyMeasure::create([
                    'agent_id' => $agent_id,
                    'month' => $mes,
                    'year' => $anio,
                    'active_listings' => $captacionesActivas
                ]);
            }
        } else {
            $captacionesActivas = $registro->active_listings;
        }
    }

    private function getTraficoCaptaciones($mes, $anio, $agent_id, $office_id)
    {

        $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
        $fechaFinal = Carbon::today()->format('Y-m-d') > $fechaInicio->copy()->endOfMonth() ? $fechaInicio->copy()->endOfMonth()->toDateString() : Carbon::today()->format('Y-m-d');
        $lapso = [$fechaInicio->format('Y-m-d'), $fechaFinal];

        $queryCaptacionesNuevas = Listing::leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->whereBetween('listings.date_of_listing', $lapso);

        if (!$agent_id && !$office_id) {
            $queryCaptacionesNuevas->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
                ->where('agents.office_id', $office_id);
        } elseif ($agent_id) {
            $queryCaptacionesNuevas->where('agent_listing.agent_id', $agent_id);
        }

        $captacionesNuevas = count($queryCaptacionesNuevas->get());

        $queryCaptacionesFinalizadas = Listing::select('listings.id')->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->join('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->whereRaw('LEAST(
                IFNULL(listings.contract_end_date, "9999-12-31"),
                IFNULL(listings.cancellation_date, "9999-12-31"),
                IFNULL(transactions.sold_date, "9999-12-31")
                ) BETWEEN ? AND ?', [$lapso[0], $lapso[1]])
            ->groupBy('listings.id');

        if (!$agent_id && !$office_id) {
            $queryCaptacionesFinalizadas->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
                ->where('agents.office_id', $office_id);
        } elseif ($agent_id) {
            $queryCaptacionesFinalizadas->where('agent_listing.agent_id', $agent_id);
        }

        $captacionesFinalizadas = count($queryCaptacionesFinalizadas->get());

        return $captacionesNuevas - $captacionesFinalizadas;
    }

    public function test($data)
    {
        $meses = $data['meses'];
        $anios = $data['anio'];
        $agent_id = $data['agent_id'];
        $office_id = $data['office_id'];

        $query = Listing::leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->join('agent_listing', 'listings.id', '=', 'agent_listing.listing_id');

        if ($agent_id && $agent_id != [''] && $office_id != []) {
            $query->whereIn('agent_listing.agent_id', $agent_id);
        } elseif ($office_id && $office_id != [''] && $office_id != []) {
            $query->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
                ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                ->whereIn('offices.id', $office_id);
        }

        $query->where(function ($subQuery) use ($meses, $anios) {
            $subQuery->where(function () {
                return false;
            });
            foreach ($anios as $anio) {
                foreach ($meses as $mes) {

                    $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                    $fechaFinal = Carbon::today()->format('Y-m-d') > $fechaInicio->copy()->endOfMonth() ? $fechaInicio->copy()->endOfMonth()->toDateString() : Carbon::today()->format('Y-m-d');
                    $lapso = [$fechaInicio->format('Y-m-d'), $fechaFinal];

                    $subQuery->orWhere(function ($subSubQuery) use ($lapso) {
                        $subSubQuery->orWhereBetween('listings.date_of_listing', $lapso)
                            ->orWhereRaw('LEAST(
                            IFNULL(listings.contract_end_date, "9999-12-31"),
                            IFNULL(listings.cancellation_date, "9999-12-31"),
                            IFNULL(transactions.sold_date, "9999-12-31")
                            ) BETWEEN ? AND ?', [$lapso[0], $lapso[1]]);
                    })
                        ->orWhere(function ($subSubQuery) use ($lapso) {
                            $subSubQuery->where('listings.date_of_listing', '<=', $lapso[1])
                                ->whereRaw('LEAST(
                                IFNULL(listings.contract_end_date, "9999-12-31"),
                                IFNULL(listings.cancellation_date, "9999-12-31"),
                                IFNULL(transactions.sold_date, "9999-12-31")
                            ) > ?', $lapso[1]);
                        });
                }
            }
        });
        if (isset($data['cantidad']) && $data['cantidad'] == 'true') {
            $query->select('listings.id', 'listings.contract_end_date', 'listings.cancellation_date', 'transactions.sold_date')
                ->groupBy('listings.id', 'listings.contract_end_date', 'listings.cancellation_date', 'transactions.sold_date');
            $captacionesActivas = $query->get();
        } else {
            $query->selectRaW('SUM(listing_prices.amount) as amount');
            $captacionesActivas = $query->first();
        }


        return $captacionesActivas;
    }


    public function getInventario($data)
    {
        $meses = $data['meses'];
        $anios = $data['anio'];
        $agent_id = $data['agent_id'];
        $office_id = $data['office_id'];

        $query = Listing::leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->join('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->where(function ($subquery) {
                $subquery->whereNull('listing_prices.currency_id')
                    ->orWhere('listing_prices.currency_id', 2);
            });

        if ($agent_id && $agent_id != [''] && $office_id != []) {
            $query->whereIn('agent_listing.agent_id', $agent_id);
        } elseif ($office_id && $office_id != [''] && $office_id != []) {
            $query->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
                ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                ->whereIn('offices.id', $office_id);
        }

        $query->where(function ($subQuery) use ($meses, $anios) {

            foreach ($anios as $anio) {
                foreach ($meses as $mes) {

                    $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                    $fechaFinal = Carbon::today()->format('Y-m-d') > $fechaInicio->copy()->endOfMonth() ? $fechaInicio->copy()->endOfMonth()->toDateString() : Carbon::today()->format('Y-m-d');
                    $lapso = [$fechaInicio->format('Y-m-d'), $fechaFinal];

                    $subQuery->orWhere(function ($subSubQuery) use ($lapso) {
                        $subSubQuery->orWhereBetween('listings.date_of_listing', $lapso)
                            ->orWhereRaw('LEAST(
                            IFNULL(listings.contract_end_date, "9999-12-31"),
                            IFNULL(listings.cancellation_date, "9999-12-31"),
                            IFNULL(transactions.sold_date, "9999-12-31")
                            ) BETWEEN ? AND ?', [$lapso[0], $lapso[1]]);
                    })
                        ->orWhere(function ($subSubQuery) use ($lapso) {
                            $subSubQuery->where('listings.date_of_listing', '<=', $lapso[1])
                                ->whereRaw('LEAST(
                            IFNULL(listings.contract_end_date, "9999-12-31"),
                            IFNULL(listings.cancellation_date, "9999-12-31"),
                            IFNULL(transactions.sold_date, "9999-12-31")
                            ) > ?', $lapso[1]);
                        });
                }
            }
        });
        if (isset($data['cantidad']) && $data['cantidad'] == 'true') {
            $query->select('listings.id')
                ->groupBy('listings.id');
            $captacionesActivas = count($query->get());
        } else {

            $queryResult = $query->select('listings.id', 'listing_prices.amount')->get();

            $queryAmount = $queryResult->sum('amount');
            $queryQuantity = count($queryResult);

            $result = [
                'amount' => $queryAmount,
                'quantity' => $queryQuantity,
            ];

            return $result;
        }


        return $captacionesActivas;
    }

    public function getPrecioPromedio($data)
    {
        $mesLimite = $data['meses'][count($data['meses']) - 1];
        $anioLimite = $data['anio'][count($data['anio']) - 1];

        $fecha = Carbon::createFromFormat('Y-m-d', $anioLimite . '-' . str_pad($mesLimite, 2, '0', STR_PAD_LEFT) . '-01')->endOfMonth()->toDateString();

        $queryCaptacionesTotales = $this->getQueryCaptacionesTotales($data, $fecha);

        $captacionesTotales = count($queryCaptacionesTotales->get());
        $totalPrecioCaptaciones = $queryCaptacionesTotales->sum('listing_prices.amount');

        return $captacionesTotales > 0 ? $totalPrecioCaptaciones / $captacionesTotales : 0;
    }

    public function getTiempoPromedio($data)
    {
        $mesLimite = $data['meses'][count($data['meses']) - 1];
        $anioLimite = $data['anio'][count($data['anio']) - 1];

        $fecha = Carbon::createFromFormat('Y-m-d', $anioLimite . '-' . str_pad($mesLimite, 2, '0', STR_PAD_LEFT) . '-01')->endOfMonth()->toDateString();

        $queryCaptacionesTotales = $this->getQueryCaptacionesTotales($data, $fecha);

        $captacionesTotales = count($queryCaptacionesTotales->get());

        $queryTiemposEnElMercado = Listing::selectRaw(
            'SUM(DATEDIFF(
                LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ),
                listings.date_of_listing
            )) AS dias_en_mercado_total'
        )
            ->where('listings.date_of_listing', '<=', $fecha)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->whereRaw(
                'LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ) <= ?',
                $fecha
            )
            ->where(function ($subQuery) {
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.contract_end_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.cancellation_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('transactions.sold_date');
                });
            })
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id'] != [''] && $data['agent_id'] != []) {
            $queryTiemposEnElMercado->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id'] != [''] && $data['office_id'] != []) {
            $queryTiemposEnElMercado->whereIn('offices.id', $data['office_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryTiemposEnElMercado->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryTiemposEnElMercado->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryTiemposEnElMercado->whereIn('provinces.state_id', $data['states_id']);
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryTiemposEnElMercado->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryTiemposEnElMercado->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryTiemposEnElMercado->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        $tiempoEnElMErcadoTotal = $queryTiemposEnElMercado->value('dias_en_mercado_total');

        //Query para obtener las captaciones las cuales no se han finalizado aun
        $queryCaptacionesNoFinalizadas = Listing::selectRaw(
            'MAX(DATEDIFF(
                LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ),
                listings.date_of_listing
            )) AS dias_en_mercado_total,
            listings.id,
            MAX(LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                )) as fecha_final'
        )
            ->where('listings.date_of_listing', '<=', $fecha)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->whereRaw(
                'LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ) > ?',
                $fecha
            )
            ->where(function ($subQuery) {
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.contract_end_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.cancellation_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('transactions.sold_date');
                });
            })
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id']) {
            $queryCaptacionesNoFinalizadas->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id']) {
            $queryCaptacionesNoFinalizadas->whereIn('offices.id', $data['office_id']);
        }


        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesNoFinalizadas->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('provinces.state_id', $data['states_id']);
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        $captacionesNoFinalizadas = $queryCaptacionesNoFinalizadas->groupBy('listings.id')->get();

        foreach ($captacionesNoFinalizadas as $captacion) {
            $fechaFinal = Carbon::parse($captacion->fecha_final);
            $fecha = Carbon::parse($fecha);

            $diferencia = $fecha->diffInDays($fechaFinal);
            $tiempoEnElMErcadoTotal += $captacion->dias_en_mercado_total - $diferencia;
        }


        $tiempoPromedio = $captacionesTotales > 0 ? $tiempoEnElMErcadoTotal / $captacionesTotales : 0;

        return $tiempoPromedio;
    }

    private function getQueryCaptacionesTotales($data, $fechaLimite)
    {

        $queryCaptacionesTotales = Listing::select('listing_prices.amount')
            ->where('date_of_listing', '<=', $fechaLimite)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id'] != [''] && $data['agent_id'] != []) {
            $queryCaptacionesTotales->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id'] != [''] && $data['office_id'] != []) {
            $queryCaptacionesTotales->whereIn('offices.id', $data['office_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesTotales->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryCaptacionesTotales->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesTotales->whereIn('provinces.state_id', $data['states_id']);
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryCaptacionesTotales->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryCaptacionesTotales->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryCaptacionesTotales->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        return $queryCaptacionesTotales;
    }
    // Metodo especifico para el paso 6, ya que alli se manda el transaction_id
    public function updateBuyers($data)
    {
        $transaction = Transaction::where('internal_id', $data['transaction_id'])->first();

        $hasChanged = false;

        $contact_id_mapping = array_map(function ($contacto) {
            return $contacto['id'];
        }, $data['buyers']);

        $buyersToDelete = $transaction->listing->buyers->filter(function ($buyer) use ($contact_id_mapping) {
            return !in_array($buyer->id, $contact_id_mapping);
        });

        foreach ($buyersToDelete as $contact) {
            $hasChanged = true;
            $buyer = Buyer::where('contact_id', $contact->id)->first();
            $buyer->delete();
        }

        $actual_buyers_mapping_id = array_map(function ($contacto) {
            return $contacto['id'];
        }, $transaction->listing->buyers->toArray());

        foreach ($data['buyers'] as $buyer) {
            if ($buyer['id'] && !in_array($buyer['id'], $actual_buyers_mapping_id)) {
                $hasChanged = true;
                Buyer::create([
                    'listing_id' => $transaction->listing_id,
                    'contact_id' => $buyer['id']
                ]);
            }
        }

        if ($hasChanged && $transaction->transaction_status_id != 3) {
            $transaction->transaction_status_id = 4;
            $transaction->save();
        }

        return $contact_id_mapping;
    }

    // Calcular dias en el mercado (El modelo debe tener date_of_listing, status_listing_id, contract_end_date)
    private function calculateDaysInMarket($listing): int
    {
        if (empty($listing->date_of_listing)) {
            return 0;
        }

        $captationDate = Carbon::parse($listing->date_of_listing);

        switch ($listing->status_listing_id) {
            case 2:
                // Activa: desde date_of_listing hasta la fecha actual
                return $captationDate->diffInDays(Carbon::now());

            case 3:
                // Expirada: desde date_of_listing hasta contract_end_date
                if (!empty($listing->contract_end_date)) {
                    $endDate = Carbon::parse($listing->contract_end_date);
                    return $captationDate->diffInDays($endDate);
                }
                return 0;

            case 8:
                // Vendida: tomar sold_date desde la tabla transactions
                // (suponiendo que hay UNA transacción principal; si hay varias,
                //  decide si tomas la última o la primera)
                $soldDate = DB::table('transactions')
                    ->where('listing_id', $listing->id)
                    ->value('sold_date');

                if ($soldDate) {
                    return $captationDate->diffInDays(Carbon::parse($soldDate));
                }
                return 0;

            default:
                return 0;
        }
    }
}
