<?php

namespace Axiolab\CronTaskBundle\Manager;

use Axiolab\CronTaskBundle\Entity\CronTask;
use Doctrine\ORM\EntityManager;

class CronTaskManager
{
    protected $repository;
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('AxiolabCronTaskBundle:CronTask');
    }

    public function begin(CronTask $cronTask)
    {
        $cronTask->setLastRun(new \DateTime())
            ->setCronStatus(CronTask::STATUS_RUNNING)
        ;

        $this->em->flush();
    }

    public function end(CronTask $cronTask)
    {
        $duration = (new \DateTime())->diff($cronTask->getLastRun());
        $cronTask->setLastExecutionTime($duration)
            ->setCronStatus(CronTask::STATUS_WAITING)
        ;

        $this->em->flush();
    }

    public function createNew($alias, $interval, $firstRun)
    {
        $cronTask = new CronTask();
        $cronTask->setAlias($alias)
            ->setExecutionInterval($interval)
            ->setFirstRun($firstRun)
            ->setCronStatus(CronTask::STATUS_WAITING)
        ;

        $this->em->persist($cronTask);
        $this->em->flush();
    }

    public function clearError(CronTask $cronTask)
    {
        $duration = (new \DateTime())->diff($cronTask->getLastRun());
        $cronTask->lastExecutionError(null)
            ->setCronStatus(CronTask::STATUS_WAITING)
        ;

        $this->em->flush();
    }

    public function catchError(CronTask $cronTask, \Exception $e)
    {
        $duration = (new \DateTime())->diff($cronTask->getLastRun());
        $cronTask->setLastExecutionTime($duration)
            ->setCronStatus(CronTask::STATUS_ERROR)
            ->setLastExecutionError($e->getMessage())
        ;

        $this->em->flush();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function disableCronTask($alias, $enable = false)
    {
        if ($task = $this->repository->findOneBy(['alias' => $alias])) {
            try {
                $task->setCronStatus($enable ? CronTask::STATUS_WAITING : CronTask::STATUS_DISABLED);
                $this->em->flush();
                $result = ['success' => true, 'message' => $enable ? 'axiolab.crontask.message.enabled_success' : 'axiolab.crontask.message.disabled_success'];
            } catch (\Exception $e) {
                $result = ['success' => false, 'message' => 'axiolab.crontask.message.error_occured'];
            }
        } else {
            $result = ['success' => false, 'message' => 'axiolab.crontask.message.unknown_task'];
        }

        return $result;
    }
}
