<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use App\Mail\SendPasswordToAgents as MailSendPasswordToAgents;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendPasswordToAgents extends Command
{
    private $words = [
        'apple', 'table', 'chair', 'light', 'cloud',
        'dream', 'stone', 'plant', 'train', 'glass',
        'river', 'smile', 'beach', 'heart', 'flame'
    ];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-password-to-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::
            where(function ($query){
                $query->whereDoesntHave('roles') 
                    ->orWhereHas('roles', function ($query) {
                        $query->where('name', 'Agente');
                    });
            })
            ->whereHas('agent', function ($query) {
                $query->where('agent_status_id', 1)
                    ->whereHas('office', function($query) {
                        $query->where('active_office', 1)
                            ->where(function($query) {
                                $query->whereNull('offices.is_external')
                                    ->orWhere('offices.is_external', 0);
                            });
                    });
            })
            
            ->get();

        echo("Total de usuarios: " . count($users) . "\n");
        foreach ($users as $user)
        {
            $user->syncRoles(['Agente']);
            
            $password = $this->generateCode();
            $user->password = bcrypt($password);
            $user->save();

            $url = "https://rmb.targetbit.com";
            
            $response = Http::post($url.'/api/change-user-password', [
                'user_id' => $user->id,
                'new_password' => $password,
            ]);

            if ($response->status() == 200) {
                SendEmail::dispatch($user->id, $password);
                echo "Contraseña actualizada para: [{$user->id}] {$user->name_to_show} {$user->agent->office->name}  {$password} \n";
            } else {
                echo "Failed to update password for user ID: {$user->id}. Error: {$response->body()}\n";
            }
        }
    }

    function generateCode(): string
    {
        $word = $this->words[array_rand($this->words)];
        $numLength = 8 - strlen($word);
        $numbers = '';
        for ($i = 0; $i < $numLength; $i++) {
            $numbers .= rand(0, 9);
        }
        return $word . $numbers;
    }
}
