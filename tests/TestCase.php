<?php

namespace Stryksta\ReferenceNumber\Test;

use File;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var \Stryksta\ReferenceNumber\Test\TestModel */
    protected $testModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->initializeDirectory($this->getTempDirectory());
        $this->setUpDatabase($this->app);
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory().'/database.sqlite',
            'prefix' => '',
        ]);
    }

    /**
     * @param  $app
     */
    protected function setUpDatabase(Application $app)
    {
        file_put_contents($this->getTempDirectory().'/database.sqlite', null);

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->softDeletes();
        });
    }

    protected function initializeDirectory(string $directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory);
        }

    }

    public function getTempDirectory() : string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'temp';
    }
}
