<?php

namespace App\Services\Migrations;

use App\Services\Agents\AgentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IconnectService
{

	protected $agentService;

	public function __construct(AgentService $agentService)
	{
		$this->agentService = $agentService;
	}

	public function getAuthToken()
	{
		try {
			$clientSecret = config('services.iconnect.client_secret');
			$clientID = config('services.iconnect.client_id');
			$apiUrl = config('services.iconnect.client_url');

			Log::info('CLIENT SECRET: ' . $clientSecret);
			Log::info('CLIENT ID: ' . $clientID);
			Log::info('API URL: ' . $apiUrl);

			$response = Http::post("$apiUrl/api/OAuth/GetToken", [
				'grant_type' => 'client_credentials',
				'client_id' => $clientID,
				'client_secret' => $clientSecret,
				'scope' => 'api'
			]);

			if ($response->status() === 200) {
				$token = $response->json()['access_token'];
				Log::info("Token obtenido: $token");
				return $token;
			} else {
				Log::warning('❌ No se pudo obtener token. Status: ' . $response->status());
				return false;
			}
		} catch (\Exception $e) {
			Log::error("❌ Error pidiendo token: " . $e->getMessage());
			throw $e;
		}
		// Js > Typescript :)
	}

	public function getContacts($token, $agent_internal_id)
	{

		$contactSkip = 0;
		$keepGoing = true;

		$contacts = [];

		do {
			$response = Http::withToken($token)->get(env('ICONNECT_API_URL') . '/odata/Agents(' . $agent_internal_id . ')/Contacts?$skip=' . $contactSkip);
			sleep(2);
			Log::info('RESPUESTA CORRECTA STATUS: ');
			Log::info($response->status());
			if ($response->status() == 200) {
				$contactsResponse = $response->json()['value'];
				$contactSkip += count($contactsResponse);
				$keepGoing = count($contactsResponse) == 100;
				$contacts = array_merge($contacts, $contactsResponse);
			} else {
				$keepGoing = false;
			}
		} while ($keepGoing);

		return $contacts;
	}

	public function migrateAgents($token)
	{
		$skip = 1400;
		$keepGoing = true;

		do {
			sleep(2);
			Log::info("******************$skip*******************");
			try {
				$response = Http::withToken($token)->timeout(300)->get(env('ICONNECT_API_URL') . '/odata/Agents?$skip=' . $skip);
			} catch (\Illuminate\Http\Client\RequestException $e) {
				Log::warning('Aumentando 100: ' . $e->getMessage());
				$skip += 100;
				continue;
			}
			Log::info('RESPUESTA CORRECTA STATUS: ');
			Log::info($response->status());
			if ($response->status() == 200) {
				$agentResponse = $response->json()['value'];
				$skip += count($agentResponse);
				$keepGoing = count($agentResponse) == 100;
				$this->agentService->migrateAgentImage($agentResponse);
			}
		} while ($keepGoing);
		Log::info('MIGRACION DE AGENTES FINALIZADA');
	}
}
