# jadob/jadob

## DISCLAIMER

There is no production-ready version at this time. Use at your own risk.

## Introduction

Jadob is a PHP7 application framework, based on Symfony and Zend Framework components. It comes with the most important
stuff needed to start developing your PHP project.
Jadob uses Twig for templating and Doctrine ORM for SQL databases support.

## Tests

Jadob uses PHPUnit and Phan to test the whole codebase. 

``php-ast`` extension would be useful. 

## Things that need to be done before `1.0.0` release
* [x]  30%+ Code Coverage
* [ ]  40%+ Code Coverage
* [ ]  50%+ Code Coverage
* [ ]  60%+ Code Coverage
* [ ]  70%+ Code Coverage
* [ ]  80%+ Code Coverage
* [ ]  90%+ Code Coverage
* [ ]  95%+ Code Coverage
* [ ]  CRAP Index >= 10 for all methods
* [ ] `Jadob\Core`
* [ ] `Jadob\Security`
* [ ] `Jadob\Saml`
* [ ] `Jadob\Oauth`
* [ ] `Jadob\Router`
* [ ] Better Error Handling
* [ ] Documentation
* [ ] Default Event Listeners should be moved from `Jadob\EventListener` to `Jadob\Core`, because they are framework-specific stuff.
* [ ] Service Providers Should be moved to `Jadob\Core` too.
* [ ] All Bridges (`Jadob\DoctrineORMBridge`, `Jadob\SymfonyFormBridge`) should be moved do `Jadob\Bridge\*` namespace.

## Getting Started

@TODO

## License 

MIT


