<?php

namespace Axiolab\CronTaskBundle\Command;

use Axiolab\CronTaskBundle\Entity\CronTask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AxiolabCronTaskMasterCommand extends AxiolabCronTaskParent
{
    protected function configure()
    {
        $this
            ->setName('axiolab:crontask:run')
            ->setDescription('Axiolab cron tasks manager')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $beginning = new \DateTime();
        $manager = $this->getContainer()->get('axiolab.crontask.manager');
        $reportHeaders = [
            $this->trans('axiolab.crontask.report.headers.began_at'),
            $this->trans('axiolab.crontask.report.headers.task'),
            $this->trans('axiolab.crontask.report.headers.status'),
            $this->trans('axiolab.crontask.report.headers.execution_time'),
        ];
        $executions = [];
        foreach ($manager->findAll() as $task) {
            if ($this->needsRun($task, $io)) {
                try {
                    $this->runTask($task, $output, $io, $manager);
                } catch (\Exception $e) {
                    $io->error($this->trans('axiolab.crontask.report.error_occured', ['%alias%' => $task->getAlias()]));
                    $manager->catchError($task, $e);
                }

                $executions[] = [
                    $beginning->format('Y-m-d H:i:s'),
                    $task->getAlias(),
                    $this->trans('axiolab.crontask.status.'.$task->getCronStatus()),
                    $task->getLastExecutionTime()->format('%H:%I:%S'),
                ];
            }
        }

        if (!empty($executions)) {
            $io->table($reportHeaders, $executions);
        } else {
            $io->comment($this->trans('axiolab.crontask.report.nothing_to_execute'));
        }
    }

    protected function needsRun(CronTask $cronTask, $io)
    {
        $needsRun = false;

        switch ($cronTask->getCronStatus()) {
            case Crontask::STATUS_WAITING:
                $now = new \DateTime();
                $firstRun = $cronTask->getFirstRun();
                $lastRun = $cronTask->getLastRun();
                $nextRun = empty($lastRun) ? $firstRun : $lastRun->add(new \DateInterval($cronTask->getExecutionInterval()));
                $needsRun = $now >= $nextRun;
                break;
            case CronTask::STATUS_RUNNING:
                $io->warning($this->trans('axiolab.crontask.message.already_running', ['%alias%' => $cronTask->getAlias()]));
                break;
            case CronTask::STATUS_ERROR:
                $io->warning($this->trans('axiolab.crontask.message.task_error', ['%alias%' => $cronTask->getAlias()]));
                break;
            case CronTask::STATUS_DISABLED:
                $io->warning($this->trans('axiolab.crontask.message.task_disabled', ['%alias%' => $cronTask->getAlias()]));
                break;
            default:
                $io->error($this->trans('axiolab.crontask.message.unknown_status', ['%alias%' => $cronTask->getAlias()]));
                break;
        }

        return $needsRun;
    }

    protected function runTask($task, $output, $io, $manager)
    {
        $alias = $task->getAlias();
        $namespace = split(' ', $alias)[0];
        $command = $this->getApplication()->find($namespace);
        $input = new StringInput($alias);

        $io->comment($this->trans('axiolab.crontask.message.executing', ['%alias%' => $alias]));
        $manager->begin($task);
        $resultCode = $command->run($input, $output);
        $manager->end($task);

        return $resultCode;
    }
}
