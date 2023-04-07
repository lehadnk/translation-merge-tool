<?php

namespace TranslationMergeTool\Environment;

class EnvironmentFactory
{
    public function build(): Environment
    {
        return new Environment(
            getenv('I18N_MRG_GITLAB_AUTH_TOKEN'),
            getenv('I18N_MRG_GITHUB_AUTH_TOKEN'),
            getenv('I18N_MRG_BITBUCKET_USERNAME'),
            getenv('I18N_MRG_BITBUCKET_PASSWORD'),
            getenv('I18N_WEBLATE_AUTH_TOKEN'),
        );
    }
}
