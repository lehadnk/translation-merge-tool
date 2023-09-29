<?php

namespace TranslationMergeTool\Environment;

class Environment
{
    public function __construct(
        public readonly ?string $gitlabAuthToken,
        public readonly ?string $githubAuthToken,
        public readonly ?string $bitbucketAccessToken,
        public readonly ?string $weblateAuthToken,
    ) {
    }
}
