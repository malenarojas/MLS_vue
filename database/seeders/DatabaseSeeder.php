<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
        /**
         * Seed the application's database.
         */
        public function run(): void
        {
                // $this->call(LanguageSeeder::class);

                // $this->call(OfficeSeeder::class);
                // $this->call(UserTypesTableSeeder::class);
                // $this->call(RemaxTitlesTableSeeder::class,);
                // $this->call(RemaxTitleToShowsTableSeeder::class,);
                // $this->call(CustomerPreferencesTableSeeder::class,);
                //  $this->call(TeamStatusesTableSeeder::class,);

                // PERMISOS Y RELACIONADOS, NO BORRAR

                //$this->call(RegionSeeder::class);
                // $this->call(UsersSeeder::class);
                // $this->call(RolesSeeder::class);
                //  $this->call(PermissionsSeeder::class);
                // $this->call(TransactionPermissionSeeder::class);

                // NECESARIOS PARA MIGRACIONES DE DB, NO BORRAR

                // $this->call(AreaSeeder::class);
                // $this->call(SpecialitiesTableSeeder::class);
                // $this->call(AreaSpecialitySeeder::class);
                // $this->call(AchievementsSeeder::class,);
                // $this->call(CustomerPreferenceSeeder::class);
                // $this->call(UserTypeSeeder::class);
                // $this->call(RemaxTitleSeeder::class);
                // $this->call(RemaxTitleToShowSeeder::class);
                // $this->call(TeamStatusSeeder::class);
                // $this->call(ContractTypeSeeder::class);
                // $this->call(StatusListingSeeder::class);
                // $this->call(TransactionTypeSeeder::class);
                // $this->call(ListingTransactionTypeSeeder::class);
                // $this->call(CurrencySeeder::class);
                // $this->call(MarketStatusSeeder::class);
                // $this->call(TypePropertySeeder::class);
                // $this->call(SubtypePropertySeeder::class);
                // $this->call(LandUseSeeder::class);
                // $this->call(LandCategorySeeder::class);
                // $this->call(ParkingTypeSeeder::class);
                // $this->call(PropertyCategorySeeder::class);
                // $this->call(StatePropertySeeder::class);
                // $this->call(PriceTypesSeeder::class);
                // $this->call(BankSeeder::class);
                // $this->call(CommissionTypeSeeder::class);
                // $this->call(DocumentationTypeSeeder::class);
                // $this->call(TransactionStatusSeeder::class);
                // $this->call(FeaturesSeeder::class);
                // $this->call(TypeFloorSeeder::class);
                // $this->call(MultimediaTypeSeeder::class);
                // $this->call(LanguageSeeder::class);

                // $this->call(AgentSeeder::class);
                // $this->call(QualificationSeeder::class,);
                // $this->call(SocialNetworkSeeder::class,);
                // $this->call(GoalsTableSeeder::class);

                //  $this->call(UsersSeeder::class);
                // $this->call(AgentStatuSeeder::class);
                //$this->call(ListingPermissionSeeder::class);
                //$this->call(ListingQualityControlSeeder::class);
               // $this->call(MenuItemSeeder::class);
                //$this->call(RolesPermission::class);

                 $this->call(AgentStatuSeeder::class);
                // $this->call(DocumentacionTypeAgentSeeder::class);
                 $this->call(AdminMenuItemSeeder::class);
        }
}
