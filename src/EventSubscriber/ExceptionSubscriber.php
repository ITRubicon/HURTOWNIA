<?php

namespace App\EventSubscriber;

use App\Service\TaskReporter\TaskReporter;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ExceptionSubscriber implements EventSubscriberInterface
{
    private $id = false;
    private $taskReporter;

    public function __construct(TaskReporter $taskReporter)
    {
        $this->taskReporter = $taskReporter;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::TERMINATE => [
                ['setEnded', 1000]
            ],
            ConsoleEvents::COMMAND => [
                ['setStarted', 1000]
            ],
            ConsoleEvents::ERROR => [
                ['setError', 1000]
            ]
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
            $this->taskReporter->setEnd($this->id);
            $output = $event->getOutput();
            $output->writeln(sprintf('KOŃCOWY ZAPIS DO JOBS_HISTORY'));
        }
    }

    public function setError(ConsoleErrorEvent $event)
    {
        $output = $event->getOutput();
        if ($this->id == true) {
            $e = $event->getError();
            $msg = $e->getMessage() . ". W linii: " . $e->getLine() . ".\n" . $e->getTraceAsString();
            $this->taskReporter->setError($this->id, $msg);
            $output->writeln('');
            $output->writeln(sprintf('Bład  <info>%s</info>', $event->getError()));
        }
    }
}
