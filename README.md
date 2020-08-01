#Installation
The software you need to install translation merge tool:
- PHP >= 7.0
- NodeJS >= 6.0
- [Composer](https://getcomposer.org/download/) packet manager
- [GNU Gettext](https://www.gnu.org/software/gettext/) package (already installed in most macOS/linux versions)
- [i18next converter](https://github.com/i18next/i18next-gettext-converter) (if you use i18next on your js project)

You require php>=7.0 in order to run the software.
Also, you require to have PHP packet manager (composer) which you mat get [here](https://getcomposer.org/download/).

First, add this repository to your ~/.composer/composer.json file (create it if you don't have one):
```json
{
  "repositories": [
    {
      "type": "git",
      "url": "git@gitlab.com:giftd/translation-merge-tool.git"
    }
  ]
}
```

Next, install the tool:
```bash
composer global require giftd/translation-merge-tool
```

#Updating tool
```bash
composer global update giftd/translation-merge-tool
```

# Handling authorization tokens
To use the tool with various VCS providers, you must setup authorization.

#### Gitlab
1. Create the key using your profile settings > Access Tokens.
2. Your key must have next scopes: api, read_repository, write_repository
3. Export your token to I18N_MRG_GITLAB_AUTH_TOKEN env variable: `export I18N_MRG_GITLAB_AUTH_TOKEN=<token>`

### Github
1. Goto Settings > Developer Settings > Personal access tokens
2. Create a token with scope: repo
3. Export your token to I18N_MRG_GITHUB_AUTH_TOKEN env variable: `export I18N_MRG_GITHUB_AUTH_TOKEN=<token>`

### BitBucket
1. Place your username under I18N_MRG_BITBUCKET_USERNAME env variable: `export I18N_MRG_BITBUCKET_USERNAME=<username>`
2. Place your password under I18N_MRG_BITBUCKET_PASSWORD env variable: `export I18N_MRG_BITBUCKET_PASSWORD=<password>`

#How to add it to the project
First, you need create `.translate-config.json` configuration file under the project's root. An example contents of config file you can always get here:
```
{
  "configVersion": "1.1.13",

  "components": [
    {
      "name": "default",
      "includePaths": [
        "app/",
        "resources/"
      ],
      "excludePaths": [],
      "translationFileName": "resources/lang/i18n/{localeName}/LC_MESSAGES/default.po"
    }
  ],

  "vcs": "bitbucket",
  "vcsUsername": "lehadnk@gmail.com",
  "vcsPassword": "password",
  "vcsRepository": "nevidimov/giftd-crm",
  "vcsAuthToken": "token",

  "translationBranchName": "translation",

  "weblateServiceUrl": "http://159.65.200.211",
  "weblateProjectSlug": "crm",
  "weblateComponentSlug": "main",
  "weblateAuthToken": "token"
}
```

#Running tool
Run the following command under your project root:
```
i18n_mrg
```

#Troubleshooting
If you haven't gettext on macOS, you can install it using brew:
```
brew link gettext && brew link gettext
```
