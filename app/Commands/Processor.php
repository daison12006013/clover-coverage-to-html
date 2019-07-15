<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Repositories\CloverXml;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Processor extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'process {--xml-path=} {--store-path=} {--config-path=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create an html based on clover xml file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!($xmlPath = $this->getXmlPath())) {
            $this->error('Provide the path of the xml!');

            return;
        }

        if (!($storePath = $this->getStorePath())) {
            $this->error('Provide the path of where to store the html!');

            return;
        }

        if ($configPath = $this->getConfigPath()) {
            $config = $this->parseConfig($configPath);
        } else {
            $config = $this->parseConfig();
        }

        try {
            CloverXml::html($config, $xmlPath, $this->getStorePath());
        } catch (FileNotFoundException $e) {
            $this->error($e->getMessage());
        } catch (\Throwable $e) {
            throw $e;
        }

        $this->info('Successfully created an html!');
    }

    protected function getConfigPath()
    {
        return $this->option('config-path');
    }

    protected function getStorePath()
    {
        return $this->option('store-path');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function getXmlPath()
    {
        return $this->option('xml-path');
    }

    protected function parseConfig($path = null)
    {
        if ($path === null) {
            return [];
        }

        if (!file_exists($path)) {
            throw new FileNotFoundException("Config [$path] not found!");
        }

        if (strpos($path, '.json') !== false) {
            return json_decode(file_get_contents($path), true);
        }

        if (strpos($path, '.php') !== false) {
            return require($path);
        }

        throw new \RuntimeException('Config must be a json or php file!');
    }
}
