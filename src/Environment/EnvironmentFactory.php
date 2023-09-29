<?php

namespace TranslationMergeTool\Environment;

class EnvironmentFactory
{
    public function build(): Environment
    {
        return new Environment(
            $this->getEnvironmentVariable('I18N_MRG_GITLAB_AUTH_TOKEN'),
            $this->getEnvironmentVariable('I18N_MRG_GITHUB_AUTH_TOKEN'),
            $this->getEnvironmentVariable('I18N_MRG_BITBUCKET_AUTH_TOKEN'),
            $this->getEnvironmentVariable('I18N_WEBLATE_AUTH_TOKEN'),
        );
    }

    private function getEnvironmentVariable(string $name): ?string
    {
        $value = getenv($name);
        if ($value === false) {
            return null;
        }

        return $value;
    }
}
