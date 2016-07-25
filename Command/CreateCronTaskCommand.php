<?php

namespace Axiolab\CronTaskBundle\Command;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCronTaskCommand extends AxiolabCronTaskParent
{
    protected function configure()
    {
        $this
            ->setName('axiolab:crontask:create')
            ->setDescription('Create a new cron task')
            ->setDefinition([
                new InputOption('alias', '', InputOption::VALUE_REQUIRED, 'Name of the new task'),
                new InputOption('interval', '', InputOption::VALUE_REQUIRED, 'Execution interval (DateInterval code)'),
                new InputOption('first_execution', '', InputOption::VALUE_REQUIRED, 'First Execution date (Y-m-d H:i:s)'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $alias = $io->ask($this->trans('axiolab.crontask.question.provide_alias'), $this->trans('axiolab.crontask.default.alias'));
        $interval = $io->ask($this->trans('axiolab.crontask.question.provide_interval'), $this->trans('axiolab.crontask.default.interval'));
        $firstRun = $io->ask($this->trans('axiolab.crontask.question.provide_first_run'), $this->trans('axiolab.crontask.default.first_run'));

        $cronTask = $this->getContainer()->get('axiolab.crontask.manager')->createNew($alias, $interval, $firstRun);
        $io->success($this->trans('axiolab.crontask.message.creation_successfull'));
    }
}
