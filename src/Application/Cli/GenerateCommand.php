<?php

namespace Couscous\Application\Cli;

use Couscous\Generator;
use Couscous\Model\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates the website.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class GenerateCommand extends Command
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate the website')
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'Repository you want to generate.',
                getcwd()
            )
            ->addOption(
                'target',
                null,
                InputOption::VALUE_REQUIRED,
                'Target directory in which to generate the files.',
                getcwd().'/.couscous/generated'
            )
            ->addOption(
                'config-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Alternate config file instead of couscous.yml.',
                getcwd().'couscous.yml'
            )
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If specified will override entries in couscous.yml (key=value)',
                []
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cliConfig = $input->getOption('config');
        $configFile = $input->getOption('config-file');

        $project = new Project($input->getArgument('source'), $input->getOption('target'));

        $project->metadata['cliConfig'] = $cliConfig;
        if ($configFile)
            $project->metadata['configFile'] = $configFile;

        $this->generator->generate($project, $output);
    }
}
