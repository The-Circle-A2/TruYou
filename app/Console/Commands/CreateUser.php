<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user';

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
        $username = $this->argument('username');

        $user = User::where('username', $username)
            ->first();

        if($user != null) {
            $this->error('Username already exists');
            return 0;
        }

        $user = new User();

        $private = RSA::createKey();
        $public = $private->getPublicKey();

        $user->username = $username;
        $user->public_key = $public;

        $user->save();

        $this->info('New user for ' . $username . ' created.');

        $this->info('Private key:');

        $this->info($private);

        $this->info('Public key:');

        $this->info($public);

        return 0;
    }
}
