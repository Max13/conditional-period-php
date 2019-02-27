# ConditionalPeriod

`ConditionalPeriod` is a php class allowing to store 2 numerical/`DateInterval` intervals resulting in a `DateInterval`.

It is, actually, a requirement for a project I'm working on. Typically, it concerns the working contracts. Here is an example using integers as conditions:

- If the **category** of the employee is **between 1 and 5**, the *prior notice* is **1 month**.
- If the **category** of the employee is **between 6 and 7**, the *prior notice* is **2 months**.
- If the **category** of the employee is **between 8 and 12**, the *prior notice* is **3 months**.

Here is another example using date intervals as conditions:

- If the **fixed term contract** lasts from **0** to **6 months**, then the *trial period* is **15 days**.
- Above **6 months**, the *trial period* is **1 month**.

Expressing these interval with conditions using `ConditionalPeriod` is super duper easy and can be expressed in 2 ways: Using 4 arguments on the constructor, or using a string syntax.

## Installation

You can use composer: `composer require max13/conditional-period:^1.0`

Or download the repo and add the files (in `/src`) to your project.

## Usage

### Using 4 arguments on the constructor, aka "classic instantiation"

```php
use MX\ConditionalPeriod;
...
$prior_notices = [
    new ConditionalPeriod(
        ConditionalPeriod::CATEGORY,
        1,
        5,
        DateInterval::createFromDateString('1 month')
    ),
    new ConditionalPeriod(
        ConditionalPeriod::CATEGORY,
        6,
        7,
        DateInterval::createFromDateString('2 months')
    ),
    new ConditionalPeriod(
        ConditionalPeriod::CATEGORY,
        8,
        12,
        DateInterval::createFromDateString('3 months')
    ),
];

$trial_periods = [
    new ConditionalPeriod(
        ConditionalPeriod::DURATION,
        DateInterval::createFromDateString(0),
        DateInterval::createFromDateString('6 months'),
        DateInterval::createFromDateString('15 days')
    ),
    new ConditionalPeriod(
        ConditionalPeriod::DURATION,
        DateInterval::createFromDateString('6 months'),
        DateInterval::createFromDateString('99 years'), // Equivalent to +∞
        DateInterval::createFromDateString('1 month')
    ),
];
```


### Using the short string format, aka "badass mode"

```php
use MX\ConditionalPeriod;
...
$prior_notices = [
    new ConditionalPeriod('C1-5P1M'),
    new ConditionalPeriod('C6-7P2M'),
    new ConditionalPeriod('C8-12P3M'),
];

$trial_periods = [
    new ConditionalPeriod('DP0DP6MP15D'),
    new ConditionalPeriod('DP6MP99YP1M')
];
```

### Miscellaneous

You may of may not have noticed it, but every `DateInterval` argument can be replaced by either its [`ISO8601` duration spec](https://en.wikipedia.org/wiki/ISO_8601#Durations) (the same way you can [instanciate a `DateInteval`](http://php.net/dateinterval.construct), or a relative date string (as you can [`DateInterval::createFromDateString`](http://php.net/dateinterval.createfromdatestring).

So, here are the 3 same ways to input a `DateInterval` using `ConditionalPeriod` constructor:

- `DateInterval::createFromDateString('1 year, 2 months, 3 days')`
- `new DateInterval('P1Y2M3D');`
- `'1 year, 2 months, 3 days'`
- `'P1Y2M3D'`

## Need help?
Open an issue.

Now have fun.
