# Github Issue Auto Labeler

The purpose of this project is to auto-label issues linked to newly created PR's. It works the same way the auto-close functionality on Github does.

## The following need to be set in the environment

- `API_TOKEN` Your github API Token
- `SECRET` The secret being passed from the webhook
- `GITHUB_LABEL` The label to apply to the issue

## Webhook setup

- Create a new webhook for your project.
- Set the payload URL to the `web/index.php` in this project.
- Set the Content type to `application/json`
- Set the secret to match what you have set in the environment above
- Select the `Pull Request` event to trigger on.
