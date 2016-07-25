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
            $this->trans('axiolab.crontask.report.headers.next_execution'),
        ];

        foreach ($this->getContainer()->get('axiolab.crontask.manager')->findAll() as $task) {
            $nextExecutionDT = $task->getLastRun() ? $task->getLastRun()->add(new \DateInterval($task->getExecutionInterval())) : $task->getFirstRun();
            $rows[] = [
                $task->getAlias(),
                $this->trans('axiolab.crontask.status.'.$task->getCronStatus()),
                $nextExecutionDT->format('Y-m-d H:i:s'),
            ];
        }
        $io->table($headers, $rows);
    }
}
