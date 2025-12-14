<?php

namespace App\Console\Commands;

use App\Application\User\CreateUser\CreateUserCommand;
use App\Application\User\CreateUser\CreateUserHandler;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

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

    public function __construct(private readonly CreateUserHandler $handler)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): int
    {
        try {
            $validated = $this->validateInput();

            if ($validated === null) {
                return self::FAILURE;
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $command = new CreateUserCommand(
            $validated['name'],
            $validated['email'],
            $validated['password']
        );

        try {
            $user = $this->handler->handle($command);

            $this->info("User " . $user->getEmail() . " created (id: " . $user->getId() . ").");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateInput(): ?array
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

            return null;
        }

        return $validator->validated();
    }
}
