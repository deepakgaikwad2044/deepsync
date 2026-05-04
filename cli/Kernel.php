<?php

namespace CLI;

class Kernel
{
    protected array $commands = [
         'make:command'  => Commands\MakeCommand::class,
         'make:view'  => Commands\MakeView::class,
        'make:controller'  => Commands\MakeController::class,
        
        'make:model'       => Commands\MakeModel::class,
        'make:middleware'  => Commands\MakeMiddleware::class,
        'migrate:install'  => Commands\MigrateInstall::class,
        'seed:update' => \CLI\Commands\SeedUpdate::class,
        'make:migration'   => Commands\MakeMigration::class,
        'migrate'          => Commands\Migrate::class,
        'migrate:rollback' => Commands\Rollback::class,
        'make:event'       => Commands\MakeEvent::class,
        'make:channel'     => Commands\MakeChannel::class,
        'key:generate'     => Commands\KeyGenerateCommand::class,
        'app:key'          => Commands\AppKeyGenerateCommand::class,
        'migrate:status'   => Commands\MigrateStatus::class,
        'serve'            => Commands\Serve::class,
        'socket:serve'            => Commands\WSSocket::class,
        'redis:serve'            => Commands\Redis::class,
        'serve:status' => \CLI\Commands\ServeStatus::class,
    ];

    protected array $config = [];

    public function __construct()
    {
        $this->loadComposerConfig();
    }

    // Load composer.json config
    private function loadComposerConfig(): void
    {
        $path = __DIR__ . '/../composer.json';
        if (file_exists($path)) {
            $this->config = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    // Handle CLI input
    public function handle(array $argv): void
    {
        $command = $argv[1] ?? null;

        // Global commands
        if ($command === '-v' || $command === '--version') {
            $this->version();
            return;
        }

        if ($command === 'info') {
            $this->info();
            return;
        }

        // No command
        if (!$command) {
            $this->help();
            return;
        }

        // Command not found
        if (!isset($this->commands[$command])) {
            echo "❌ Command not found\n";
            $this->help();
            return;
        }

        // Run command
        $class = $this->commands[$command];
        $instance = new $class();
        $instance->handle($argv);
    }

    // Display version
    private function version(): void
    {
        $version = $this->config['version'] ?? '5.0.0';
       
        $date    = $this->config['extra']['release-date'] ?? '';
        echo "🚀 DeepSync Framework v$version" . ($date ? " ($date)" : "") . "\n";
    }

    // Display full info
    private function info(): void
    {
        $author = $this->config['authors'][0]['name'] ?? 'Unknown';
        $email  = $this->config['authors'][0]['email'] ?? '-';

        echo "\n🔥 DeepSync Framework\n\n";
        echo "📦 Name        : " . ($this->config['name'] ?? '-') . "\n";
        echo "📝 Description : " . ($this->config['description'] ?? '-') . "\n";
        echo "🚀 Version     : " . 
        ( 
      $this->config['version'] ??  '5.0.0') . "\n";
        echo "📜 License     : " . ($this->config['license'] ?? '-') . "\n";

        echo "\n👤 Author Info\n";
        echo "   Name  : $author\n";
        echo "   Email : $email\n";

        if (!empty($this->config['extra'])) {
            echo "\n⚙️ Framework Info\n";
            echo "   Current Version : " . ($this->config['extra']['current-version'] ?? '-') . "\n";
       
        }

        echo "\n";
    }

    // Display CLI help
    private function help(): void
    {
        echo "\n🔥 DeepSync CLI\n\n";
        echo "Usage:\n";
        echo "  php deep -v          Show version\n";
        echo "  php deep info        Show full info\n\n";
        echo "Available Commands:\n";

        foreach ($this->commands as $name => $class) {
            echo "  php deep $name\n";
        }

        echo "\n";
    }
}