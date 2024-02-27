<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            EmailMessagesSeeder::class,
            smsMessagesSeeder::class,
            PosSettingSeeder::class,
            PaymentMethodSeeder::class,
            CurrencySeeder::class,
            SettingSeeder::class,
            PermissionsSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PermissionRoleSeeder::class,
        ]);

        DB::table('brands')->insert(
            array(
                'id' => 1,
                'name' => 'defult',
                'image' => 'image_default.png',
            )

        );

        DB::table('categories')->insert(
            array(
                'id' => 1,
                'code' => 'defult',
                'name' => 'defult',
            )

        );

        DB::table('expense_categories')->insert(
            array(
                'id' => 1,
                'title' => 'Purchase',
            )

        );

        DB::table('units')->insert(
            array(
                'id' => 1,
                'name' => 'Pcs',
                'ShortName' => 'Pcs',
                'operator' => '*',
                'operator_value' => '1',
            )

        );

        DB::table('warehouses')->insert(
            array(
                'id' => 1,
                'name' => 'Warehouse 1',
            )

        );
    }
}
