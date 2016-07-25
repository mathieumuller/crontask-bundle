<?php

namespace Axiolab\CronTaskBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class AxiolabCronTaskParent extends ContainerAwareCommand
{
    protected function trans($key, array $parameters = [])
    {
        return $this->getContainer()->get('translator')->trans($key, $parameters);
    }
}
