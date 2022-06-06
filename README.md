# insider-test-task

This is a test-task with football league results prediction

I made almost everything extendable that is why we have a lot of interfaces which allow us to swap dependencies (Predictors, for example)
We can also create even new League types if we implement LeagueImplementation interface and add new type to enum.

Basically, I could have predicted match results in many ways, but I decided to use Machine Learning. There are not so many params (features) we can use to train my model
that is why its accuracy is pretty low. I used last 2 Premier League seasons datasets to train my models and added all teams from them to the seeder.

I covered around 65% of code with Unit/Feature tests using fresh and new Pest framework, because I love it.

On the frontend part I used Vue Js 3 with Options API, vue-router and axios.
All styles are created and preprocessed via Tailwind CSS framework.
Moreover, my app works via SPA, so no page reloads are required.

This project uses PSR12 coding standard and PHPStan analyzer at level 5

## Dependencies

- `Docker`
- `PHP 8.1`
- `composer`

No Node JS is required because it is used inside Docker containers via Makefile commands

## How to use

-  copy `.env.example` to `.env`
- `make install` to install deps
- `make init` to migrate and seed DB
- `make up` to bring everything up
- Feel free to use `make test` and `make test-cover` to run PSR12, PHPStan checks and Pest tests
