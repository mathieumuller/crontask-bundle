<?php

namespace Axiolab\CronTaskBundle\Manager;

use Axiolab\CronTaskBundle\Entity\CronTask;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class CronTaskManager
{
    protected $repository;
    protected $em;
    protected $logger;

    public function __construct(EntityManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->repository = $this->em->getRepository('AxiolabCronTaskBundle:CronTask');
    }

    private function find($id)
    {
        return $this->repository->find($id);
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
        // If the entity manager has been closed during the command execution, it will be reinitialized here
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );

            //the repository and the cronTask have to be reinitialized too
            $this->repository = $this->em->getRepository('AxiolabCronTaskBundle:CronTask');
            $cronTask = $this->find($cronTask->getId());
        }

        $now = new \DateTime();
        $duration = $cronTask->getLastRun()->diff($now);
        $cronTask->setLastExecutionTime($now->setTime($duration->h, $duration->m, $duration->s))
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
        $message = $cronTask->getAlias().' => '.$e->getMessage();
        $this->logger->log('error', $message);
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

    public function findOneBy(array $search)
    {
        return $this->repository->findOneBy($search);
    }

    public function findBy(array $search, array $orderBy = [])
    {
        return $this->repository->findBy($search, $orderBy);
    }

    public function isRunning($alias)
    {
        $task = $this->repository->findOneBy(['alias' => $alias]);
        $status = !empty($task) ? $task->getCronStatus() : null;

        return $status === CronTask::STATUS_RUNNING;
    }
}
