<?php

namespace TranslationMergeTool\Environment;

class Environment
{
    public function __construct(
        public readonly ?string $gitlabAuthToken,
        public readonly ?string $githubAuthToken,
        public readonly ?string $bitbucketUsername,
        public readonly ?string $bitbucketPassword,
        public readonly ?string $weblateAuthToken,
    ) {
    }
}
