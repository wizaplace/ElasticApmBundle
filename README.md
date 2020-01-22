# Elastic APM Symfony Bundle
Uses [wizaplace/wizaplace/elastic-apm-wrapper](https://github.com/wizaplace/elastic-apm-wrapper).


## Installation

1) Add the repositories to your _composer.json_ file: 

        {
            "type": "git",
            "url": "https://github.com/wizaplace/elastic-apm-symfony"
        },
        {
            "type": "git",
            "url": "https://github.com/wizaplace/elastic-apm-wrapper"
        }
        
2)    Add the requirements in your _require_ section of the _composer.json_ file:
`        "philkra/elastic-apm-php-agent": "dev-master"`

3)   Require the bundle _composer require wizaplace/elastic-apm-symfony_
