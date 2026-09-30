# Deploy SoleVibe to Render

## Before deploying

- Rotate the Aiven password if it has been shared or exposed.
- Push the reviewed project and Docker files to a GitHub repository. Do not commit `.env`, database credentials, or CA certificates.
- Create a persistent `APP_KEY` with PHP 8.3 by running `php artisan key:generate --show` once. Keep the same value for future deployments.
- Create an Aiven MySQL service, download its CA certificate, and keep the connection host, port, database, username, and new password available.

## Create the Render service

1. In Render, create a **Web Service** from the GitHub repository and select the branch to deploy.
2. Choose **Docker** as the runtime. Leave Root Directory blank when the repository root contains this Dockerfile.
3. Set Health Check Path to `/up`.
4. Add the variables from `docker/render.env.example` in the service's Environment settings. Use the Aiven values from the service's connection panel, set `APP_URL` to Render's HTTPS URL, and paste the persistent `APP_KEY`.
5. Add a Render Secret File named `ca.pem` containing the full Aiven CA certificate. Keep `MYSQL_ATTR_SSL_CA=/etc/secrets/ca.pem`.
6. Deploy and check the logs. The container builds Vite assets and runs database migrations at startup.

Do not enable production seeders yet. The current seeders create a hard-coded admin account and are not safe to run repeatedly; make them configurable and idempotent before using them on Aiven.

## Important runtime notes

- Render's local filesystem is ephemeral. Product/category images uploaded to the local `public` disk can disappear on redeploy or restart. Use object storage or a supported persistent disk before relying on admin uploads.
- `MAIL_MAILER=log` does not send verification or order emails. Configure a real mail provider before testing those flows.
- Database migrations create the schema, not your local records. Export/import local data separately if it must be preserved.