<?php

namespace gpibarra\TrackerSession;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use gpibarra\TrackerSession\Models\TrackerSession;
use gpibarra\TrackerSession\Exceptions\InvalidConfiguration;

class TrackerSessionServiceProvider extends ServiceProvider
{
    /**
     * The Authentication Log event / listener mappings.
     *
     * @var array
     */
    protected $events = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();

        $this->mergeConfigFrom(__DIR__.'/../config/tracker-session.php', 'tracker-session');

        if ($this->app->runningInConsole()) {
			$timestamp = date('Y_m_d_His', time());
			$this->publishes([
				__DIR__.'/../database/migrations/create_tracker_sessions_table.php' => $this->app->databasePath()."/migrations/{$timestamp}_create_tracker_session_table.php",
			], 'migrations');

			$this->publishes([
				__DIR__.'/../config/tracker-session.php' => config_path('tracker-session.php'),
			], 'config');

        }
    }

    /**
     * Register the Authentication Log's events.
     *
     * @return void
     */
    protected function registerEvents()
    {

        $this->events = [
            'Illuminate\Auth\Events\Login' => [
                'gpibarra\TrackerSession\Listeners\LogSuccessfulLogin',
            ],

            'Illuminate\Auth\Events\Logout' => [
                'gpibarra\TrackerSession\Listeners\LogSuccessfulLogout',
            ],

            'Illuminate\Auth\Events\Failed' => [
                'gpibarra\TrackerSession\Listeners\LogFailedLogin',
            ],
        ];

        if ($this->app->getProvider(PassportServiceProvider::class)) {
            $this->events['Laravel\Passport\Events\AccessTokenCreated'] = [
                'gpibarra\TrackerSession\Listeners\LogSuccessOAuthLogin',
            ];
        }

        $events = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ClearCommand::class,
            ]);
        }
    }

    public static function determineSessionModel() :string
    {
        $sessionModel = config('tracker-session.session_model') ?? TrackerSession::class;

        if (! is_a($sessionModel, TrackerSession::class, true)
            || ! is_a($sessionModel, Model::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($sessionModel);
        }

        return $sessionModel;
    }
}
