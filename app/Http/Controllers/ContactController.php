<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Models\Agent;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\IconnectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContactController extends Controller
{
	protected $contactService;
	protected $iconectService;

	public function __construct(
		IconnectService $iconectService,
		ContactService $contactService,
	) {
		$this->iconectService = $iconectService;
		$this->contactService = $contactService;
	}

	public function index()
	{
		return Inertia::render('Contacts/index');
	}

	public function edit(Contact $contact)
	{

		return Inertia::render('Contacts/edit', [
			'contact_id' => $contact->id
		]);
	}

	public function indexLegacy(Request $request)
	{
		$search = $request->input('search');
		$user = auth()->user();
		// Log::info("Userr", ["user" => $user]);

		$agentId = null;
		if ($user &&  $userId = $user->id) {
			$agentId = Agent::where('user_id', $userId)->first()?->id ?? null;
			// Log::info("AgentId" . $agentId);
		}

		$contacts = Contact::select(
			'contacts.id',
			DB::raw('TRIM(CONCAT(COALESCE(contacts.name, ""), " ", COALESCE(contacts.last_name, ""))) as full_name'),
			// 'contacts.name',
			// 'contacts.last_name',
			'contacts.mobile',
			'contacts.email'
		)
			->leftJoin('agent_contact', 'agent_contact.contact_id', '=', 'contacts.id') // Pueden existir contactos sin agentes
			->when(isset($agentId), function ($query) use ($agentId) {
				$query->where('agent_contact.agent_id', $agentId);
			})
			->when($search, function ($query, $search) {
				// Log::info('Filtradon por' . $search);
				return $query->where('name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
					->orWhere('mobile', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
			->orderBy('contacts.name', 'asc')
			->limit(20)
			->get();

		return response()->json($contacts);
	}

	public function search(Request $request)
	{
		return response()->json(Contact::where('name', 'like', "%{$request->name}%")->get());
	}
	public function show($id)
	{
		return response()->json(Contact::where('id', $id)->first());
	}

	public function store(StoreContactRequest $request)
	{
		return Contact::create([
			'name' => $request->name,
			'last_name' => $request->last_name,
			'mobile' => $request->mobile,
			'email' => $request->email,
			'profile_type_id' => $request->profile_type_id,
		]);
	}
	public function update(UpdateContactRequest $request, $id)
	{
		$item = Contact::findOrFail($id);
		$item->update(
			[
				'name' => $request->name,
				'last_name' => $request->last_name,
				'company' => $request->company,
				'email' => $request->email,
				'mobile' => $request->mobile,
				'rating' => $request->rating,
				'greeting' => $request->greeting,
				'department_name' => $request->department_name,
				'identification_number' => $request->identification_number,
				'birth_day' => $request->birth_day,
				'sex' => $request->sex,
				'motivation' => $request->motivation,
				'children' => $request->children,
				'first_time_buyer' => $request->first_time_buyer,
				'buyer_commission' => $request->buyer_commission,
				'preferred_communication_method_id' => $request->preferred_communication_method_id,
				'agent_id' => $request->agent_id,
				'quick_note' => $request->quick_note,
				'display_name' => $request->display_name,
				'sell' => $request->sell,
				'stage_id' => $request->stage_id,
				'type_buyer_commission' => $request->type_buyer_commission,
				'home_phone' => $request->home_phone,
				'salutation' => $request->salutation,
				'preferred_language_id' => $request->preferred_language_id,
				'chat_telegram' => $request->chat_telegram,
				'chat_viber' => $request->chat_viber,
				'chat_messenger' => $request->chat_messenger,
				'chat_whatsapp' => $request->chat_whatsapp,
				'grade_id' => $request->grade_id,
				'gender_id' => $request->gender_id,
				'time_frame_id' => $request->time_frame_id,
				'birthday_reminder_id' => $request->birthday_reminder_id,
				'birthday_template_id' => $request->birthday_template_id,
				'fax' => $request->fax,
				'birth_day' => Carbon::parse($request->birth_day)->format('Y-m-d') ?? null,
				'prospect_id' => $request->prospect_id,
				'nationalitie_id' => $request->nationalitie_id,
				'title_id' => $request->title_id,
				'marital_statu_id' => $request->marital_statu_id,
				'red_facebook' => $request->red_facebook,
				'red_twitter' => $request->red_twitter,
				'red_youtube' => $request->red_youtube,
				'red_linkedin' => $request->red_linkedin,
				'red_instagram' => $request->red_instagram,

			]
		);
		return response()->json($item);
	}

	public function migrateContactsGyAPI()
	{

		set_time_limit(0);

		$token = $this->iconectService->getAuthToken();
		$agents = Agent::where('agent_internal_id', '!=', null)
			->where('id', '>', 3830)->get();

		foreach ($agents as $agent) {
			sleep(2);
			Log::info('Migrating contacts for agent: ' . $agent->id);
			$apiContacts = $this->iconectService->getContacts($token, $agent->agent_internal_id);
			$this->contactService->migrateContacts($apiContacts, $agent->id);
		}
	}

	public function create(Request $request) {}
}
