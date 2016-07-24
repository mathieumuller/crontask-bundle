<?php

namespace AxiolabCronTaskBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DisableCronTaskCommand extends AxiolabCronTaskParent
{
    protected function configure()
    {
        $this
            ->setName('axiolab:crontask:disable')
            ->setDescription('Create a new cron task')
            ->setDefinition([
                new InputArgument('alias', InputArgument::REQUIRED, 'The command alias to disable'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $alias = $input->getArgument('alias');
        $result = $this->getContainer()->get('axiolab.crontask.manager')->disableCronTask($alias);
        $method = $result['success'] ? 'success' : 'error';
        $io->{$method}($this->trans($result['message'], ['%alias%' => $alias]));
    }
}
