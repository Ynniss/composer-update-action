<?php

namespace Tests\Feature;

use App\Facades\Git;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\File;
use Mockery as m;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testUpdateCommand()
    {
        GitHub::shouldReceive('authenticate')->once();

        Git::shouldReceive('setRemoteUrl')->once();
        Git::shouldReceive('execute')->twice();
        Git::shouldReceive('createBranch')->once();

        File::shouldReceive('exists')->twice()->andReturnTrue();

        $this->instance(
            'process.install',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->mustRun->getOutput')->once()->andReturn(
                        'test'
                    );
                }
            )
        );

        $this->instance(
            'process.update',
            m::mock(
                Process::class,
                function ($mock) {
                    $mock->shouldReceive('setWorkingDirectory->setTimeout->mustRun->getOutput')->once()->andReturn(
                        'test'
                    );
                }
            )
        );

        Git::shouldReceive('hasChanges')->andReturnTrue();
        Git::shouldReceive('addAllChanges->commit->push')->once();

        GitHub::shouldReceive('pullRequest->create')->once();

        $this->artisan('update')
             ->expectsOutput('init')
             ->assertExitCode(0);
    }
}
