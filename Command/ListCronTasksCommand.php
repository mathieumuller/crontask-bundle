<?php

namespace Axiolab\CronTaskBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCronTasksCommand extends AxiolabCronTaskParent
{
    protected function configure()
    {
        $this
            ->setName('axiolab:crontask:list')
            ->setDescription('List cron tasks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $rows = [];

        $headers = [
            $this->trans('axiolab.crontask.report.headers.task'),
            $this->trans('axiolab.crontask.report.headers.status'),
        ];

        foreach ($this->getContainer()->get('axiolab.crontask.manager')->findAll() as $task) {
            $rows[] = [
                $task->getAlias(),
                $this->trans('axiolab.crontask.status.'.$task->getCronStatus()),
            ];
        }
        $io->table($headers, $rows);
    }
}
