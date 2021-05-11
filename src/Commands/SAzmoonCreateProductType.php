<?php

namespace Larapress\SAzmoon\Commands;

use Illuminate\Console\Command;
use Larapress\ECommerce\Models\ProductType;

class SAzmoonCreateProductType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:sazmoon:create-pt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Simple Azmoon product types';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProductType::updateOrCreate([
            'name' => config('larapress.sazmoon.product_typename'),
            'author_id' => 1,
        ], [
            'flags' => 0,
            'data' => [
                "form" => [
                ],
                "title" => trans('larapress::sazmoon.product_type.title'),
                "agent" => "pages.vuetify.1.0"
            ]
        ]);
        $this->info("Done.");

        return 0;
    }
}
