<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\TaskReporter\TaskReporter;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;


class ExceptionSubscriber implements EventSubscriberInterface
{
    private $id = false;
    private $taskReporter = 'unknown';

    public function __construct(TaskReporter $taskReporter)
    {
        $this->taskReporter = $taskReporter;
    }

    public static function getSubscribedEvents()
    {
        return [
            // ConsoleEvents::TERMINATE => [
            //     ['setEnded', 1000]
            // ],
            // ConsoleEvents::COMMAND => [
            //     ['setStarted', 1000]
            // ],
            // ConsoleEvents::ERROR => [
            //     ['setError', 1000]
            // ]
        ];
    }

    public function setStarted(ConsoleCommandEvent $event)
    {
        $input = $event->getInput();
        $output = $event->getOutput();
        $command = $event->getCommand();

        $args = $input->getArguments() ?? [];

        $commandName = $command->getName();
        if ($commandName != 'cache:clear') {
            $this->id = $this->taskReporter->setStart($commandName, $args);
            
            $output->writeln(sprintf('ZAPIS DO JOBS_HISTORY'));
        }
    }

    public function setEnded(ConsoleTerminateEvent $event)
    {
        if ($this->id == true) {
            $input = $event->getInput();
            $this->taskReporter->setEnd($this->id);
            
            $output = $event->getOutput();
            // $command = $event->getCommand();
            // $commandName = $command->getName();
            $output->writeln('');
            $output->writeln(sprintf('KOŃCOWY ZAPIS DO JOBS_HISTORY'));
        }
    }

    public function setError(ConsoleErrorEvent $event)
    {
        // $input = $event->getInput();
        $output = $event->getOutput();

        if ($this->id == true) {
            $this->taskReporter->setError($this->id, $event->getError());
            $output->writeln('');
            $output->writeln(sprintf('Bład  <info>%s</info>', $event->getError()));
        }
    }
}
