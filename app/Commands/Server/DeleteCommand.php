<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class DeleteCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:delete {name? : The name of the server} {--no-backup : Don\'t backup the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers) = $this->getConfig();

        list($server, $serverName) = $this->getServer();

        if (! $this->confirm('Are you sure you want to delete this server?')) {
            $this->info('Canceling.');
            exit;
        }

        $path = $server['path'];

        if (! empty($this->getServerStatus()[$serverName])) {
            $this->warn('Server is being shutdown!');
            exec("screen -XS fivem-$serverName quit");
        }

        if (! $this->option('no-backup')) {
            $this->call('server:backup', ['name' => $serverName]);
        }

        exec("rm -rf $path");

        $this->info("$serverName server deleted!");

        unset($servers[$serverName]);

        $this->saveServers($servers);
    }
}
