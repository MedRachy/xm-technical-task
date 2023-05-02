# XM technical task

## To do befor testing :

### Add Rapid API keys :

-   add keys in .env file :

```bash
X_RapidAPI_Key=xxxxxxxx
X_RapidAPI_Host=xxxxxxx
```

### Queue Configuration for Sending Emails

To avoid response delay SendEmail class implement "ShouldQueue", so it can be processed in the background when the form is submitted.

NOTE : using Queue was not asked in the exercise, to test the applciation without queues configuration, please remove " implements ShouldQueue" from SendEmail class and sending emails will be processed synchronously

Configurations steps :

1. Set up database queue driver :
   to use the database queue driver, you will need a database table to hold the jobs. To generate a migration that creates this table, run the queue:table Artisan command. Once the migration has been created, you may migrate your database using the migrate command:

```bash
- php artisan queue:table
- php artisan migrate
```

2. Instruct application to use database driver by updating queue_connection variable at .env file

```bash
QUEUE_CONNECTION=database
```

3.  Add mailtrap credentials to catch emails

```bash
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxxxxxx
MAIL_PASSWORD=xxxxxxxxx
```

4. Running the queue worker
   You may run the worker using the queue:work Artisan command.

```bash
- php artisan queue:work
```
