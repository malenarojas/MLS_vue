<?php

namespace App\Services;

use App\Dtos\Agents\ContactDto;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactService
{
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Contact::paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Contact::all();
    }

    public function findById(string $id): Contact
    {
        return Contact::findOrFail($id);
    }

    public function create(ContactDto $dto): Contact
    {
        return DB::transaction(function () use ($dto) {
            return Contact::create($dto->toArray());
        });
    }

    public function update(string $id, ContactDto $dto): Contact
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->findById($id);
            $model->fill($dto->toArray());
            $model->save();
            return $model;
        });
    }

	public function migrateContacts($contacts, $agent_id)
	{
		$filePath = storage_path('contacts.json');

		if (file_exists($filePath)) {
			$existingData = file_get_contents($filePath);
			$contactsArray = json_decode($existingData, true) ?? [];
		}
		foreach ($contacts as $contact) {
			if ($contact['FirstName'] != null) {

				$mobile = null;
				$cellular = null;
				$home_phone = null;
				$other_phone = null;

				if ($contact['Communications'] && count($contact['Communications']) > 0) {
					foreach ($contact['Communications'] as $communication) {
						if ($communication['CommunicationTypeID'] == 5233) {
							$mobile = $communication['CommunicationValue'];
							$cellular = $mobile;
						}

						if ($communication['CommunicationTypeID'] == 616) {
							$home_phone = $communication['CommunicationValue'];
						}
						if ($communication['CommunicationTypeID'] == 609) {
							$other_phone = $communication['CommunicationValue'];
						}
					}
				}

				if(count($contact['Addresses']) > 1) {
					Log::info('Contacto con mas de 1 direccion');
					Log::info($contact);
				}

				$contactDto = new ContactDto(
					$contact['FirstName'],
					$contact['LastName'],
					null, //Email
					$mobile,
					$contact['Rating'], //Qualification
					$contact['Title'], //Greating (Title)
					$contact['Company'],
					null, //Department Name
					$contact['ContactId'],
					$contact['BirthDay'],
					null, // Sex
					null, // Nationality
					null, // Preferred Language
					null, // Motivation
					null, // Marital Status
					$contact['Chidrens'], // Children
					$contact['IsNew'], // First Time Buyer
					null, // Buyer Commission
					null, // Type Buyer Commission
					null, // Mail
					$home_phone,
					$cellular,
					$other_phone,
					null, // Grade ID
					null, // Time Frame ID
					null, // Preferred Communication Method ID
					null, // Category Contact ID
					$agent_id
				);
				$contactArray = $contactDto->toArray();

				Contact::create($contactArray);
				$contactsArray[] = $contactArray;
			}
		}

		$jsonContacts = json_encode($contactsArray, JSON_PRETTY_PRINT);
    	file_put_contents($filePath, $jsonContacts);
	}
}
