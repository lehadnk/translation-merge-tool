# Installation
The software you need to install the translation merge tool:
- PHP >= 7.0
- [Composer](https://getcomposer.org/download/) packet manager
- [GNU Gettext](https://www.gnu.org/software/gettext/) package (already installed in most macOS/Linux versions)
- NodeJS >= 6.0 (for JS projects)
- [i18next converter](https://github.com/i18next/i18next-gettext-converter) (if you use i18next for your js project)

You require php>=7.0 to run the software.
Also, you require to have a PHP packet manager (composer), which you may get [here](https://getcomposer.org/download/).

Run the following command to install the tool:
```bash
composer global require lehadnk/translation-merge-tool
```

# Updating the tool
```bash
composer global update lehadnk/translation-merge-tool
```

# Handling authorization tokens
To use the tool with various VCS providers, you must setup authorization.

### Gitlab
1. Create the key using your profile settings > Access Tokens.
2. Your key must have the next scopes: api, read_repository, write_repository
3. Export your token to I18N_MRG_GITLAB_AUTH_TOKEN env variable: `export I18N_MRG_GITLAB_AUTH_TOKEN=<token>`

### Github
1. Goto Settings > Developer Settings > Personal access tokens
2. Create a token with scope: repo
3. Export your token to I18N_MRG_GITHUB_AUTH_TOKEN env variable: `export I18N_MRG_GITHUB_AUTH_TOKEN=<token>`

### BitBucket
1. Place your username under I18N_MRG_BITBUCKET_USERNAME env variable: `export I18N_MRG_BITBUCKET_USERNAME=<username>`
2. Place your password under I18N_MRG_BITBUCKET_PASSWORD env variable: `export I18N_MRG_BITBUCKET_PASSWORD=<password>`

# How to add it to the project
First, you need to create a`.translate-config.json` configuration file under the project's root. Example contents of the config file:
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

# Running the tool
Run the following command under your project root:
```
i18n_mrg
```

# Troubleshooting
If you have issues with gettext on macOS, try to unlink/link it:
```
brew unlink gettext && brew link gettext
```