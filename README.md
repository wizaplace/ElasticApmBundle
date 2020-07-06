# Elastic APM Symfony Bundle
Uses the [wizaplace/elastic-apm-wrapper](https://github.com/wizaplace/elastic-apm-wrapper).

[![License](https://poser.pugx.org/wizaplace/elastic-apm-symfony/license)](https://packagist.org/packages/wizaplace/elastic-apm-symfony)
[![CircleCI](https://circleci.com/gh/wizaplace/ElasticApmBundle/tree/master.svg?style=svg)](https://circleci.com/gh/wizaplace/ElasticApmBundle/tree/master)
[![Version](https://img.shields.io/github/v/release/wizaplace/ElasticApmBundle)](https://circleci.com/gh/wizaplace/ElasticApmBundle/tree/master)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/wizaplace/ElasticApmBundle/graphs/commit-activity)
[![Ask Me Anything !](https://img.shields.io/badge/Ask%20me-anything-1abc9c.svg)](https://GitHub.com/wizaplace/ElasticApmBundle)

## Installation

First, in your _.env_ file, add the following variables (replace with your values):

        PHILKRA_APP_NAME=My microservice
        PHILKRA_APP_VERSION=1.0
        PHILKRA_ENVIRONMENT=Dev
        PHILKRA_SERVER_URL=http://172.17.0.1:8200
        PHILKRA_SECRET_TOKEN=blabla
        ELASTIC_APM_ENABLED=1

Then you can add them in your _services.yaml_:

        parameters:
               elastic_apm.enabled: '%env(bool:ELASTIC_APM_ENABLED)%'
               philkra.app_name: '%env(PHILKRA_APP_NAME)%'
               philkra.app_version: '%env(PHILKRA_APP_VERSION)%'
               philkra.environment: '%env(PHILKRA_ENVIRONMENT)%'
               philkra.server_url: '%env(PHILKRA_SERVER_URL)%'
               philkra.secret_token: '%env(PHILKRA_SECRET_TOKEN)%'
               philkra.timeout: '%env(int:PHILKRA_TIMEOUT)%'

Next install the bundle via composer.

* For Symfony >=4.4:

        composer require wizaplace/elastic-apm-symfony
        
* For Symfony <=4.3:

        composer require wizaplace/elastic-apm-symfony:^1

## License
This library is licensed under the [MIT license](http://opensource.org/licenses/MIT).
