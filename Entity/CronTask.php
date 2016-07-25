<?php

namespace Axiolab\CronTaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AxiolabCronTaskBundle\Repository\CronTaskRepository")
 * @ORM\Table(name="axiolab_cron_task", options={"charset":"UTF8"})
 * @UniqueEntity("alias")
 */
class CronTask
{
    const STATUS_WAITING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_ERROR = 3;
    const STATUS_DISABLED = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $alias;

    /**
     * @ORM\Column(type="string")
     */
    protected $executionInterval;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $firstRun;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastRun;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $lastExecutionTime;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastExecutionError;

    /**
     * @ORM\Column(type="integer")
     */
    protected $cronStatus;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of alias.
     *
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Sets the value of alias.
     *
     * @param mixed $alias the alias
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Gets the value of interval.
     *
     * @return mixed
     */
    public function getExecutionInterval()
    {
        return $this->executionInterval;
    }

    /**
     * Sets the value of interval.
     *
     * @param mixed $interval the interval
     *
     * @return self
     */
    public function setExecutionInterval($interval)
    {
        $this->executionInterval = $interval;

        return $this;
    }

    /**
     * Gets the value of lastRun.
     *
     * @return mixed
     */
    public function getLastrun()
    {
        return $this->lastRun;
    }

    /**
     * Sets the value of lastRun.
     *
     * @param mixed $lastRun the lastRun
     *
     * @return self
     */
    public function setLastrun(\DateTime $lastRun = null)
    {
        $this->lastRun = $lastRun;

        return $this;
    }

    /**
     * Gets the value of lastExecutionTime.
     *
     * @return mixed
     */
    public function getLastExecutionTime()
    {
        return $this->lastExecutionTime;
    }

    /**
     * Sets the value of lastExecutionTime.
     *
     * @param mixed $lastExecutionTime the last execution time
     *
     * @return self
     */
    public function setLastExecutionTime($lastExecutionTime)
    {
        $this->lastExecutionTime = $lastExecutionTime;

        return $this;
    }

    /**
     * Gets the value of lastExecutionError.
     *
     * @return mixed
     */
    public function getLastExecutionError()
    {
        return $this->lastExecutionError;
    }

    /**
     * Sets the value of lastExecutionError.
     *
     * @param mixed $lastExecutionError the last execution error
     *
     * @return self
     */
    public function setLastExecutionError($lastExecutionError)
    {
        $this->lastExecutionError = $lastExecutionError;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return mixed
     */
    public function getCronStatus()
    {
        return $this->cronStatus;
    }

    /**
     * Sets the value of status.
     *
     * @param mixed $status the status
     *
     * @return self
     */
    public function setCronStatus($status)
    {
        $this->cronStatus = $status;

        return $this;
    }

    /**
     * Gets the value of firstRun.
     *
     * @return mixed
     */
    public function getFirstRun()
    {
        return $this->firstRun;
    }

    /**
     * Sets the value of firstRun.
     *
     * @param mixed $firstRun the first run
     *
     * @return self
     */
    public function setFirstRun($firstRun)
    {
        if (!$firstRun instanceof \DateTime) {
            try {
                $firstRun = new \DateTime($firstRun);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        $this->firstRun = $firstRun;

        return $this;
    }
}
