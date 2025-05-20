<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\PriceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptTranslationContactSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$spanish_id = Language::where('name', 'Español')->first()->id;
		$english_id = Language::where('name', 'English')->first()->id;
		
		$values =
			[
				//Gender
				['option_id' => 1, 'language_id' => $spanish_id,  'value' => 'Masculino', 'key_name' => 'male'],
				['option_id' => 1, 'language_id' => $spanish_id,  'value' => 'Femenino', 'key_name' => 'female'],
				['option_id' => 1, 'language_id' => $spanish_id,  'value' => 'No binario', 'key_name' => 'no_binary'],
				['option_id' => 1, 'language_id' => $english_id,  'value' => 'Male', 'key_name' => 'male'],
				['option_id' => 1, 'language_id' => $english_id,  'value' => 'Female', 'key_name' => 'female'],
				['option_id' => 1, 'language_id' => $english_id,  'value' => 'No Binary', 'key_name' => 'no_binary'],

				//Contact.profileType
				['option_id' => 40, 'language_id' => $spanish_id,  'value' => 'Comprador o inquilino', 'key_name' => 'buyer_tenant'],
				['option_id' => 40, 'language_id' => $english_id,  'value' => 'Buyer or tenant', 'key_name' => 'buyer_tenant'],
				['option_id' => 40, 'language_id' => $spanish_id,  'value' => 'Vendedor o Propietario', 'key_name' => 'seller_owner'],
				['option_id' => 40, 'language_id' => $english_id,  'value' => 'Seller or Owner', 'key_name' => 'seller_owner'],
				['option_id' => 40, 'language_id' => $spanish_id,  'value' => 'Otro', 'key_name' => 'other'],
				['option_id' => 40, 'language_id' => $english_id,  'value' => 'Other', 'key_name' => 'other'],

				//Contact.LeadSource
				['option_id' => 42, 'language_id' => $spanish_id,  'value' => 'Amigo', 'key_name' => 'Friend'],
				['option_id' => 42, 'language_id' => $english_id,  'value' => 'Friend', 'key_name' => 'Friend'],
				['option_id' => 42, 'language_id' => $spanish_id,  'value' => 'Familia', 'key_name' => 'Family'],
				['option_id' => 42, 'language_id' => $english_id,  'value' => 'Family', 'key_name' => 'Family'],
				['option_id' => 42, 'language_id' => $spanish_id,  'value' => 'Feria', 'key_name' => 'Fancy Fair'],
				['option_id' => 42, 'language_id' => $english_id,  'value' => 'Fancy Fair', 'key_name' => 'Fancy Fair'],
				['option_id' => 42, 'language_id' => $spanish_id,  'value' => 'Altavista', 'key_name' => 'Altavista'],
				['option_id' => 42, 'language_id' => $english_id,  'value' => 'Altavista', 'key_name' => 'Altavista'],
				['option_id' => 42, 'language_id' => $spanish_id,  'value' => 'Sitio del agente', 'key_name' => 'Agent Website'],
				['option_id' => 42, 'language_id' => $english_id,  'value' => 'Agent Website', 'key_name' => 'Agent Website'],

				//LeadStages
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Nuevas oportunidades', 'key_name' => 'New Opportunities'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'New Opportunities', 'key_name' => 'New Opportunities'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Para Seguimiento', 'key_name' => 'To Follow up'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'To Follow up', 'key_name' => 'To Follow up'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Buscando activamente', 'key_name' => 'Actively Pursuing'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'Actively Pursuing', 'key_name' => 'Actively Pursuing'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Bajo Contrato', 'key_name' => 'Under Contract'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'Under Contract', 'key_name' => 'Under Contract'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'En Ofertas/Propuestas', 'key_name' => 'Under Offers/Proposals'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'Under Offers/Proposals', 'key_name' => 'Under Offers/Proposals'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Ofertas cerradas', 'key_name' => 'Closed Deals'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'Closed Deals', 'key_name' => 'Closed Deals'],
				['option_id' => 124, 'language_id' => $spanish_id,  'value' => 'Archivado', 'key_name' => 'Archived'],
				['option_id' => 124, 'language_id' => $english_id,  'value' => 'Archived', 'key_name' => 'Archived'],

				//Contact.MaritalStatus
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Civil Union", "key_name" => "Civil Union"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Civil Union", "key_name" => "Civil Union"],
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Divorciado(a)", "key_name" => "Divorced"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Divorced", "key_name" => "Divorced"],
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Casado(a)", "key_name" => "Married"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Married", "key_name" => "Married"],
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Separado(a)", "key_name" => "Separated"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Separated", "key_name" => "Separated"],
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Solteto(a)", "key_name" => "Single"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Single", "key_name" => "Single"],
				["option_id" => 59, 'language_id' => $spanish_id, "value" => "Viudo(a)", "key_name" => "Widowed"],
				["option_id" => 59, 'language_id' => $english_id, "value" => "Widowed", "key_name" => "Widowed"],

				//Contact.Title
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Arq.", "key_name" => "Arq."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Arq.", "key_name" => "Arq."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Dr.", "key_name" => "Dr."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Dr.", "key_name" => "Dr."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Dra.", "key_name" => "Dra."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Dra.", "key_name" => "Dra."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Ing.", "key_name" => "Ing."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Ing.", "key_name" => "Ing."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Lic.", "key_name" => "Lic."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Lic.", "key_name" => "Lic."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Prof.", "key_name" => "Prof."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Prof.", "key_name" => "Prof."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Sr.", "key_name" => "Mr."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Mr.", "key_name" => "Mr."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Sra.", "key_name" => "Mrs."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Mrs.", "key_name" => "Mrs."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Sra.", "key_name" => "Mrs."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Mrs.", "key_name" => "Mrs."],
				["option_id" => 39, 'language_id' => $spanish_id, "value" => "Srta.", "key_name" => "Ms."],
				["option_id" => 39, 'language_id' => $english_id, "value" => "Ms.", "key_name" => "Ms."],

				//Birthday Reminder
				["option_id" => 103, 'language_id' => $spanish_id, "value" => "1 día antes", "key_name" => "1 day before"],
				["option_id" => 103, 'language_id' => $english_id, "value" => "1 day before", "key_name" => "1 day before"],
				["option_id" => 103, 'language_id' => $spanish_id, "value" => "3 dias antes", "key_name" => "3 days before"],
				["option_id" => 103, 'language_id' => $english_id, "value" => "3 days before", "key_name" => "3 days before"],
				["option_id" => 103, 'language_id' => $spanish_id, "value" => "7 dias antes", "key_name" => "7 days before"],
				["option_id" => 103, 'language_id' => $english_id, "value" => "7 days before", "key_name" => "7 days before"],

				//Contact.MotivationForTransaction
				["option_id" => 60, 'language_id' => $spanish_id, "value" => "Condiciones de vida deterioradas", "key_name" => "Deteriorated living conditions"],
				["option_id" => 60, 'language_id' => $english_id, "value" => "Deteriorated living conditions", "key_name" => "Deteriorated living conditions"],
				["option_id" => 60, 'language_id' => $spanish_id, "value" => "Cambio de empleo", "key_name" => "Employment change"],
				["option_id" => 60, 'language_id' => $english_id, "value" => "Employment change", "key_name" => "Employment change"],

				//CommissionType
				["option_id" => 140, 'language_id' => $spanish_id, "value" => "Monto Fijo", "key_name" => "Amount Fixed"],
				["option_id" => 140, 'language_id' => $english_id, "value" => "Amount Fixed", "key_name" => "Amount Fixed"],
				["option_id" => 140, 'language_id' => $spanish_id, "value" => "Porcentaje", "key_name" => "Percentage"],
				["option_id" => 140, 'language_id' => $english_id, "value" => "Percentage", "key_name" => "Percentage"],

				//AddressType
				["option_id" => 110, 'language_id' => $spanish_id, "value" => "Correo", "key_name" => "Mailing"],
				["option_id" => 110, 'language_id' => $english_id, "value" => "Mailing", "key_name" => "Mailing"],
				["option_id" => 110, 'language_id' => $spanish_id, "value" => "Casa", "key_name" => "Home"],
				["option_id" => 110, 'language_id' => $english_id, "value" => "Home", "key_name" => "Home"],

				//Listing.FloorType
				["option_id" => 11, 'language_id' => $spanish_id, "value" => "Sótano", "key_name" => "Basement"],
				["option_id" => 11, 'language_id' => $english_id, "value" => "Basement", "key_name" => "Basement"],
				["option_id" => 11, 'language_id' => $spanish_id, "value" => "Planta Baja", "key_name" => "Ground floor"],
				["option_id" => 11, 'language_id' => $english_id, "value" => "Ground floor", "key_name" => "Ground floor"],

				//Contact.PreferredCommMethod
				["option_id" => 41, 'language_id' => $spanish_id, "value" => "Email", "key_name" => "Email"],
				["option_id" => 41, 'language_id' => $english_id, "value" => "Email", "key_name" => "Email"],
				["option_id" => 41, 'language_id' => $spanish_id, "value" => "Facebook Messenger", "key_name" => "Facebook Messenger"],
				["option_id" => 41, 'language_id' => $english_id, "value" => "Facebook Messenger", "key_name" => "Facebook Messenger"],

				//Contact.TimeframeForTransaction
				["option_id" => 61, 'language_id' => $spanish_id, "value" => "1 a 3 meses", "key_name" => "1 to 3 Month"],
				["option_id" => 61, 'language_id' => $english_id, "value" => "1 to 3 Month", "key_name" => "1 to 3 Month"],
				["option_id" => 61, 'language_id' => $spanish_id, "value" => "Prospecto Frío – seguimiento en 3 meses", "key_name" => "> 1 Year"],
				["option_id" => 61, 'language_id' => $english_id, "value" => "> 1 Year", "key_name" => "> 1 Year"],

			];


		DB::table('option_translations')->insert($values);

		$this->command->info('Options seeded successfully.');
	}
}
