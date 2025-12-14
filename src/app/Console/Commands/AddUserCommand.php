<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:add {name : Name of the user} {email : Email of the user} {password : Plain password for the user}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $validator = Validator::make([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => $this->argument('password'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $data = $validator->validated();

        $user = User::create($data);

        $this->info("User {$user->email} created (id: {$user->id}).");

        return self::SUCCESS;
    }
}
