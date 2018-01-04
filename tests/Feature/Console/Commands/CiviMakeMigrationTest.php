<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;
use Urbics\Civitools\Console\Commands\CiviMakeMigration;

class CiviMakeMigrationTest extends TestCase
{

    /**
     * Verifies civi:make:migration generates the expected number of files.
     *
     * @return void
     */
    public function testMigrationGeneratesCorrectNumberOfFiles()
    {
        $targetFileCount = 305;
        $testDir = 'TempCiviTest';
        $filePath = database_path("migrations/$testDir");
        $seederPath = database_path("seeds/$testDir");

        $application = new ConsoleApplication();
        $testedCommand = $this->app->make(CiviMakeMigration::class);
        $testedCommand->setLaravel(app());
        $application->add($testedCommand);
        $command =  $application->find('civi:make:migration');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--path' => $testDir,
        ]);
        $fileCount = 0;
        $dir = new \DirectoryIterator($filePath);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $fileCount++;
            }
        }

        // Remove directories before assertion
        if (file_exists($filePath)) {
            array_map('unlink', glob("$filePath/*.php"));
            rmdir($filePath);
        }
        if (file_exists($seederPath)) {
            array_map('unlink', glob("$seederPath/*.php"));
            rmdir($seederPath);
        }

        // CiviCRM currently (late 2017) has 152 tables.
        // Migration generates a table and a foreign key migration for each table,
        //  plus 1 function plus XX triggers, so 305 files should exist in the migration folder.
        $this->assertEquals($targetFileCount, $fileCount);
    }

}
