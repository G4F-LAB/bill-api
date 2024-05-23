<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckDuplicateApiRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:check-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicate API routes in the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return $route->domain() === null && strpos($route->uri(), 'api/') === 0;
        });

        $routesList = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = implode('|', $route->methods());
            $action = $route->getActionName();
            $routeKey = "$methods $uri";

            if (!isset($routesList[$routeKey])) {
                $routesList[$routeKey] = [];
            }
            $routesList[$routeKey][] = $action;
        }

        $duplicates = array_filter($routesList, function ($actions) {
            return count($actions) > 1;
        });

        if (empty($duplicates)) {
            $this->info('No duplicate API routes found.');
        } else {
            $this->warn('Duplicate API routes found:');
            foreach ($duplicates as $key => $actions) {
                $this->warn("Route: $key");
                foreach ($actions as $action) {
                    $this->line(" - " . $action);
                }
            }
        }

        return 0;
    }
}
