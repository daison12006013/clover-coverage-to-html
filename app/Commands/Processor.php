<?php

namespace Daison\CloverToHtml\Commands;

use Daison\CloverToHtml\ConfigManager;
use Daison\CloverToHtml\Repositories\CloverManager;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;

class Processor extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'process
        {--xml-path=}
        {--store-path=}
        {--config-path=}
        {--vendor-autoload= : Provide the vendor/autoload.php}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create an html report based on clover xml file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->requireVendorPath();
        } catch (\Throwable $e) {
            $this->error('Provide the path of your project vendor/autoload.php!');

            return;
        }

        if (!($xmlPath = $this->getXmlPath())) {
            $this->error('Provide the path of the xml!');

            return;
        }

        if (!($storePath = $this->getStorePath())) {
            $this->error('Provide the path of where to store the html!');

            return;
        }

        if (!($config = $this->setupConfig($xmlPath, $storePath))) {
            $this->error('Provide the path of where the config!');

            return;
        }

        try {
            $manager = new CloverManager($xmlPath);

            $manager->setConfig($config);

            $manager->handle();
        } catch (FileNotFoundException $e) {
            $this->error($e->getMessage());
        } catch (\Throwable $e) {
            throw $e;
        }

        $this->info('Successfully created an html!');
    }

    protected function requireVendorPath()
    {
        $path = $this->option('vendor-autoload') ?? getcwd() . '/vendor/autoload.php';

        if (!$path) {
            throw new Exception('Provide the path of your project vendor/autoload.php!');
        }

        if (!file_exists($path)) {
            throw new Exception("Path [$path] to vendor's autoload does not exists!");
        }

        require $path;
    }

    protected function setupConfig($xmlPath, $storePath)
    {
        $path = $this->option('config-path');

        if (!file_exists($path)) {
            throw new RuntimeException("Config [$path] not found!");
        }

        $arr = [];

        if (strpos($path, '.json') !== false) {
            $arr = json_decode(file_get_contents($path), true);
        } elseif (strpos($path, '.php') !== false) {
            $arr = require $path;
        } else {
            throw new RuntimeException("Config must be [php or json] format!");
        }

        $manager = new ConfigManager($arr);
        $manager->set('xmlPath', $xmlPath);
        $manager->set('storePath', $storePath);

        return $manager;
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
            return require $path;
        }

        throw new \RuntimeException('Config must be a json or php file!');
    }
}
