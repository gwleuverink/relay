<p align="center">
<img src="./public/icon.png" width="180" />
</p>

# Relay

Relay provides real-time monitoring of your GitHub Workflows directly from your Mac menu bar. Stay on top of your CI/CD pipelines without constantly checking GitHub.

<!-- <br />

> [!NOTE]
> At present, only a macOS build is available. Windows and Linux builds are pending due to lack of testing environments. Contributions from users with access to these platforms are more than welcome. -->

<br />

**[Download the latest release here](https://github.com/gwleuverink/relay/releases)**

<br />

## Features

Relay comes packed with powerful features to enhance your GitHub Workflows experience:

- **Repository Management**: Easily configure and monitor multiple repositories
- **Notifications**: Get instant alerts when new runs are detected
- **Live Status Updates**: Watch your workflows progress in real-time
- **Detailed Insights**: Dive deep into workflow runs with comprehensive execution details
- **Workflow Control**: Trigger, cancel, and manage workflow runs directly from your menu bar

<!-- <div align="center">
<img src="https://github.com/gwleuverink/relay/blob/main/storage/app/public/screenshots/menu-bar.png?raw=true" width="400"  alt="Menu bar screenshot" />

<img src="https://github.com/gwleuverink/relay/blob/main/storage/app/public/screenshots/detail-window.png?raw=true" width="700" alt="Detail window screenshot" />
</div> -->

### Coming soon

- Live workflow log streaming
- Advanced notification configuration
- Raspberry Pi client support (make your own external e-ink monitor)

### Feature requests

Have an idea for a new feature? Head over to the [Discussions](https://github.com/gwleuverink/relay/discussions) page to share your suggestions or inquire about upcoming features.

If you're considering submitting a pull request for a new feature, please reach out to [me](https://github.com/gwleuverink) first to discuss your ideas.

### Contributing

Clone the project and run the following commands from the project directory

```bash
# Prep the environment
# You'll need to add the GITHUB_CLIENT_ID in your .env file manually
composer native:setup

# Serve app
composer native:dev
```

Please make sure all checks pass before submitting a PR

```bash
composer format
composer analyze
composer test
```

<br />

## Supporting the Project

To distribute Relay on macOS, the application needs to be signed and notarized through the Apple Developer Program, which costs €100 annually. If you find Relay useful, please consider supporting its development and maintenance through [GitHub Sponsors](https://github.com/sponsors/gwleuverink). Your support helps cover these costs and enables continued development of this open-source tool.
