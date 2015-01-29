# Contributing

Contributions are **welcome** and will be fully **credited**.


## Posting Issues


- **Provide detailed log** - If a command is failing, post the full output you get when running the command, with the `--debug` flag.


## Pull Requests

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


## Coding Standard

- We use **[Zenify Coding Standard](https://github.com/Zenify/CodingStandard)**

- The easiest way to apply the conventions:

 ```sh
 $ vendor/bin/phpcs src tests --extensions=php --ignore=bootstrap --standard=vendor/zenify/coding-standard/src/ZenifyCodingStandard/ruleset.xml
 ```


## Tests

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- Run tests by calling `phpunit`


Both coding standard and tests are validated by [Travis CI](.travis.yml), so just make it pass. 


**Happy coding**!
