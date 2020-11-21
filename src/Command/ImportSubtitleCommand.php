<?php

namespace App\Command;

use App\Service\SubtitleImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSubtitleCommand extends Command
{
    protected static $defaultName = 'app:import-subtitle';
    /** @var SubtitleImporter */
    private $subtitleImporter;

    protected function configure()
    {
        $this
            ->setDescription('Import (Netflix) subtitle file')
            ->addArgument('bookId', InputArgument::REQUIRED, 'The Id of the book in the db')
            ->addArgument('file', InputArgument::REQUIRED, 'The subtitle XML file')
            ->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'Commit to db')
            ->addOption('silent', null, InputOption::VALUE_OPTIONAL, 'Do not output the full table');
    }

    public function __construct(SubtitleImporter $subtitleImporter)
    {
        $this->subtitleImporter = $subtitleImporter;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if ($file) {
            $io->note(sprintf('You passed an argument: %s', $file));
            $contents = simplexml_load_string(file_get_contents($file));

            $lines = $this->subtitleImporter->import($contents);
        }

        if (is_null($lines)) {
            $io->error('Could not get lines from file.');
            return Command::FAILURE;
        }

        if (!$input->getOption('silent')) {
            $io->table(
                [
                    'Text',
                    'Time',
                    'Number',
                ],
                $lines
            );
        }

        if ($input->getOption('commit')) {
            $bookId = $input->getArgument('bookId');
            if ($bookId) {
                $io->note('Inserting!');
                $this->subtitleImporter->commit($lines, $bookId);

                $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

                return Command::SUCCESS;
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
