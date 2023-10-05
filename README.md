# Introduction
i18n_mrg is the helper tool that scans your project's codebase for translated strings and synchronizes translations with a popular translation tool named [Weblate](https://weblate.org) and back.

# Installation
The software you need to install the translation merge tool:
- PHP >= 7.0
- [Composer](https://getcomposer.org/download/) package manager
- [GNU Gettext](https://www.gnu.org/software/gettext/) package (already installed on macOS and most Linux distributions)
- NodeJS >= 6.0 (for JS projects)
- [i18next converter](https://github.com/i18next/i18next-gettext-converter) (if you use i18next for your js project)

You require php>=8.1 to run the software.
Also, you require to have a PHP packet manager (composer), which you may get [here](https://getcomposer.org/download/).

Run the following command to install the tool:
```bash
composer global require lehadnk/translation-merge-tool
```

# Running the tool
Run the following command under your project root:
```
i18n_mrg
```

# Project setup
First, you need to prepare your codebase to work with i18n_mrg:
1. Configure your project to work with translation files. We'd suggest you use [GNU gettext](https://www.gnu.org/software/gettext/) format, but i18n_mrg could also compile JSON files as well if you prefer, which works better for web applications. You need to set up a directory in your project and store your translation files in it, naming each subfolder with the language code you're planning to use. Example file tree structure:
```
/i18n/en_GB/messages.po
/i18n/de_DE/messages.po
/i18n/zh_CN/messages.po
```
2. Use the wrapper function named `__()` to mark your translation strings. Implement the function to return the corresponding string from your storage, e.g.:
```java
public class i18nService {
    private static <LocaleEnum, HashMap<String, String>> translationStrings;

    public static String __(string text, HashMap<String, String> placeholders) 
    {
        var translatedString = i18nService.translationStrings.get("de_DE").get(text);
        StrSubstitutor sub = new StrSubstitutor(placeholders, "%", "%");
        return sub.replace(translatedString);
    }
}
```

```java
import static i18n.I18nFacade.__;
public class ScoreHandler {
    public UserResponse getScore()
    {
        var placeholders = new HashMap<String, String>();
        placeholders.put("score", 16);
            
        var response = new UserResponse();
        response.message = __("You have %score% points", placeholders); // Sie haben 16 Punkte
        
        return response;
    }
}
```
3. Add `.translate-config.json` to your project root. The example contents of the file:
```
{
  "configVersion": "1.3.0",

  "components": [
    {
      "name": "default",
      "includePaths": [
        "app/",
        "resources/"
      ],
      "excludePaths": [],
      "translationFileName": "resources/lang/i18n/{localeName}/LC_MESSAGES/default.po",
      "weblateProjectSlug": "crm",
      "weblateComponentSlug": "main"
    }
  ],

  "vcs": "bitbucket",
  "bitbucketUsername": "bitbucket@user.com",
  "bitbucketPassword": "password",
  "vcsRepository": "company/crm-project",
  "vcsAuthToken": "token",

  "translationBranchName": "translation",

  "weblateServiceUrl": "http://weblate.service.com",
  "weblateAuthToken": "token"
}
```
4. Next, define the translation branch in your repository. Usually, you want it to be managed by translation tools (i18n_mrg and Weblate) only, and never touch it: `git checkout -b translations && git push --set-upstream origin translations`
5. Set up the Weblate platform and define the [component](https://docs.weblate.org/en/latest/admin/projects.html) for your project.
6. Now run `i18n_mrg` to scan your project for translation strings and initially upload them to Weblate. i18n_mrg will parse your codebase for every string wrapped in __() decorator and add it to translation files, the pull updates from Weblate automatically.

# Developer workflow
For developers working with `i18n_mrg` on your project, I'd suggest the following workflow:
1. When you're about to be done with your working branch, which contains translation strings, run `i18n_mrg` and push new translation strings to Weblate.
2. Notify the person in your team responsible for handling translation that new strings are added to Weblate.
3. Once translations are done, run `i18n_mrg` again to pull translated strings from Weblate and add updated translated files to your branch.

In big teams, you may also want translations files compilation to be a part of your CI cycle.

# Updating the tool
```bash
composer global update lehadnk/translation-merge-tool
```

# Handling authorization tokens
To use the tool with various VCS providers, you must set up authorization.

### Gitlab
1. Create the key using your profile settings > Access Tokens.
2. Your key must have the next scopes: api, read_repository, write_repository
3. Export your token to I18N_MRG_GITLAB_AUTH_TOKEN env variable: `export I18N_MRG_GITLAB_AUTH_TOKEN=<token>`

### Github
1. Goto Settings > Developer Settings > Personal access tokens
2. Create a token with scope: repo
3. Export your token to I18N_MRG_GITHUB_AUTH_TOKEN env variable: `export I18N_MRG_GITHUB_AUTH_TOKEN=<token>`

### BitBucket
1. Go to repository settings
2. Issue the repository access token with read/write permissions in the "Access Tokens" section
3. Place your token in I18N_MRG_BITBUCKET_AUTH_TOKEN env variable: `export I18N_MRG_BITBUCKET_AUTH_TOKEN=<token>`

# How to add it to the project
First, you need to create a`.translate-config.json` configuration file under the project's root. Example contents of the config file:


# Troubleshooting
Some Mac users reported that they had issues with the gettext tool installed through Brew. Unlinking and linking it again resolves the issue:
```
brew unlink gettext && brew link gettext
```
