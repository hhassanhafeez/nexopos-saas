<?php

namespace App\Jobs;

use App\Services\SetupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\Tenant;

class ProvisionNexoPOSTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public Tenant $tenant) {}

    public function handle(): void
    {
        $tenantDbName = config('tenancy.database.prefix') . $this->tenant->getTenantKey();
        $centralDbName = config('database.connections.mysql.database');

        // Switch 'mysql' connection to the tenant's database so NexoPOS's
        // Artisan::call('migrate') and internal migration runner use it
        $this->switchConnection($tenantDbName);

        try {
            /** @var SetupService $service */
            $service = app(SetupService::class);

            $service->runMigration([
                'admin_username' => $this->tenant->admin_username,
                'admin_email'    => $this->tenant->admin_email,
                'password'       => $this->tenant->admin_password,
                'ns_store_name'  => $this->tenant->business_name,
                'language'       => 'en',
            ]);

            // Set default currency to PKR for every new tenant
            $currencyOptions = [
                'ns_currency_symbol'             => 'Rs',
                'ns_currency_iso'                => 'PKR',
                'ns_currency_position'           => 'before',
                'ns_currency_prefered'           => 'symbol',
                'ns_currency_thousand_separator' => ',',
                'ns_currency_decimal_separator'  => '.',
                'ns_currency_precision'          => '0',
            ];
            foreach ($currencyOptions as $key => $value) {
                ns()->option->set($key, $value);
            }

            // Clear sensitive provisioning data
            $this->switchConnection($centralDbName);
            $this->tenant->admin_password = null;
            $this->tenant->save();

        } catch (\Throwable $e) {
            $this->switchConnection($centralDbName);
            throw $e;
        }
    }

    private function switchConnection(string $database): void
    {
        config(['database.connections.mysql.database' => $database]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }
}
