<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Services\Hashing\Auth\ApiKey\ApiKeyGeneratorInterface;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API key';

    public function __construct(private ApiKeyGeneratorInterface $generator)
    {
        parent::__construct();
    }

    public function handle()
    {
        $key = $this->generator->generateKey();

        $this->info(sprintf(
            'New key generated: %s',
            $key,
        ));
    }
}
