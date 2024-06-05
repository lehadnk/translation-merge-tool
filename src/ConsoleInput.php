<?php

namespace TranslationMergeTool;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use TranslationMergeTool\Application\Application;
use TranslationMergeTool\DTO\Arguments;
use TranslationMergeTool\Environment\EnvironmentFactory;
use TranslationMergeTool\Output\CliOutputInterface;
use TranslationMergeTool\System\Git;

class ConsoleInput extends CLI
{
    protected function setup(Options $options)
    {
        $options->setHelp('A tool for merging translation files for giftd projects');
        $options->registerOption('just-parse', 'just parse code, no further actions', 'j');
        $options->registerOption('version', 'print version', 'v');
        $options->registerOption('weblate-pull', 'just pull Weblate, no other actions', 'w');
        $options->registerOption('print-untranslated', 'print all untranslated strings from current branch');
        $options->registerOption('prune', 'mark all non-existing strings in project as disabled');
        $options->registerOption('force', 'pushes sources to repository and pulls component, even if no changes are found', 'f');
        $options->registerOption('no-weblate', 'skips all weblate-based operations');
        $options->registerOption('autoconfirm', 'autoconfirms all prompts');
        $options->registerOption('workingdir', 'sets working dir', 'd', true);
    }

    protected function main(Options $options)
    {
        $arguments = new Arguments(
            $this->options->getOpt('weblate-pull', false),
            $this->options->getOpt('just-parse', false),
            $this->options->getOpt('check', false),
            $this->options->getOpt('prune', false),
            $this->options->getOpt('print-untranslated', false),
            $this->options->getOpt('version', false),
            $this->options->getOpt('force', false),
            $this->options->getOpt('no-weblate', false),
            $this->options->getOpt('autoconfirm', false),
        );

        $environmentFactory = new EnvironmentFactory();
        $application = new Application(
            $this->options->getOpt('workingdir', getcwd()),
            $arguments,
            $environmentFactory->build(),
            new CliOutputInterface($this),
            new Git(null)
        );

        return $application->run();
    }
}
